<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientBillingInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'country',
        'city',
        'state',
        'address',
        'zip_code'
    ];

    public function invoice ()
    {
        return $this->belongsTo(Invoice::class);
    }
}
