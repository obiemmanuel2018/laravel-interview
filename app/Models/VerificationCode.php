<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'user_name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password'
    ];
}
