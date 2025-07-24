<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class member_payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'user_id',
        'total_amount',
        'paid_amount',
        'payment_status',
        'installment',
        'next_payment_amount',
        'next_payment_date',
    ];

    protected $hidden = ['created_at', 'updated_at']; //  Hide timestamps

    public function member_detail()
    {
        return $this->belongsTo(member_detail::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsTo(users::class, 'gym_id');
    }
}
