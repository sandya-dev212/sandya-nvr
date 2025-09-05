<?php
namespace App\Models;
use CodeIgniter\Model;

class RecordingsModel extends Model {
    protected $table = 'recordings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['camera_id','started_at','ended_at','file_relpath','size_bytes','duration_sec','fps','has_audio','status','error_msg'];
}

