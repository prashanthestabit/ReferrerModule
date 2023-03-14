<?php

namespace Modules\ReferrerModule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_name',
        'referrer_email',
        'referred_name',
        'referred_email',
        'referral_code',
        'user_id',
        'status'
    ];

    protected static function newFactory()
    {
        return \Modules\ReferrerModule\Database\factories\ReferralFactory::new();
    }
}
