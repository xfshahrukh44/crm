<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PressReleaseForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'client_id',
        'agent_id',
        'book_title',
        'author_name',
        'genre',
        'publisher',
        'publication_date',
        'isbn',
        'synopsis',
        'unique_selling_points',
        'target_audience',
        'short_biography',
        'previous_works',
        'award_recognition',
        'quote_excerpts',
        'reviews',
        'tie_ins',
        'formats_and_availability',
        'price',
        'events',
        'media_kit',
        'press_contact',
        'twitter',
        'tiktok',
        'facebook',
        'instagram',
        'publishers_contact',
        'cta',
        'book_cover_image',
        'author_photo',
        'key_highlights'
    ];

    public function invoice(){
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }

    public function formfiles(){
        return $this->hasMany(FormFiles::class, 'logo_form_id', 'id')->where('form_code', 16);
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function client(){
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function project(){
        return $this->hasOne(Project::class, 'form_id', 'id')->where('form_checker', 16);
    }
}
