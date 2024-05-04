<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $primarykey = "id";
    protected $keyType = "int";
    protected $table = "contacts";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
    ];

    // Relasi Contact User
    // 1 contact dimiliki 1 user
    public function user(): BelongsTo
    {
        return $this->belongsTo(Contact::class, "user_id", "id");
    }

    // 1 contact memiliki N address
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, "contact_id", "id");
    }
}
