<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookMarketing extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'client_id',
        'agent_id',
        'client_name',
        'project_name',
        'package_chosen',
        'project_duration',
        'desired_results',
        'business_name',
        'business_email',
        'business_contact',
        'business_address',
        'business_location',
        'business_website_url',
        'business_working_hours',
        'where_is_your_book_published',
        'business_category',
        'facebook_link',
        'instagram_link',
        'instagram_password',
        'twitter_link',
        'twitter_password',
        'linkedin_link',
        'pinterest_link',
        'pinterest_password',
        'youtube_link',
        'youtube_gmail',
        'youtube_gmail_password',
        'youtube_gmail_password',
        'social_media_platforms',
        'target_locations',
        'target_audiences',
        'age_bracket',
        'keywords',
        'unique_selling_points',
        'exclude_information',
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
