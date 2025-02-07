<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFinance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'daily_target',
        'monthly_target',
        'daily_printing_costs',
        'monthly_printing_costs'
    ];
}
