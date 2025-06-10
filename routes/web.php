<?php

use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\AdminLeadController;
use App\Http\Controllers\BillingClientController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ClientInvoiceController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\Manager\ManagerUserController;
use App\Http\Controllers\ManagerAdminInvoiceController;
use App\Http\Controllers\ManagerLeadController;
use App\Http\Controllers\QAController;
use App\Http\Controllers\SaleLeadController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SupportClientController;
use App\Http\Controllers\SupportInvoiceController;
use App\Http\Livewire\BrandDashboard;
use App\Http\Livewire\ProductionDashboard;
use App\Http\Livewire\Revenue;
use App\Http\Livewire\Tutorials;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClientChatController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\BrandController as GeneralBrandController;
use App\Http\Controllers\Admin\AdminClientController;
use App\Http\Controllers\Admin\AdminProjectController;
use App\Http\Controllers\Admin\AdminTaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubTaskController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\LogoFormController;
use App\Http\Controllers\VerifyController;
use App\Http\Controllers\WebFormController;
use App\Http\Controllers\SmmFormController;
use App\Http\Controllers\ContentWritingFormController;
use App\Http\Controllers\SeoFormController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\BookFormattingController;
use App\Http\Controllers\BookWritingController;
use App\Http\Controllers\AuthorWebsiteController;
use App\Http\Controllers\ProofreadingController;
use App\Http\Controllers\BookCoverController;
use App\Http\Controllers\IsbnController;
use App\Http\Controllers\BookprintingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
0 for seller, 1 for production, 2 for admin
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Client Links

Route::any('pay-now/{id}', [InvoiceController::class, 'payNow'])->name('client.paynow');
Route::post('/payment', [InvoiceController::class, 'paymentProcess'])->name('client.payment');
Route::any('thank-you/{id}', [InvoiceController::class, 'thankYou'])->name('thankYou');
Route::any('failed/{id}', [InvoiceController::class, 'failed'])->name('failed');

Route::group(['middleware' => 'auth'], function () {
    Route::post('/keep-alive', function () {
        if(auth()->user()->status == 0){
            return response()->json(['ok' => false]);
        }else{
            return response()->json(['ok' => true]);
        }
    });
});
Route::get('/', function () {
    return redirect()->route('login');
})->middleware('auth');

//auth()->routes(['register' => false]);
Auth::routes(['register' => false]);
Route::get('/send-notification/{task_id}/{role}', [TaskController::class, 'sendTaskNotification']);

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'is_client'], function(){
        Route::get('client/home', [ClientController::class, 'clientTaskshow'])->name('client.home');
        Route::get('client/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
        Route::get('client/profile', [ClientController::class, 'profile'])->name('client.profile');
        Route::post('client/profile', [ClientController::class, 'updateProfile'])->name('client.update.profile');
        Route::post('client/update-profile-picture', [ClientController::class, 'updateProfilePicture'])->name('client.update.profile.picture');
        Route::get('client/chat', [ClientChatController::class, 'clientChat'])->name('client.chat');
        Route::get('client/messages', [ClientController::class, 'clientTaskshow'])->name('client.fetch.messages');
        Route::post('client/messages', [ClientChatController::class, 'sendMessage'])->name('client.send.messages');
        Route::get('client/logo/{id}', [LogoFormController::class, 'index'])->name('client.logo.form');
        Route::get('client/web/{id}', [WebFormController::class, 'index'])->name('client.web.form');
        Route::get('client/smm/{id}', [SmmFormController::class, 'index'])->name('client.smm.form');
        Route::get('client/content/{id}', [ContentWritingFormController::class, 'index'])->name('client.content.form');
        Route::get('client/seo/{id}', [SeoFormController::class, 'index'])->name('client.seo.form');
        Route::post('client/logo/{id}', [LogoFormController::class, 'update'])->name('client.logo.form.update');
        Route::post('client/web/{id}', [WebFormController::class, 'update'])->name('client.web.form.update');
        Route::post('client/smm/{id}', [SmmFormController::class, 'update'])->name('client.smm.form.update');
        Route::post('client/content/{id}', [ContentWritingFormController::class, 'update'])->name('client.content.form.update');
        Route::post('client/seo/{id}', [SeoFormController::class, 'update'])->name('client.seo.form.update');
        Route::post('client/seo-brief/{id}', [SeoFormController::class, 'updateBrief'])->name('client.seo-brief.form.update');
        Route::post('client/book-marketing/{id}', [SeoFormController::class, 'updateBookMarketing'])->name('client.book-marketing.form.update');
        Route::post('client/new-smm/{id}', [SeoFormController::class, 'updateNewSMM'])->name('client.new-smm.form.update');
        Route::post('client/press-release/{id}', [SeoFormController::class, 'updatePressRelease'])->name('client.press-release.form.update');
        Route::post('client/bookformatting/{id}', [BookFormattingController::class, 'update'])->name('client.bookformatting.form.update');
        Route::post('client/bookwriting/{id}', [BookWritingController::class, 'update'])->name('client.bookwriting.form.update');
        Route::post('client/authorwebsite/{id}', [AuthorWebsiteController::class, 'update'])->name('client.authorwebsite.form.update');
        Route::post('client/proofreading/{id}', [ProofreadingController::class, 'update'])->name('client.proofreading.form.update');
        Route::post('client/bookcover/{id}', [BookCoverController::class, 'update'])->name('client.bookcover.form.update');

        Route::post('client/isbn/{id}', [IsbnController::class, 'update'])->name('client.isbn.form.update');
        Route::post('client/bookprinting/{id}', [BookprintingController::class, 'update'])->name('client.bookprinting.form.update');

        Route::post('client/logo', [LogoFormController::class, 'destroy'])->name('client.logo.form.file.delete');
        Route::get('client/projects', [ClientController::class, 'clientProject'])->name('client.project');
        Route::get('client/projects/view/{id}', [ClientController::class, 'clientProjectView'])->name('client.project.view');
        Route::get('client/task/show/{id}/{notify?}', [ClientController::class, 'clientTaskshow'])->name('client.task.show');
        Route::post('client/message/', [SupportController::class, 'sendMessageClient'])->name('client.message.send');
        Route::get('client/brief', [ClientController::class, 'getClientBrief'])->name('client.brief');

        //invoices
        Route::get('/client/invoice', [ClientInvoiceController::class, 'getInvoiceByUserId'])->name('client.invoice');
        Route::get('/client/invoice-detail/{id}', [ClientInvoiceController::class, 'invoiceDetail'])->name('client.invoice-detail');
        Route::get('/client/pay-with-authorize/{id}', [ClientInvoiceController::class, 'payWithAuthorize'])->name('client.pay.with.authorize')->withoutMiddleware(['auth', 'is_client']);
//        Route::get('/client/pay-with-authorize-2/{id}', [ClientInvoiceController::class, 'payWithAuthorize2'])->name('client.pay.with.authorize.2')->withoutMiddleware(['auth', 'is_client']);
        Route::post('/client/pay-with-authorize-submit/{id}', [ClientInvoiceController::class, 'payWithAuthorizeSubmit'])->name('client.pay.with.authorize.submit')->withoutMiddleware(['auth', 'is_client']);
        Route::get('/client/confirm-authorize-payment/{id}', [ClientInvoiceController::class, 'confirmAuthorizePayment'])->name('confirm.authorize.payment')->withoutMiddleware(['auth', 'is_client']);
    });
});
Route::group(['middleware' => 'auth'], function () {
    Route::post('verify/code',  [AdminController::class, 'verifyCode'])->name('verify.code');
});
// Admin Routes
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'admin',  'middleware' => 'is_admin'], function(){
        Route::get('home', [AdminController::class, 'dashboard'])->name('admin.home');
        Route::get('edit-profile', [AdminController::class, 'editProfile'])->name('admin.edit.profile');
        Route::patch('update-profile/{id}', [AdminController::class, 'updateProfile'])->name('admin.update.profile');
        Route::get('change-password', [AdminController::class, 'changePassword'])->name('admin.change.password');
        Route::post('change-password', [AdminController::class, 'updatePassword'])->name('admin.update.password');
        Route::get('user/production/create', [AdminUserController::class, 'getProductionUser'])->name('admin.user.production.create');
        Route::get('user/production', [AdminUserController::class, 'getUserProduction'])->name('admin.user.production');
        Route::post('user/status', [AdminUserController::class, 'updateStatus'])->name('update.user.status');
        Route::post('user/sale/password', [AdminUserController::class, 'updateSalePassword'])->name('update.user.update.password')->withoutMiddleware('is_admin');

        //view forms
        Route::get('admin/{form_id}/projects/{check}/form/{id}', [SupportController::class, 'getFormAdmin'])->name('admin.form');

        Route::get('user/sales', [AdminUserController::class, 'getUserSale'])->name('admin.user.sales');
        Route::post('user/sales', [AdminUserController::class, 'storeUserSale'])->name('admin.user.sales.store');
        Route::post('user/production', [AdminUserController::class, 'storeUserSale'])->name('admin.user.production.store');
        Route::post('user/sales/password', [AdminUserController::class, 'passwordUserSale'])->name('admin.user.password');
        Route::get('user/create', [AdminUserController::class, 'createUserSale'])->name('admin.user.sales.create');
        Route::get('user/sale/edit/{id}', [AdminUserController::class, 'editUserSale'])->name('admin.user.sales.edit');
        Route::get('user/production/edit/{id}', [AdminUserController::class, 'editUserProduction'])->name('admin.user.production.edit');
        Route::post('user/sale/update/{id}', [AdminUserController::class, 'updateUserSale'])->name('admin.user.sales.update');

        //QA
        Route::get('user/qa', [AdminUserController::class, 'getUserQA'])->name('admin.user.qa');
        Route::get('user/qa/create', [AdminUserController::class, 'createUserQA'])->name('admin.user.qa.create');
        Route::post('user/qa', [AdminUserController::class, 'storeUserQA'])->name('admin.user.qa.store');
        Route::get('user/qa/edit/{id}', [AdminUserController::class, 'editUserQA'])->name('admin.user.qa.edit');
        Route::post('user/qa/update/{id}', [AdminUserController::class, 'updateUserQA'])->name('admin.user.qa.update');

        Route::resource('category', CategoryController::class);
        Route::resource('brand', BrandController::class);
        Route::resource('service', ServiceController::class);
        Route::resource('package', PackageController::class);
        Route::resource('currency', CurrencyController::class);

        Route::get('/service-list/{id}', [HomeController::class, 'serviceList'])->name('admin.service.list');
        Route::resource('client', AdminClientController::class, ['names' => 'admin.client']);
        Route::resource('merchant', MerchantController::class, ['names' => 'admin.merchant']);
        Route::post('client/create_auth/', [AdminClientController::class, 'createAuth'])->name('admin.client.createauth');
        Route::post('client/update_auth/', [AdminClientController::class, 'updateAuth'])->name('admin.client.updateauth');
        Route::get('client/agent/{brand_id?}', [AdminClientController::class, 'getAgent'])->name('admin.client.agent');
        Route::post('assign/support/', [AdminClientController::class, 'assignSupport'])->name('admin.assign.support');
        Route::post('client/agent/update', [AdminClientController::class, 'updateAgent'])->name('admin.client.update.agent');
        Route::resource('project', AdminProjectController::class, ['names' => 'admin.project']);
        Route::resource('task', AdminTaskController::class, ['names' => 'admin.task']);
        Route::post('admin/subtask/store', [AdminTaskController::class, 'adminSubtaskStore'])->name('admin.subtask.store');
        Route::get('client/{client}/{id}', [AdminClientController::class, 'showNotification'])->name('admin.client.shownotification');
        Route::get('create-invoice/{id}', [InvoiceController::class, 'index'])->name('admin.invoice.index');
        Route::post('invoice', [InvoiceController::class, 'store'])->name('admin.invoice.create');
        Route::any('invoice/generated/{id}', [InvoiceController::class, 'linkPage'])->name('admin.link');
        Route::get('invoice', [InvoiceController::class, 'invoiceAll'])->name('admin.invoice');
        Route::get('invoice/{id}', [InvoiceController::class, 'getInvoice'])->name('admin.single.invoice');
        Route::get('brief-pending', [LogoFormController::class, 'getBriefPending'])->name('admin.brief.pending');
        Route::get('pending/projects', [LogoFormController::class, 'getPendingProject'])->name('admin.pending.project');
        Route::get('pending/projects/{id}/{form}', [LogoFormController::class, 'getPendingProjectbyId'])->name('admin.pending.project.details');
        Route::post('invoice/paid/{id}', [InvoiceController::class, 'invoicePaidById'])->name('admin.invoice.paid');
        Route::get('/admin/invoice/refund', [InvoiceController::class, 'adminRefundCB'])->name('admin.refund.cb');
        Route::post('/admin/invoice/refund', [InvoiceController::class, 'adminRefundCBSubmit'])->name('admin.refund.cb.submit');
        Route::get('/admin/invoice/sheet', [InvoiceController::class, 'adminSalesSheet'])->name('admin.sales.sheet');

        Route::get('message/edit/{id}', [SupportController::class, 'editMessageByAdminClientId'])->name('admin.message.edit');
        Route::post('message/update', [SupportController::class, 'updateAdminMessage'])->name('admin.message.update');
        Route::get('message', [SupportController::class, 'getMessageByAdmin'])->name('admin.message');
        Route::get('message/{id}/{name}/show', [SupportController::class, 'getMessageByAdminClientId'])->name('admin.message.show');

        //admin invoices
        Route::get('admin-invoices', [AdminInvoiceController::class, 'index'])->name('admin.admin-invoice.index');
        Route::get('admin-invoices/create', [AdminInvoiceController::class, 'create'])->name('admin.admin-invoice.create');
        Route::post('admin-invoices/create', [AdminInvoiceController::class, 'store'])->name('admin.admin-invoice.store');
        Route::post('admin-invoices/import', [AdminInvoiceController::class, 'import'])->name('admin.admin-invoice.import');

        //leads
        Route::resource('admin/lead', AdminLeadController::class, ['names' => 'admin.lead']);

        Route::get('login-bypass', function () {
            session()->put('coming-from-admin', true);

            return login_bypass($_GET['email']);
        })->name('admin.login_bypass');

        Route::get('back-to-admin', function () {
            if (!session()->has('coming-from-admin')) {
                auth()->logout();

                return redirect()->route('login');
            }

            $admin = User::where('is_employee', 2)->first();

            session()->remove('coming-from-admin');
            return login_bypass($admin->email);
        })->name('admin.back_to_admin')->withoutMiddleware('is_admin');
    });
});

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'is_support'], function(){
        Route::get('support/change-password', [SupportController::class, 'changePassword'])->name('support.change.password');
        Route::post('support/change-password', [SupportController::class, 'updatePassword'])->name('support.update.password');
        Route::get('support/home', [SupportController::class, 'index'])->name('support.home');
        Route::get('support/projects', [SupportController::class, 'projects'])->name('support.project');
        // Route::get('support/all-projects', [SupportController::class, 'allProjects'])->name('support.all-projects');
        Route::get('support/message/', [SupportController::class, 'getMessageBySupport'])->name('support.message.get.by.support');
        Route::get('support/{form_id}/projects/{check}/form/{id}', [SupportController::class, 'getForm'])->name('support.form');
        Route::get('support/message/{id}/show', [SupportController::class, 'showMessage'])->name('support.message.show');
        Route::post('support/message/', [SupportController::class, 'sendMessage'])->name('support.message.send');
        Route::get('create/task/{id}/{name}/{notify?}', [TaskController::class, 'createTaskByProjectId'])->name('create.task.by.project.id');
        Route::get('support/edit/task/{id}', [TaskController::class, 'editTaskById'])->name('edit.task.by.id');
        Route::post('support/task/store', [TaskController::class, 'storeTaskBySupport'])->name('store.task.by.support');
        Route::post('support/task/update/{id}', [TaskController::class, 'updateTaskBySupport'])->name('update.task.by.support');
        Route::post('support/task/notes', [TaskController::class, 'storeoNotesBySupport'])->name('store.notes.by.support');
        Route::get('support/task/list', [TaskController::class, 'supportTaskList'])->name('support.task');
        Route::get('support/task/show/{id}/{notify?}', [TaskController::class, 'supportTaskShow'])->name('support.task.show');
        Route::post('support/subtask/store', [TaskController::class, 'supportTaskStore'])->name('support.subtask.store');
        Route::post('support/files/{id}', [TaskController::class, 'insertFiles'])->name('support.insert.sale.files');
        Route::post('support/files/show/client', [TaskController::class, 'showFilesToClient'])->name('support.client.file.show');
        Route::get('support/message/{id}/{name}/show', [SupportController::class, 'getMessageBySupportClientId'])->name('support.message.show.id');
        Route::get('/support/message/edit/{id}', [SupportController::class, 'editMessageBySupportClientId'])->name('support.message.edit');
        Route::post('/support/message/update', [SupportController::class, 'updateSupportMessage'])->name('support.message.update');


        Route::get('/support/pending/projects', [SupportController::class, 'getPendingProjectManager'])->name('support.pending.project');
        Route::get('/support/pending/projects/{id}/{form}', [SupportController::class, 'getPendingProjectbyIdManager'])->name('support.pending.project.details');
        Route::post('/support/assign/support/', [SupportController::class, 'assignSupportManager'])->name('support.assign.support');
        Route::post('/support/reassign/support/', [SupportController::class, 'reassignSupportManager'])->name('support.reassign.support')->withoutMiddleware('is_support');
        Route::get('/support/client/agent/{brand_id?}', [SupportController::class, 'getAgentManager'])->name('support.client.agent')->withoutMiddleware('is_support');

        //update task status
        Route::post('/support/updatetask/{id}', [TaskController::class, 'supportUpdateTask'])->name('support.update.task');

        //client
        Route::post('/support/client/create_auth/', [AdminClientController::class, 'createAuthSupport'])->name('support.client.createauth');
        Route::post('/support/client/update_auth/', [AdminClientController::class, 'updateAuthSupport'])->name('support.client.updateauth');
        Route::resource('/support/client', SupportClientController::class, ['names' => 'support.client']);
        Route::get('/support/payment-link/{id}', [SupportClientController::class, 'paymentLink'])->name('support.client.generate.payment');

        //invoice
        Route::post('/support/invoice', [SupportInvoiceController::class, 'saleStore'])->name('support.invoice.create');
        Route::post('/support/invoice/update', [SupportInvoiceController::class, 'saleUpdate'])->name('support.invoice.update');
        Route::any('/support/invoice/generated/{id}', [SupportInvoiceController::class, 'linkPageSale'])->name('support.link');
        Route::get('/support/invoice', [SupportInvoiceController::class, 'getInvoiceByUserId'])->name('support.invoice');
        Route::get('/support/invoice/{id}', [SupportInvoiceController::class, 'getSingleInvoice'])->name('support.single.invoice');
        Route::get('/support/invoice/edit/{id}', [SupportInvoiceController::class, 'editInvoice'])->name('support.invoice.edit');
        Route::post('/support/invoice/paid/{id}', [SupportInvoiceController::class, 'invoicePaidByIdSale'])->name('support.invoice.paid');

        //brief pending
        Route::get('/support/brief/pending', [SupportClientController::class, 'getBriefPendingById'])->name('support.brief.pending');

        //notifications
        Route::get('/support/my-notifications', function () { return view('support.my-notifications'); })->name('support.my-notifications');
    });
});

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'is_sale'], function(){
        Route::get('/projects', [HomeController::class, 'getProjectBySale'])->name('sale.project');
        Route::get('task/show/{id}', [TaskController::class, 'saleTaskShow'])->name('sale.task.show');
        Route::get('/home', [HomeController::class, 'index'])->name('sale.home');
        Route::get('/edit-profile', [HomeController::class, 'editProfile'])->name('sale.edit.profile');
        Route::patch('/update-profile/{id}', [HomeController::class, 'updateProfile'])->name('sale.update.profile');
        Route::get('/change-password', [HomeController::class, 'changePassword'])->name('sale.change.password');
        Route::post('/change-password', [HomeController::class, 'updatePassword'])->name('sale.update.password');
        Route::resource('/project', ProjectController::class);
//        Route::resource('/client', ClientController::class, ['names' => 'sale.client']);
        Route::resource('/client', ClientController::class);
        Route::get('/payment-link/{id}', [ClientController::class, 'paymentLink'])->name('client.generate.payment');
        Route::resource('/task', TaskController::class, ['names' => 'sale.task']);
        Route::resource('/subtask', SubTaskController::class);
        Route::post('/sale/files/{id}', [TaskController::class, 'insertFiles'])->name('insert.sale.files');
        Route::get('/service-list/{id}', [HomeController::class, 'serviceList'])->name('service.list');
        Route::get('/package-list/{service_id}/{brand_id}', [HomeController::class, 'packageList'])->name('package.list');
        Route::get('/assigned/client', [ClientController::class, 'getAssignedClient'])->name('assigned.client');
        Route::get('/client-chat/{id}', [HomeController::class, 'saleChat'])->name('sale.chat');
        Route::get('sale/messages/{id}', [HomeController::class, 'fetchMessages'])->name('sale.fetch.messages');
        Route::post('sale/messages/{id}', [HomeController::class, 'sendMessage'])->name('sale.send.messages');

        Route::post('invoice', [InvoiceController::class, 'saleStore'])->name('sale.invoice.create');
        Route::post('invoice/update', [InvoiceController::class, 'saleUpdate'])->name('sale.invoice.update');
        Route::any('invoice/generated/{id}', [InvoiceController::class, 'linkPageSale'])->name('sale.link');
        Route::get('invoice', [InvoiceController::class, 'getInvoiceByUserId'])->name('sale.invoice');
        Route::get('invoice/{id}', [InvoiceController::class, 'getSingleInvoice'])->name('sale.single.invoice');
        Route::get('invoice/edit/{id}', [InvoiceController::class, 'editInvoice'])->name('sale.invoice.edit');
        Route::post('/invoice/paid/{id}', [InvoiceController::class, 'invoicePaidByIdSale'])->name('sale.invoice.paid');

        Route::post('client/create_auth/', [AdminClientController::class, 'createAuth'])->name('sale.client.createauth');
        Route::post('client/update_auth/', [AdminClientController::class, 'updateAuth'])->name('sale.client.updateauth');
        Route::get('brief/pending', [LogoFormController::class, 'getBriefPendingById'])->name('sale.brief.pending');
        Route::get('sale/{form_id}/projects/{check}/form/{id}', [SupportController::class, 'getFormSale'])->name('sale.form');

        //notifications
        Route::get('/my-notifications', function () { return view('sale.my-notifications'); })->name('sale.my-notifications');

        //leads
        Route::resource('sale/lead', SaleLeadController::class, ['names' => 'sale.lead']);
    });
});
Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'is_production'], function(){
        Route::get('/production/dashboard', [HomeController::class, 'productionDashboard'])->name('production.dashboard');
        Route::get('/production/home', [HomeController::class, 'productionHome'])->name('production.home');
        Route::get('/production/profile/edit', [HomeController::class, 'productionProfile'])->name('production.profile');
        Route::get('/production/task/{id}/{notify?}', [TaskController::class, 'productionShow'])->name('production.task.show');
        Route::get('/production/subtask/{id}/{notify?}', [TaskController::class, 'productionSubtaskShow'])->name('production.subtask.show');
        Route::post('/production/subtask/edit/{id}', [TaskController::class, 'productionSubtaskUpdate'])->name('production.subtask.update');
        Route::post('/production/subtask', [SubTaskController::class, 'producionSubtask'])->name('production.subtask.store');
        Route::get('/production/subtask', [SubTaskController::class, 'producionSubtaskAssigned'])->name('production.subtask.assigned');
        Route::post('/production/files/{id}', [TaskController::class, 'insertFiles'])->name('insert.files')->withoutMiddleware('is_production');
        Route::post('/production/file/delete', [TaskController::class, 'deleteFiles'])->name('delete.files')->withoutMiddleware('is_production');
        Route::post('/production/updatetask/{id}', [TaskController::class, 'updateTask'])->name('update.task');
        Route::post('/production/subtask/assign', [SubTaskController::class, 'producionSubtaskAssign'])->name('production.subtask.assign');
        Route::post('/production/files/show/agent', [TaskController::class, 'showFilesToAgent'])->name('production.agent.file.show')->withoutMiddleware('is_production');
        Route::get('production/{form_id}/projects/{check}/form/{id}', [SupportController::class, 'getFormByProduction'])->name('production.form');
        Route::get('production/download/{form_id}/projects/{check}/form/{id}', [SupportController::class, 'getPdfFormByProduction'])->name('production.download.form');
        Route::patch('production/update-profile/{id}', [HomeController::class, 'updateProfileProduction'])->name('production.update.profile');
        //testing
        Route::get('notification/all/read', [HomeController::class, 'readNotification'])->name('notification.all.read')->withoutMiddleware('is_production');
        Route::get('notification', [HomeController::class, 'allNotification'])->name('production.notification');
        Route::post('change/duedate', [SubTaskController::class, 'productionChangeDuedate'])->name('production.change.duadate');
        Route::post('production/member/message', [SubTaskController::class, 'productionMemberSubtaskStore'])->name('production.member.subtask.store');
        Route::post('/production/member/files/{id}/{subtask_id?}', [TaskController::class, 'insertFilesMember'])->name('production.member.insert.files');
        Route::post('production/member/category', [TaskController::class, 'categoryMemberList'])->name('category.member.list')->withoutMiddleware('is_production');
        Route::post('production/member/category/add', [TaskController::class, 'categoryMemberListAdd'])->name('category.member.list.add')->withoutMiddleware('is_production');
        Route::post('production/member/category/remove', [TaskController::class, 'categoryMemberListRemove'])->name('category.member.list.remove');

        //notifications
        Route::get('/production/my-notifications', function () { return view('production.my-notifications'); })->name('production.my-notifications');
    });
});
Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'is_member'], function(){
        Route::get('/member/dashboard', [HomeController::class, 'memberDashboard'])->name('member.dashboard');
        Route::get('/member/task', [TaskController::class, 'memberTask'])->name('member.home');
        Route::get('/member/task/{id}/{notify?}', [SubTaskController::class, 'memberSubTask'])->name('member.subtask.show');
        Route::post('/member/files/{id}/{subtask_id?}', [TaskController::class, 'insertFilesMember'])->name('member.insert.files');
        Route::post('/member/subtask', [SubTaskController::class, 'memberSubtaskStore'])->name('member.subtask.store');
        Route::post('/member/subtask/update/{id}', [SubTaskController::class, 'memberSubtaskUpdate'])->name('member.update.task');
        Route::get('member/{form_id}/projects/{check}/form/{id}', [SupportController::class, 'getFormByMember'])->name('member.form');

        //notifications
        Route::get('/member/my-notifications', function () { return view('member.my-notifications'); })->name('member.my-notifications');
    });
});

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'is_qa'], function(){
        Route::get('/qa/dashboard', [QAController::class, 'qaDashboard'])->name('qa.dashboard');

        Route::get('/qa/home', [TaskController::class, 'qaHome'])->name('qa.home');
        Route::get('/qa/task/{id}/{notify?}', [TaskController::class, 'qaShow'])->name('qa.task.show');
        Route::post('/qa/updatetask/{id}', [TaskController::class, 'qaUpdateTask'])->name('qa.update.task');
        Route::get('qa/{form_id}/projects/{check}/form/{id}', [SupportController::class, 'getFormByQA'])->name('qa.form');
        Route::get('qa/delete-file/{id}', [TaskController::class, 'deleteQaFile'])->name('qa.delete.file');
        Route::get('qa/delete-feedback/{id}', [TaskController::class, 'deleteQaFeedback'])->name('qa.delete.feedback');

        //member create
        Route::get('qa/user', [QAController::class, 'getUserQA'])->name('qa.user.qa');
        Route::get('qa/user/create', [QAController::class, 'createUserQA'])->name('qa.user.qa.create');
        Route::post('qa/user', [QAController::class, 'storeUserQA'])->name('qa.user.qa.store');
        Route::get('qa/user/edit/{id}', [QAController::class, 'editUserQA'])->name('qa.user.qa.edit');
        Route::post('qa/user/update/{id}', [QAController::class, 'updateUserQA'])->name('qa.user.qa.update');

        //assign task to member
        Route::post('qa/assign-task-to-member', [QAController::class, 'assignTaskToMember'])->name('qa.assign.task.to.member');

        //completed tasks
        Route::get('/qa/completed-tasks', [TaskController::class, 'completedTasks'])->name('qa.completed_tasks');

        //notifications
        Route::get('/qa/my-notifications', function () { return view('qa.my-notifications'); })->name('qa.my-notifications');
    });
});

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'is_billing'], function(){
        Route::get('/billing/dashboard', [BillingController::class, 'billingDashboard'])->name('billing.dashboard');

        Route::get('/billing/clients', [BillingController::class, 'billingClient'])->name('billing.client.index');
        Route::get('/billing/clients/create', [BillingController::class, 'billingClientCreate'])->name('billing.client.create');
        Route::get('/billing/payment-link/{id}', [BillingController::class, 'billingPaymentLink'])->name('billing.generate.payment');
        Route::get('/billing/clients/edit/{id}', [BillingController::class, 'billingClientEdit'])->name('billing.client.edit');
        Route::post('/billing/clients/create', [BillingController::class, 'billingClientStore'])->name('billing.client.store');
        Route::post('/billing/invoice', [BillingController::class, 'billingStore'])->name('billing.invoice.create');
        Route::patch('/billing/clients/update/{id}', [BillingController::class, 'billingClientUpdate'])->name('billing.client.update');
        Route::any('/billing/invoice/generated/{id}', [BillingController::class, 'linkPageBilling'])->name('billing.link');

        Route::get('/billing/invoice', [BillingController::class, 'getInvoiceByBilling'])->name('billing.invoice');
        Route::get('/billing/invoice/edit/{id}', [BillingController::class, 'editInvoiceBilling'])->name('billing.invoice.edit');
        Route::post('/billing/invoice/paid/{id}', [BillingController::class, 'invoicePaidByIdBilling'])->name('billing.invoice.paid');
        Route::post('/billing/create_auth/', [BillingController::class, 'createAuthBilling'])->name('billing.client.createauth');
        Route::post('/billing/update_auth/', [BillingController::class, 'updateAuthBilling'])->name('billing.client.updateauth');
        Route::post('/billing/invoice/update', [BillingController::class, 'billingUpdateManager'])->name('billing.invoice.update');
    });
});

Route::get('/verify', [VerifyController::class, 'index'])->name('salemanager.verify');

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'is_sale_manager'], function(){
        Route::get('/manager/dashboard', [HomeController::class, 'managerDashboard'])->name('salemanager.dashboard');
        Route::get('/manager/clients', [ClientController::class, 'managerClient'])->name('salemanager.client.index');
        Route::get('/manager/clients/create', [ClientController::class, 'managerClientCreate'])->name('salemanager.client.create');
        Route::post('/manager/clients/create', [ClientController::class, 'managerClientStore'])->name('salemanager.client.store');
        Route::get('/manager/payment-link/{id}', [ClientController::class, 'managerPaymentLink'])->name('manager.generate.payment');
        Route::post('/manager/invoice', [InvoiceController::class, 'managerStore'])->name('manager.invoice.create');
        Route::any('/manager/invoice/generated/{id}', [InvoiceController::class, 'linkPageManager'])->name('manager.link');
        Route::patch('/manager/clients/update/{id}', [ClientController::class, 'managerClientUpdate'])->name('manager.client.update');
        Route::get('/manager/clients/edit/{id}', [ClientController::class, 'managerClientEdit'])->name('manager.client.edit');
        Route::get('/manager/invoice', [InvoiceController::class, 'getInvoiceBySaleManager'])->name('manager.invoice');
        Route::post('/manager/create_auth/', [AdminClientController::class, 'createAuthManager'])->name('manager.client.createauth');
        Route::post('/manager/update_auth/', [AdminClientController::class, 'updateAuthManager'])->name('manager.client.updateauth');
        Route::post('/manager/invoice/paid/{id}', [InvoiceController::class, 'invoicePaidByIdManager'])->name('manager.invoice.paid');
        Route::get('/manager/brief/pending', [LogoFormController::class, 'getBriefPendingByIdManager'])->name('manager.brief.pending');
        Route::get('/manager/invoice/edit/{id}', [InvoiceController::class, 'editInvoiceManager'])->name('manager.invoice.edit');
        Route::post('/manager/invoice/update', [InvoiceController::class, 'saleUpdateManager'])->name('manager.invoice.update');
        Route::get('/manager/invoice/refund', [InvoiceController::class, 'managerRefundCB'])->name('manager.refund.cb');
        Route::post('/manager/invoice/refund', [InvoiceController::class, 'managerRefundCBSubmit'])->name('manager.refund.cb.submit');
        Route::get('/manager/invoice/sheet', [InvoiceController::class, 'managerSalesSheet'])->name('manager.sales.sheet');

        Route::get('/manager/pending/projects', [LogoFormController::class, 'getPendingProjectManager'])->name('manager.pending.project');
        Route::get('/manager/pending/projects/{id}/{form}', [LogoFormController::class, 'getPendingProjectbyIdManager'])->name('manager.pending.project.details');
        Route::post('/manager/assign/support/', [AdminClientController::class, 'assignSupportManager'])->name('manager.assign.support');
        Route::post('/manager/reassign/support/', [AdminClientController::class, 'reassignSupportManager'])->name('manager.reassign.support');

        Route::post('/manager/reassign/support/taskid', [AdminClientController::class, 'reassignSupportManagerTaskID'])->name('manager.reassign.support.taskid');

        Route::get('/manager/client/agent/{brand_id?}', [AdminClientController::class, 'getAgentManager'])->name('manager.client.agent');
        Route::get('/manager/project', [AdminProjectController::class, 'indexManager'])->name('manager.project.index');
        Route::get('/manager/project/edit/{id}', [AdminProjectController::class, 'indexEdit'])->name('manager.project.edit');
        Route::get('/manager/task', [AdminTaskController::class, 'indexManager'])->name('manager.task.index');
        Route::get('/manager/task/show/{id}', [TaskController::class, 'managerTaskShow'])->name('manager.task.show');
        Route::post('/manager/task/production', [TaskController::class, 'managerTaskProduction'])->name('manager.task.production');
        Route::post('manager/subtask/store', [TaskController::class, 'managerTaskStore'])->name('manager.subtask.store');
        Route::post('/manager/files/{id}', [TaskController::class, 'managerInsertFiles'])->name('manager.insert.sale.files');
        Route::post('/manager/message/', [SupportController::class, 'managerSendMessage'])->name('manager.message.send');
        Route::post('/manager/task/notes', [TaskController::class, 'storeoNotesByManager'])->name('store.notes.by.manager');
        Route::get('/manager/message', [SupportController::class, 'getMessageByManager'])->name('manager.message');
        Route::get('/manager/message/{id}/{name}/show', [SupportController::class, 'getMessageByManagerClientId'])->name('manager.message.show');
        Route::get('/manager/message/edit/{id}', [SupportController::class, 'editMessageByManagerClientId'])->name('manager.message.edit');
        Route::post('/manager/message/update', [SupportController::class, 'updateManagerMessage'])->name('manager.message.update');
        Route::get('/manager/clients/details/{id}/{name}', [ClientController::class, 'managerClientById'])->name('manager.client.details');
        Route::get('/manager/edit-profile', [HomeController::class, 'editProfileManager'])->name('manager.edit.profile');
        Route::patch('/manager/update-profile/{id}', [HomeController::class, 'updateProfileManager'])->name('manager.update.profile');
        Route::get('/manager/change-password', [HomeController::class, 'changePasswordManager'])->name('manager.change.password');
        Route::post('/manager/change-password', [HomeController::class, 'updatePasswordManager'])->name('manager.update.password');
        Route::get('manager/{form_id}/projects/{check}/form/{id}', [SupportController::class, 'getFormManager'])->name('manager.form');
        Route::post('manager/mark/notification', [HomeController::class, 'markNotification'])->name('mark.notification');
        Route::get('manager/notification', [HomeController::class, 'managerNotification'])->name('manager.notification');

        Route::get('manager/user/sales', [ManagerUserController::class, 'getUserSale'])->name('manager.user.sales');
        Route::post('manager/user/sales', [ManagerUserController::class, 'storeUserSale'])->name('manager.user.sales.store');
        Route::get('manager/user/create', [ManagerUserController::class, 'createUserSale'])->name('manager.user.sales.create');
        Route::get('manager/user/sale/edit/{id}', [ManagerUserController::class, 'editUserSale'])->name('manager.user.sales.edit');
        Route::post('manager/user/sale/update/{id}', [ManagerUserController::class, 'updateUserSale'])->name('manager.user.sales.update');

        //notifications
        Route::get('/manager/my-notifications', function () { return view('manager.my-notifications'); })->name('manager.my-notifications');

        //admin invoices
        Route::get('manager/admin-invoices', [ManagerAdminInvoiceController::class, 'index'])->name('manager.admin-invoice.index');

        //leads
        Route::resource('manager/lead', ManagerLeadController::class, ['names' => 'manager.lead']);
    });
});


//brands dashboard
//Route::get('brands-dashboard', [GeneralBrandController::class, 'brands_dashboard'])->name('brands.dashboard');
//Route::get('brands-detail/{id}', [GeneralBrandController::class, 'brands_detail'])->name('brands.detail');
//Route::get('clients-detail/{id}', [GeneralBrandController::class, 'clients_detail'])->name('clients.detail');
//Route::get('service-detail/{id}', [GeneralBrandController::class, 'projects_detail'])->name('projects.detail');

//brands dashboard v3
Route::get('brands-dashboard', BrandDashboard::class)->name('brands.dashboard.v3');
Route::get('revenue', Revenue::class)->name('revenue');
Route::get('tutorials', Tutorials::class)->name('tutorials');

Route::get('production-dashboard', ProductionDashboard::class)->name('production.dashboard.v2');

Route::get('get-invoices', [GeneralBrandController::class, 'get_invoices'])->middleware('auth')->name('get-invoices');
Route::get('get-support-agents', [GeneralBrandController::class, 'get_support_agents'])->middleware('auth')->name('get-support-agents');
Route::post('assign-pending-project-to-agent', [GeneralBrandController::class, 'assign_pending_project_to_agent'])->middleware('auth')->name('assign-pending-project-to-agent');
Route::get('fetch-search-bar-content', [GeneralBrandController::class, 'fetch_search_bar_content'])->middleware('auth')->name('fetch-search-bar-content');
Route::get('check-if-external-client', [GeneralBrandController::class, 'check_if_external_client'])->middleware('auth')->name('check-if-external-client');
Route::post('clear-notification', [GeneralBrandController::class, 'clear_notification'])->middleware('auth')->name('clear-notification');

Route::get('redirect-to-livewire', function (Request $request) {
    session()->put('livewire_history', [ 'brands_dashboard', $request->page ]);
    return redirect()->route('brands.dashboard.v3');
})->name('redirect-to-livewire');

Route::post('stripe-invoice-paid', [StripeController::class, 'stripe_invoice_paid'])->name('stripe.invoice.paid')->withoutMiddleware('verify.csrf.token');

//comments
Route::post('update-client-comments', [CommentsController::class, 'updateClientComments'])->name('update.client.comments');
Route::post('update-project-comments', [CommentsController::class, 'updateProjectComments'])->name('update.project.comments');

Route::get('temp', function () {
//    return view('client.messages');
//    foreach (\App\Models\Client::all() as $client) {
//        populate_clients_show_service_forms($client);
//    }

//    $ids = [9445, 10673, 9906, 9422, 9636, 9657, 10040, 10108, 10352, 10629, 10394, 10525, 10662, 10576, 10577, 10578, 10303, 10534, 10396, 10302, 10302, 10580, 10371, 10395, 10419, 10420, 10441, 10442, 10468];
//    $notifications = \App\Models\CRMNotification::where('type', 'App\Notifications\TaskNotification')
//        ->where('notifiable_id', 1733)
//        ->get();
//    $affected_count= 0;
//
//    foreach ($notifications as $notification) {
//        $data = json_decode($notification->data);
//
//        if (in_array($data->task_id, $ids)) {
//            $notification->notifiable_id = 2565;
//            $notification->save();
//
//            $affected_count += 1;
//        }
//    }
//
//    dd('done! affected count: ' . $affected_count);


//    $date = Carbon::parse('31 December 2023');
//    dump(DB::table('notifications')->whereDate('created_at', '<=', $date)->count());
//    \App\Models\Client::whereHas('user', function ($q) {
//        return $q->whereHas('projects', function ($q) {
//            return $q->whereHas('tasks', function ($q) {
//                return $q->where('id', 123);
//            });
//        });
//    });
});

Route::group(['middleware' => ['auth'], 'prefix' => 'v2'], function () {
    Route::get('dashboard', [\App\Http\Controllers\v2\DashboardController::class, 'dashboard'])->name('v2.dashboard');

    Route::get('revenue', [\App\Http\Controllers\v2\DashboardController::class, 'revenue'])->name('v2.revenue');

    //merchants
    Route::get('merchants', [\App\Http\Controllers\v2\MerchantController::class, 'index'])->name('v2.merchants');
    Route::get('merchants/create', [\App\Http\Controllers\v2\MerchantController::class, 'create'])->name('v2.merchants.create');
    Route::post('merchants/store', [\App\Http\Controllers\v2\MerchantController::class, 'store'])->name('v2.merchants.store');
    Route::get('merchants/edit/{id}', [\App\Http\Controllers\v2\MerchantController::class, 'edit'])->name('v2.merchants.edit');
    Route::post('merchants/update/{id}', [\App\Http\Controllers\v2\MerchantController::class, 'update'])->name('v2.merchants.update');

    //clients
    Route::get('clients', [\App\Http\Controllers\v2\ClientController::class, 'index'])->name('v2.clients');
    Route::get('clients/create', [\App\Http\Controllers\v2\ClientController::class, 'create'])->name('v2.clients.create');
    Route::post('clients/store', [\App\Http\Controllers\v2\ClientController::class, 'store'])->name('v2.clients.store');
    Route::get('clients/edit/{id}', [\App\Http\Controllers\v2\ClientController::class, 'edit'])->name('v2.clients.edit');
    Route::post('clients/update/{id}', [\App\Http\Controllers\v2\ClientController::class, 'update'])->name('v2.clients.update');
    Route::get('clients/detail/{id}', [\App\Http\Controllers\v2\ClientController::class, 'show'])->name('v2.clients.show');
    Route::post('clients/create_auth', [\App\Http\Controllers\v2\ClientController::class, 'createAuth'])->name('v2.clients.create.auth');
    Route::post('clients/update_auth', [\App\Http\Controllers\v2\ClientController::class, 'updateAuth'])->name('v2.clients.update.auth');

    //invoices
    Route::get('invoices', [\App\Http\Controllers\v2\InvoiceController::class, 'index'])->name('v2.invoices');
    Route::get('invoices/create/{id}', [\App\Http\Controllers\v2\InvoiceController::class, 'create'])->name('v2.invoices.create');
    Route::post('invoices/store', [\App\Http\Controllers\v2\InvoiceController::class, 'store'])->name('v2.invoices.store');
    Route::get('invoices/edit/{id}', [\App\Http\Controllers\v2\InvoiceController::class, 'edit'])->name('v2.invoices.edit');
    Route::post('invoices/update/{id}', [\App\Http\Controllers\v2\InvoiceController::class, 'update'])->name('v2.invoices.update');
    Route::get('invoices/detail/{id}', [\App\Http\Controllers\v2\InvoiceController::class, 'show'])->name('v2.invoices.show');
    Route::post('/invoices/paid/{id}', [\App\Http\Controllers\v2\InvoiceController::class, 'markPaid'])->name('v2.invoices.paid');
    Route::get('/invoices/refund/cb', [\App\Http\Controllers\v2\InvoiceController::class, 'refundCB'])->name('v2.invoices.refund.cb');
    Route::get('/invoices/sales/sheet', [\App\Http\Controllers\v2\InvoiceController::class, 'salesSheet'])->name('v2.invoices.sales.sheet');
    Route::get('/invoices/admin/invoices', [\App\Http\Controllers\v2\InvoiceController::class, 'adminInvoices'])->name('v2.invoices.admin.invoices');

    //leads
    Route::get('leads', [\App\Http\Controllers\v2\LeadController::class, 'index'])->name('v2.leads');
    Route::get('leads/create', [\App\Http\Controllers\v2\LeadController::class, 'create'])->name('v2.leads.create');
    Route::post('leads/store', [\App\Http\Controllers\v2\LeadController::class, 'store'])->name('v2.leads.store');
    Route::get('leads/edit/{id}', [\App\Http\Controllers\v2\LeadController::class, 'edit'])->name('v2.leads.edit');
    Route::post('leads/update/{id}', [\App\Http\Controllers\v2\LeadController::class, 'update'])->name('v2.leads.update');
    Route::get('leads/detail/{id}', [\App\Http\Controllers\v2\LeadController::class, 'show'])->name('v2.leads.show');

    Route::get('briefs-pending', [\App\Http\Controllers\v2\DashboardController::class, 'briefsPending'])->name('v2.briefs.pending');

    Route::get('pending-projects', [\App\Http\Controllers\v2\DashboardController::class, 'pendingProjects'])->name('v2.pending.projects');

    //projects
    Route::get('projects', [\App\Http\Controllers\v2\ProjectController::class, 'index'])->name('v2.projects');
//    Route::get('projects/create', [\App\Http\Controllers\v2\ProjectController::class, 'create'])->name('v2.projects.create');
//    Route::post('projects/store', [\App\Http\Controllers\v2\ProjectController::class, 'store'])->name('v2.projects.store');
//    Route::get('projects/edit/{id}', [\App\Http\Controllers\v2\ProjectController::class, 'edit'])->name('v2.projects.edit');
//    Route::post('projects/update/{id}', [\App\Http\Controllers\v2\ProjectController::class, 'update'])->name('v2.projects.update');
//    Route::get('projects/detail/{id}', [\App\Http\Controllers\v2\ProjectController::class, 'show'])->name('v2.projects.show');

    //tasks
    Route::get('tasks', [\App\Http\Controllers\v2\TaskController::class, 'index'])->name('v2.tasks');
    Route::get('tasks/create/{id}', [\App\Http\Controllers\v2\TaskController::class, 'create'])->name('v2.tasks.create');
    Route::post('tasks/store', [\App\Http\Controllers\v2\TaskController::class, 'store'])->name('v2.tasks.store');
    Route::get('tasks/edit/{id}', [\App\Http\Controllers\v2\TaskController::class, 'edit'])->name('v2.tasks.edit');
    Route::post('tasks/update/{id}', [\App\Http\Controllers\v2\TaskController::class, 'update'])->name('v2.tasks.update');
    Route::get('tasks/detail/{id}', [\App\Http\Controllers\v2\TaskController::class, 'show'])->name('v2.tasks.show');
    Route::post('tasks/update-status/{id}', [\App\Http\Controllers\v2\TaskController::class, 'updateStatus'])->name('v2.tasks.update.status');
    Route::post('tasks/upload-files/{id}', [\App\Http\Controllers\v2\TaskController::class, 'uploadFiles'])->name('v2.tasks.upload.files');
    Route::post('support/files/{id}', [TaskController::class, 'insertFiles'])->name('support.insert.sale.files');

    //sub tasks
    Route::post('subtasks/store', [\App\Http\Controllers\v2\SubtaskController::class, 'store'])->name('v2.subtasks.store');

    //services
    Route::get('services', [\App\Http\Controllers\v2\ServiceController::class, 'index'])->name('v2.services');
    Route::get('services/create', [\App\Http\Controllers\v2\ServiceController::class, 'create'])->name('v2.services.create');
    Route::post('services/store', [\App\Http\Controllers\v2\ServiceController::class, 'store'])->name('v2.services.store');
    Route::get('services/edit/{id}', [\App\Http\Controllers\v2\ServiceController::class, 'edit'])->name('v2.services.edit');
    Route::post('services/update/{id}', [\App\Http\Controllers\v2\ServiceController::class, 'update'])->name('v2.services.update');
    Route::get('services/detail/{id}', [\App\Http\Controllers\v2\ServiceController::class, 'show'])->name('v2.services.show');

    //brands
    Route::get('brands', [\App\Http\Controllers\v2\BrandController::class, 'index'])->name('v2.brands');
    Route::get('brands/create', [\App\Http\Controllers\v2\BrandController::class, 'create'])->name('v2.brands.create');
    Route::post('brands/store', [\App\Http\Controllers\v2\BrandController::class, 'store'])->name('v2.brands.store');
    Route::get('brands/edit/{id}', [\App\Http\Controllers\v2\BrandController::class, 'edit'])->name('v2.brands.edit');
    Route::post('brands/update/{id}', [\App\Http\Controllers\v2\BrandController::class, 'update'])->name('v2.brands.update');
    Route::get('brands/detail/{id}', [\App\Http\Controllers\v2\BrandController::class, 'show'])->name('v2.brands.show');

    //production
    Route::get('users/production', [\App\Http\Controllers\v2\ProductionController::class, 'index'])->name('v2.users.production');
    Route::get('users/production/create', [\App\Http\Controllers\v2\ProductionController::class, 'create'])->name('v2.users.production.create');
    Route::post('users/production/store', [\App\Http\Controllers\v2\ProductionController::class, 'store'])->name('v2.users.production.store');
    Route::get('users/production/edit/{id}', [\App\Http\Controllers\v2\ProductionController::class, 'edit'])->name('v2.users.production.edit');
    Route::post('users/production/update/{id}', [\App\Http\Controllers\v2\ProductionController::class, 'update'])->name('v2.users.production.update');
    Route::get('users/production/detail/{id}', [\App\Http\Controllers\v2\ProductionController::class, 'show'])->name('v2.users.production.show');

    //sales
    Route::get('users/sales', [\App\Http\Controllers\v2\SalesController::class, 'index'])->name('v2.users.sales');
    Route::get('users/sales/create', [\App\Http\Controllers\v2\SalesController::class, 'create'])->name('v2.users.sales.create');
    Route::post('users/sales/store', [\App\Http\Controllers\v2\SalesController::class, 'store'])->name('v2.users.sales.store');
    Route::get('users/sales/edit/{id}', [\App\Http\Controllers\v2\SalesController::class, 'edit'])->name('v2.users.sales.edit');
    Route::post('users/sales/update/{id}', [\App\Http\Controllers\v2\SalesController::class, 'update'])->name('v2.users.sales.update');
    Route::get('users/sales/detail/{id}', [\App\Http\Controllers\v2\SalesController::class, 'show'])->name('v2.users.sales.show');

    //qa
    Route::get('users/qa', [\App\Http\Controllers\v2\QAController::class, 'index'])->name('v2.users.qa');
    Route::get('users/qa/create', [\App\Http\Controllers\v2\QAController::class, 'create'])->name('v2.users.qa.create');
    Route::post('users/qa/store', [\App\Http\Controllers\v2\QAController::class, 'store'])->name('v2.users.qa.store');
    Route::get('users/qa/edit/{id}', [\App\Http\Controllers\v2\QAController::class, 'edit'])->name('v2.users.qa.edit');
    Route::post('users/qa/update/{id}', [\App\Http\Controllers\v2\QAController::class, 'update'])->name('v2.users.qa.update');
    Route::get('users/qa/detail/{id}', [\App\Http\Controllers\v2\QAController::class, 'show'])->name('v2.users.qa.show');

    //messages
    Route::get('messages', [\App\Http\Controllers\v2\MessageController::class, 'index'])->name('v2.messages');
});
