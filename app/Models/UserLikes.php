<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLikes extends Model
{
    use HasFactory;
    protected $fillable = [
        'estate_id', 'user_id',
    ];
    public function user()
    {
        return $this->hasOne(User::class, 'user_id');
    }
    public function estate()
    {
        return $this->hasOne(RealEstate::class, 'estate_id');
    }
}
