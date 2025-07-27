<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class visitor extends Model
{

    use HasFactory;

    protected $fillable = [
        'gym_id',
        'visitorName',
        'visitorPhone',
    ];

    protected $hidden = ['created_at', 'updated_at']; //  Hide timestamps

    public function users()
    {
        return $this->belongsTo(users::class, 'gym_id');
    }
}
