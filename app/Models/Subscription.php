<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'email', 'verification_token', 'is_verified'];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function priceHistories()
{
    return $this->hasMany(PriceHistory::class);
}

}
