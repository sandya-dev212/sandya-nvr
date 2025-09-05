<?php
namespace App\Models;
use CodeIgniter\Model;

class UsersModel extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username','email','type','password_hash','is_admin','status','last_login_at'];
}
