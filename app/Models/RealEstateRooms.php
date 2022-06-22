<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealEstateRooms extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'real_estate_id'
    ];

    public function estate()
    {
        return $this->belongsTo(RealEstate::class, 'real_estate_id');
    }
    public function images()
    {
        return $this->hasMany(RealEstateRoomImages::class, 'room_id');
    }
}
