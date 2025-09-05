<?php
namespace App\Models;

use CodeIgniter\Model;

class CamerasModel extends Model
{
    protected $table         = 'cameras';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true; // created_at / updated_at

    protected $allowedFields = [
        'name',
        'ip',
        'username',
        'password',
        'path',
        'port',
        'transport',
        'mode',
        'fps',
        'audio',
        'max_days',
        'max_storage_mb',
        'status_online',
        'last_seen',
        'thumbnail_relpath',
        'notes',
    ];
}
