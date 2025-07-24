<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class member_detail extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'name',
        'gender',
        'joining_data',
        'phone',
        'email',
        'ref_by',
        'address',
        'profile_photo',
    ];

    protected $hidden = ['created_at', 'updated_at']; // Hide timestamps

    public function users()
    {
        return $this->belongsTo(users::class, 'gym_id');
    }

    public function gym_plan()
    {
        return $this->hasMany(gym_plan::class, 'user_id');
    }

    public function personal_trainer()
    {
        return $this->hasMany(personal_trainer::class, 'user_id');
    }

    public function member_payment()
    {
        return $this->hasMany(member_payment::class, 'user_id');
    }
}
