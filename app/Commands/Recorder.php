<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\CamerasModel;
use App\Models\SettingsModel;
use App\Models\RecordingsModel;

class Recorder extends BaseCommand
{
    protected $group       = 'nvr';
    protected $name        = 'recorder:tick';
    protected $description = 'Start/maintain ffmpeg recorders for cameras (15-min segments, retention). Run via cron every 5 minutes.';

    public function run(array $params)
    {
        $camModel = new CamerasModel();
        $cams = $camModel->where('mode','recording')->findAll();

        if (!$cams) {
            CLI::write('No recording cameras.', 'yellow');
            return;
        }

        $videosRoot = videos_root(); // from helper
        $now = time();

        foreach ($cams as $c) {
            $name = $c['name'];
            $camDir = $videosRoot . '/' . $name;
            @mkdir($camDir, 0775, true);

            // PID file per camera
            $pidFile = $camDir.'/.rec.pid';

            // if process dead or not found => start it
            $needStart = true;
            if (is_file($pidFile)) {
                $pid = (int)trim(@file_get_contents($pidFile));
                if ($pid > 0 && file_exists("/proc/$pid")) {
                    $needStart = false;
                }
            }

            if ($needStart) {
                $this->startRecorder($c, $camDir, $pidFile);
            }

            // Retention
            $this->applyRetention($camDir, (int)($c['max_storage_mb'] ?? 0), (int)($c['max_days'] ?? 0));

            // Touch last_seen
            $camModel->update((int)$c['id'], ['last_seen'=>date('Y-m-d H:i:s')]);
        }
    }

    private function buildRtsp(array $c): string
    {
        $port = $c['port'] ? ':'.(int)$c['port'] : '';
        $path = $c['path'] ?: '/';
        if ($path[0] !== '/') $path = '/'.$path;
        return 'rtsp://' . $c['ip'] . $port . $path;
    }

    private function startRecorder(array $c, string $camDir, string $pidFile): void
    {
        $dateDir = $camDir . '/' . date('Y-m-d');
        @mkdir($dateDir, 0775, true);

        $rtsp = $this->buildRtsp($c);
        $trans = $c['transport'] === 'udp' ? 'udp' : 'tcp';
        $fps   = (int)($c['fps'] ?? 0);
        $hasAudio = ((int)$c['audio']) === 1;

        // file pattern pakai waktu mulai segment
        $pattern = $dateDir . '/%Y%m%d-%H%M%S.mp4';

        // ffmpeg args:
        // -f segment -segment_time 900 (15 menit)
        // -reset_timestamps 1 -movflags +faststart (langsung playable)
        // -r <fps> (kalau diisi)
        $cmd = [
            'ffmpeg',
            '-rtsp_transport', $trans,
            '-i', $rtsp,
        ];
        if ($fps > 0) { $cmd[]='-r'; $cmd[]=(string)$fps; }
        // video copy; audio optional
        $cmd = array_merge($cmd, ['-vcodec','copy']);
        if ($hasAudio) {
            $cmd = array_merge($cmd, ['-acodec','aac','-b:a','128k']);
        } else {
            $cmd[]='-an';
        }
        $cmd = array_merge($cmd, [
            '-f','segment',
            '-segment_time','900',
            '-reset_timestamps','1',
            '-movflags','+faststart',
            '-strftime','1',
            $pattern
        ]);

        // jalankan background + tulis pid
        $cmdStr = '';
        foreach ($cmd as $p) { $cmdStr .= escapeshellarg($p).' '; }
        // pakai setsid supaya tidak mati saat shell exit
        $full = "setsid $cmdStr >/dev/null 2>&1 & echo $!";
        $pid = (int)shell_exec($full);
        if ($pid > 0) {
            file_put_contents($pidFile, (string)$pid);
        }
    }

    private function applyRetention(string $camDir, int $maxMB, int $maxDays): void
    {
        // 1) Prioritas: max storage
        if ($maxMB > 0) {
            $limitBytes = $maxMB * 1024 * 1024;
            $files = $this->listFilesByMTimeAsc($camDir);
            $total = 0;
            foreach ($files as $f) $total += filesize($f);
            while ($total > $limitBytes && !empty($files)) {
                $old = array_shift($files);
                $sz  = @filesize($old) ?: 0;
                @unlink($old);
                $total -= $sz;
            }
        }

        // 2) Days
        if ($maxDays > 0) {
            $threshold = time() - ($maxDays * 86400);
            $this->deleteOlderThan($camDir, $threshold);
        }
    }

    private function listFilesByMTimeAsc(string $dir): array
    {
        $files = [];
        if (!is_dir($dir)) return $files;
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            /** @var \SplFileInfo $file */
            if (strtolower($file->getExtension())==='mp4') {
                $files[] = $file->getPathname();
            }
        }
        usort($files, fn($a,$b)=>filemtime($a) <=> filemtime($b));
        return $files;
    }

    private function deleteOlderThan(string $dir, int $ts): void
    {
        if (!is_dir($dir)) return;
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $f) {
            $path = $f->getPathname();
            if ($f->isFile() && strtolower($f->getExtension())==='mp4' && filemtime($path) < $ts) {
                @unlink($path);
            }
            // bersihkan folder tanggal kosong
            if ($f->isDir() && !glob($path.'/*')) { @rmdir($path); }
        }
    }
}
