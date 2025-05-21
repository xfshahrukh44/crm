<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'last_name', 'email', 'contact', 'user_id', 'status', 'brand_id', 'assign_id', 'stripe_customer_id', 'priority', 'show_service_forms', 'comments', 'comments_id', 'comments_timestamp'];

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

    public function restricted_invoices(){
        $restricted_brands = json_decode(auth()->user()->restricted_brands, true); // Ensure it's an array
        return $this->hasMany(Invoice::class)->when(!empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date) && auth()->user()->is_employee != 2, function ($q) use ($restricted_brands) {
            return $q->where(function ($query) use ($restricted_brands) {
                $query->whereNotIn('brand', $restricted_brands)
                    ->orWhere(function ($subQuery) use ($restricted_brands) {
                        $subQuery->whereIn('brand', $restricted_brands)
                            ->whereDate('created_at', '>=', \Carbon\Carbon::parse(auth()->user()->restricted_brands_cutoff_date)); // Replace with your date
                    });
            });
        });
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

    public function commenter ()
    {
        return $this->hasOne(User::class, 'id', 'comments_id');
    }

    public function priority_badge ($small = false)
    {
        $badge_map = [
            1 => 'danger',
            2 => 'warning',
            3 => 'info',
        ];

        $badge_map_2 = [
            1 => 'HIGH',
            2 => 'MEDIUM',
            3 => 'LOW',
        ];

        return '<span class="span_client_priority_badge badge badge-'.$badge_map[$this->priority] . ($small ? ' badge-sm' : ''). '">' .$badge_map_2[$this->priority].'</span>';
    }

}
