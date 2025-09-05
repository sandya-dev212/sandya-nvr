<?php
namespace App\Models;
use CodeIgniter\Model;

class CamerasModel extends Model {
    protected $table = 'cameras';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name','rtsp_url','mode','fps','audio','transport','port','storage_path','max_days','max_storage_mb','status_online','last_seen','thumbnail_relpath','notes'];
}
