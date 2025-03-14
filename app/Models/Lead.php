<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'user_id',
        'client_id',
        'name',
        'last_name',
        'email',
        'contact',
        'status',
        'service',
        'url',
        'subject',
        'message'
    ];

    public function _brand(){
        return $this->belongsTo(Brand::class,  'brand');
    }

    public function assigned_to(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function services($id = null){
        $id = !is_null($id) ? $id : $this->service;

        return Service::where('id', $id)->first();
    }
}
