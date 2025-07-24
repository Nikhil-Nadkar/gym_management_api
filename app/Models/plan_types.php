<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class plan_types extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'plan_name',
    ];

    protected $hidden = ['created_at', 'updated_at']; // Hide timestamps

    public function users()
    {
        return $this->belongsTo(users::class, 'gym_id');
    }
}
