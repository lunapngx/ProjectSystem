<?php

// app/Models/UserModel.php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $allowedFields = ['email','password_hash'];
    protected $useTimestamps = true;
}
