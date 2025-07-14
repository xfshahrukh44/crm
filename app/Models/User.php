<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'twitter_id',
        'is_employee',
        'last_name',
        'contact',
        'status',
        'block',
        'brand_id',
        'client_id',
        'image',
        'verfication_code',
        'verfication_datetime',
        'is_support_head',
        'alternate_email',
        'pseudo_name',
        'is_upsell',
        'restricted_brands',
        'restricted_brands_cutoff_date',
        'last_login_ip',
        'last_login_device',
        'merchant',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    public function addNew($input)
    {
        $check = static::where('twitter_id',$input['twitter_id'])->first();

        if(is_null($check)){
            return static::create($input);
        }

        return $check;
    }

    // public function brand(){
    //     return $this->hasOne(Brand::class, 'id', 'brand_id');
    // }

    public function category(){
        return $this->belongsToMany(Category::class, 'category_users');
    }

    public function category_list(){
        $brand_id = array();
        foreach ($this->category as $brands){
            array_push($brand_id, $brands->id);
        }
        return $brand_id;
    }

    public function brands(){
        return $this->belongsToMany(Brand::class, 'brand_users');
    }

    public function brand_list(){
        $brand_id = array();
        foreach ($this->brands as $brands){
            array_push($brand_id, $brands->id);
        }
        return $brand_id;
    }

    public function get_role(){
        if($this->is_employee == 0){
            echo "Front Agent";
        }else if($this->is_employee == 1){
            echo "Team Lead";
        }else if($this->is_employee == 2){
            echo "Admin";
        }else if($this->is_employee == 3){
            echo "Customer";
        }else if($this->is_employee == 4){
            if ($this->is_support_head) {
                echo "Support Head";
            } else {
                echo "Customer Support";
            }
        }else if($this->is_employee == 5){
            echo "Member";
        }else if($this->is_employee == 6){
            echo "Sales Manager";
//            echo "Member";
        }else if($this->is_employee == 9){
            echo "PPC";
//            echo "Member";
        }else if ($this->is_employee == 7){
            if ($this->is_support_head) {
                echo "QA Lead";
            } else {
                echo "QA Member";
            }
        } else {
            echo "";
        }
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'user_id', 'id');
    }

    public function client_messages()
    {
        return $this->hasMany(Message::class, 'client_id', 'id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id', 'id');
    }

    public function recent_projects()
    {
        //restricted brand access
        $restricted_brands = json_decode(auth()->user()->restricted_brands, true); // Ensure it's a
        return $this->hasMany(Project::class, 'client_id', 'id')->when(!empty($restricted_brands) && !is_null(auth()->user()->restricted_brands_cutoff_date) && auth()->user()->is_employee != 2, function ($q) use ($restricted_brands) {
            return $q->where(function ($query) use ($restricted_brands) {
                $query->whereNotIn('brand_id', $restricted_brands)
                    ->orWhere(function ($subQuery) use ($restricted_brands) {
                        $subQuery->whereIn('brand_id', $restricted_brands)
                            ->whereDate('created_at', '>=', Carbon::parse(auth()->user()->restricted_brands_cutoff_date)); // Replace with your date
                    });
            });
        })->orderBy('updated_at', 'DESC');
    }

    public function projects_count()
    {
        return $this->hasMany(Project::class, 'user_id', 'id')->where('seen', 0)->count();
    }

    public function projects_count_for_support_head()
    {
        return Project::whereIn('brand_id', $this->brand_list())->count();
    }

    public function client(){
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function logoForm(){
        return $this->hasMany(LogoForm::class, 'user_id', 'id');
    }

    public function proofreading(){
        return $this->hasMany(Proofreading::class, 'user_id', 'id');
    }

    public function bookcover(){
        return $this->hasMany(BookCover::class, 'user_id', 'id');
    }

    public function webForm(){
        return $this->hasMany(WebForm::class, 'user_id', 'id');
    }

    public function smmForm(){
        return $this->hasMany(SmmForm::class, 'user_id', 'id');
    }

    public function contentWritingForm(){
        return $this->hasMany(ContentWritingForm::class, 'user_id', 'id');
    }

    public function soeForm(){
        return $this->hasMany(SeoForm::class, 'user_id', 'id');
    }

    public function seoBrief(){
        return $this->hasMany(SeoBrief::class, 'user_id', 'id');
    }

    public function bookFormattingForm(){
        return $this->hasMany(BookFormatting::class, 'user_id', 'id');
    }

    public function bookWritingForm(){
        return $this->hasMany(BookWriting::class, 'user_id', 'id');
    }

    public function authorWesbiteForm(){
        return $this->hasMany(AuthorWebsite::class, 'user_id', 'id');
    }


    public function isbnForm(){
        return $this->hasMany(Isbnform::class, 'user_id', 'id');
    }


    public function bookPrintingForm(){
        return $this->hasMany(Bookprinting::class, 'user_id', 'id');
    }

    public function bookMarketings(){
        return $this->hasMany(BookMarketing::class, 'user_id', 'id');
    }

    public function newSMMs(){
        return $this->hasMany(NewSMM::class, 'user_id', 'id');
    }

    public function press_releases(){
        return $this->hasMany(PressReleaseForm::class, 'user_id', 'id');
    }



    public function getClient(){
        return $this->hasMany(Client::class, 'user_id', 'id')->select('id', 'name', 'last_name');
    }

    public function getBriefPendingCount(){
        $count = 0;
        $logoForms = Auth()->user()->logoForm;
        foreach($logoForms as $logoForm){
            if(($logoForm->logo_name == null) || ($logoForm->logo_name == '')){
                $count++;
            }
        }
        $webForms = Auth()->user()->webForm;
        foreach($webForms as $webForm){
            if(($webForm->business_name == null) || ($webForm->business_name == '')){
                $count++;
            }
        }
        $smmForms = Auth()->user()->smmForm;
        foreach($smmForms as $smmForm){
            if(($smmForm->business_name == null) || ($smmForm->business_name == '')){
                $count++;
            }
        }
        $contentWritingForms = Auth()->user()->contentWritingForm;
        foreach($contentWritingForms as $contentWritingForm){
            if(($contentWritingForm->company_name == null) || ($contentWritingForm->company_name == '')){
                $count++;
            }
        }
        $soeForms = Auth()->user()->soeForm;
        foreach($soeForms as $soeForm){
            if(($soeForm->company_name == null) || ($soeForm->company_name == '')){
                $count++;
            }
        }

        $bookFormattingForms = Auth()->user()->bookFormattingForm;
        foreach($bookFormattingForms as $bookFormattingForm){
            if(($bookFormattingForm->book_title == null) || ($bookFormattingForm->book_title == '')){
                $count++;
            }
        }

        $bookWritingForms = Auth()->user()->bookWritingForm;
        foreach($bookWritingForms as $bookWritingForm){
            if(($bookWritingForm->book_title == null) || ($bookWritingForm->book_title == '')){
                $count++;
            }
        }

        $proofreadings = Auth()->user()->proofreading;
        foreach($proofreadings as $proofreading){
            if(($proofreading->description == null) || ($proofreading->description == '')){
                $count++;
            }
        }

        $bookcovers = Auth()->user()->bookcover;
        foreach($bookcovers as $bookcover){
            if(($bookcover->title == null) || ($bookcover->title == '')){
                $count++;
            }
        }


        $isbnforms = Auth()->user()->isbnForm;
        foreach($isbnforms as $isbnform){
            if(($isbnform->title == null) || ($isbnform->title == '')){
                $count++;
            }
        }


        $bookprintings = Auth()->user()->bookPrintingForm;
        foreach($bookprintings as $bookprinting){
            if(($bookprinting->title == null) || ($bookprinting->title == '')){
                $count++;
            }
        }



       return $count;
    }

    public function lastmessage(){
        return $this->hasOne(Message::class, 'client_id', 'id')->orderBy('id', 'desc');
    }

    public function totalAssignTask(){
        return $this->hasMany(ProductionMemberAssign::class, 'assigned_to', 'id')->count();
    }

    public function memberOpen(){
        return $this->hasMany(ProductionMemberAssign::class, 'assigned_to', 'id')->where('status', 0)->count();
    }

    public function memberReOpen(){
        return $this->hasMany(ProductionMemberAssign::class, 'assigned_to', 'id')->where('status', 1)->count();
    }

    public function memberInProgress(){
        return $this->hasMany(ProductionMemberAssign::class, 'assigned_to', 'id')->where('status', 4)->count();
    }

    public function memberSentforApproval(){
        return $this->hasMany(ProductionMemberAssign::class, 'assigned_to', 'id')->where('status', 5)->count();
    }

    public function memberIncompleteBrief(){
        return $this->hasMany(ProductionMemberAssign::class, 'assigned_to', 'id')->where('status', 6)->count();
    }

    public function memberCompleted(){
        return $this->hasMany(ProductionMemberAssign::class, 'assigned_to', 'id')->where('status', 3)->count();
    }

    public function memberOnHold(){
        return $this->hasMany(ProductionMemberAssign::class, 'assigned_to', 'id')->where('status', 2)->count();
    }

    public function status_logs ()
    {
        return $this->hasMany(TaskStatusChangedLog::class);
    }

    public function qa_feedbacks ()
    {
        return $this->hasMany(QaFeedback::class);
    }

    public function invoices(){
        return $this->hasMany(Invoice::class, 'sales_agent_id', 'id');
    }

    public function paid_invoices(){
        return $this->hasMany(Invoice::class, 'sales_agent_id', 'id')->where('payment_status', 2);
    }

    public function finances ()
    {
        return $this->hasOne(UserFinance::class, 'user_id');
    }
}
