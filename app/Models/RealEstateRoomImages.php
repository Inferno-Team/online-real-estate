<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealEstateRoomImages extends Model
{
    use HasFactory;
    protected $fillable = [
        'img_url', 'room_id'
    ];

    public function room()
    {
        return $this->belongsTo(RealEstateRooms::class, 'room_id');
    }
}
