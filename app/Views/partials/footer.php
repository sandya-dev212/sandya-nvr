<?php
$s = $stats ?? sys_stats();
$ram = ($s['ram_total']>0) ? (100*$s['ram_used']/$s['ram_total']) : 0;
$disk = ($s['disk_total']>0) ? (100*($s['disk_total']-$s['disk_free'])/$s['disk_total']) : 0;
?>
<div>
  <span>CPU load: <?= number_format($s['cpu_load_1'],2) ?></span> |
  <span>RAM: <?= format_bytes($s['ram_used']) ?>/<?= format_bytes($s['ram_total']) ?> (<?= number_format($ram,1) ?>%)</span> |
  <span>Disk(<?= esc($s['videos_root']) ?>): <?= format_bytes($s['disk_total']-$s['disk_free']) ?>/<?= format_bytes($s['disk_total']) ?> (<?= number_format($disk,1) ?>%)</span> |
  <span>IP: <?= esc($s['client_ip']) ?></span>
</div>
