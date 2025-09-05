<?php
namespace App\Models;
use CodeIgniter\Model;

class SettingsModel extends Model {
    protected $table = 'settings';
    protected $primaryKey = 'key';
    protected $allowedFields = ['key','value'];
    protected $useAutoIncrement = false;
}
