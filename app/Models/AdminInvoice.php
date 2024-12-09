<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'sales_agent_id',
        'transfer_by_id',
        'brand_id',
        'merchant_id',
        'currency',
        'client_name',
        'client_email',
        'client_phone',
        'service',
        'amount',
        'recurring',
        'sale_upsell',
        'department',
        'type',
        'payment_id',
        'invoice_number',
        'refund_cb',
        'refund_cb_date'
    ];

    public function sale(){
        return $this->hasOne(User::class, 'id', 'sales_agent_id');
    }

    public function transfer(){
        return $this->hasOne(User::class, 'id', 'transfer_by_id');
    }

    public function client(){
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function brands(){
        return $this->hasOne(Brand::class, 'id', 'brand');
    }

    public function currency_show(){
        return $this->hasOne(Currency::class, 'id', 'currency');
    }

    public function _currency(){
        return $this->hasOne(Currency::class, 'id', 'currency');
    }

    public function payment_type_show(){
        if($this->payment_type == 0){
            return "One-Time Charge";
        }else{
            return "Three-Time Charge";
        }
    }

    public function services($id = null){
        $id = !is_null($id) ? $id : $this->service;

        return Service::where('id', $id)->first();
    }

    public function merchant(){
        return $this->hasOne(Merchant::class, 'id', 'merchant_id');
    }
}
