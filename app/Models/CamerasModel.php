<?php
namespace App\Models;

use CodeIgniter\Model;

class CamerasModel extends Model
{
    protected $table         = 'cameras';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'ip',            // NEW
        'path',          // NEW (e.g. /, /ch0/main, /index, /cam/..)
        'port',          // int|null
        'transport',     // tcp|udp
        'mode',          // recording|watch|disabled
        'fps',           // int|null
        'audio',         // 0|1
        'max_days',      // int|null
        'max_storage_mb',// int|null
        'status_online',
        'last_seen',
        'thumbnail_relpath',
        'notes'
    ];
}
