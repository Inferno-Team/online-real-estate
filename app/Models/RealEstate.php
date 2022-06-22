<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealEstate extends Model
{
    use HasFactory;

    protected $fillable = [
        'lng', 'lat', 'type', 'user_id',
        'location', 'rate', 'buy_type',
        'budget', 'img_url', 'img360_url',
        'area','direction'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function rooms(){
        return $this->hasMany(RealEstateRooms::class,'real_estate_id');
    }
}
