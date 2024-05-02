<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $table = "users";
    protected $primarykey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'username',
        'password',
        'name'
    ];

    // membuat relasi user dengan contact
    // 1 user memiliki N contact
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, "user_id", "id");
    }
}
