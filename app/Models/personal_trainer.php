<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class personal_trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'user_id',
        'pt_name',
        'period',
        'start_date',
        'end_date',
        'price',
    ];

    protected $hidden = ['created_at', 'updated_at']; // Hide timestamps

    public function member_detail()
    {
        return $this->belongsTo(member_detail::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsTo(users::class, 'gym_id');
    }
}
