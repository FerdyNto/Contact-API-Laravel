<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "users";
    protected $primarykey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;
}
