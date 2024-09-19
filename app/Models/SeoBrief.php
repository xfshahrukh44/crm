<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoBrief extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'client_id',
        'agent_id',
        'company_name',
        'website_url',
        'business_address',
        'industry_or_niche',
        'contact_person_name',
        'email',
        'contact_number',
        'primary_business_goals',
        'main_seo_objectives',
        'kpis',
        'primary_target_audience',
        'secondary_niche',
        'geographical_areas',
        'previously_done_seo',
        'top_three_competitors',
        'target_keywords',
        'target_keywords_2',
        'local_specific_or_geographical',
        'have_store',
        'ga_gsc_admin_access',
        'additional_comments'
    ];

    public function invoice(){
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }

    public function formfiles(){
        return $this->hasMany(FormFiles::class, 'logo_form_id', 'id')->where('form_code', 13);
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function client(){
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function project(){
        return $this->hasOne(Project::class, 'form_id', 'id')->where('form_checker', 13);
    }
}
