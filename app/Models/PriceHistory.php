<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    use HasFactory;

    protected $fillable = ['subscription_id', 'price', 'checked_at'];
    public function subscription()
{
    return $this->belongsTo(Subscription::class);
}

}
