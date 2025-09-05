<?php
// app/Helpers/nvr_helper.php
use Config\Services;

function videos_root(): string {
    $env = getenv('nvr.videosRoot');
    if ($env) return rtrim($env, '/');
    $db = \Config\Database::connect();
    $row = $db->table('settings')->where('key','videos_root')->get()->getRow();
    return $row ? rtrim($row->value, '/') : '/BDC-NFS-VIDEO/BDC-NVR-DEV/videos';
}

function client_ip(): string {
    $req = Services::request();
    $xff = $req->getHeaderLine('X-Forwarded-For');
    if ($xff) return trim(explode(',', $xff)[0]);
    return $req->getIPAddress();
}

function format_bytes(int $bytes): string {
    $u=['B','KB','MB','GB','TB'];
    for($i=0;$bytes>=1024 && $i<count($u)-1;$i++) $bytes/=1024;
    return sprintf("%.2f %s",$bytes,$u[$i]);
}

function sys_stats(): array {
    // Simple stats untuk footer (CPU/RAM/DISK)
    $load  = sys_getloadavg();
    $memInfo = @file_get_contents('/proc/meminfo') ?: '';
    preg_match('/MemTotal:\s+(\d+)/',$memInfo,$m1);
    preg_match('/MemAvailable:\s+(\d+)/',$memInfo,$m2);
    $total = isset($m1[1]) ? (int)$m1[1]*1024 : 0;
    $avail = isset($m2[1]) ? (int)$m2[1]*1024 : 0;
    $used  = max(0,$total-$avail);
    $vr = videos_root();
    $df = @disk_free_space($vr);
    $dt = @disk_total_space($vr);
    return [
        'cpu_load_1' => $load[0] ?? 0,
        'ram_used'   => $used,
        'ram_total'  => $total,
        'disk_free'  => $df ?: 0,
        'disk_total' => $dt ?: 0,
        'videos_root'=> $vr,
        'client_ip'  => client_ip(),
    ];
}
