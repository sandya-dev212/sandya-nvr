<?php
namespace App\Models;

use CodeIgniter\Model;

class CamerasModel extends Model
{
    protected $table            = 'cameras';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'name','ip','username','password','path','port','transport','mode',
        'fps','audio','max_days','max_storage_mb','notes',
        'status_online','last_seen','thumbnail_relpath',
        'created_at','updated_at',
    ];
    protected $protectFields    = true;

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules = [
        'name'      => 'required|min_length[2]|max_length[120]',
        'ip'        => 'required|valid_ip',
        'path'      => 'permit_empty|max_length[255]',
        'port'      => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[65535]',
        'transport' => 'required|in_list[tcp,udp]',
        'mode'      => 'required|in_list[recording,watch,disabled]',
        'fps'       => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[60]',
        'audio'     => 'required|in_list[0,1]',
        'max_days'  => 'permit_empty|integer|greater_than_equal_to[0]',
        'max_storage_mb' => 'permit_empty|integer|greater_than_equal_to[0]',
    ];
}
