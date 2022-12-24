<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'website',
        'description',
        'profile_id'
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class , 'profile_id');
    }
}
