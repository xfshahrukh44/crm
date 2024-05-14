<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'last_name', 'email', 'contact', 'user_id', 'status', 'brand_id', 'assign_id'];

    public function brand(){
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'client_id', 'id');
    }

    public function added_by(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }


    public function agent(){
        return $this->hasOne(User::class, 'id', 'assign_id');
    }

    public function invoice(){
        return $this->hasMany(Invoice::class);
    }

    public function invoices(){
        return $this->hasMany(Invoice::class);
    }

    public function invoice_paid(){
        return $this->hasMany(Invoice::class)->where('payment_status', 2)->sum('amount');
    }

    public function invoice_unpaid(){
        return $this->hasMany(Invoice::class)->where('payment_status', 1)->sum('amount');
    }

    public function projects(){
        return $this->hasMany(Project::class, 'client_id');
    }

    public function _projects(){
        return $this->hasMany(Project::class, 'client_id', 'user_id');
    }

}
