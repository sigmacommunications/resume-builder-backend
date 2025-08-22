<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\MessageSent;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get("/saad",function(){
        return "<h1>saad</h1>";
});




Route::get('cron', [\App\Http\Controllers\Api\RegisterController::class, 'cron'])->name('cron');
Route::get('cron/plane', [\App\Http\Controllers\Api\RegisterController::class, 'cron_plane'])->name('cron_plane');

Route::post('register', [\App\Http\Controllers\Api\RegisterController::class, 'register']);
Route::get('noauth', [\App\Http\Controllers\Api\RegisterController::class, 'noauth'])->name('noauth');


Route::any('login', [\App\Http\Controllers\Api\RegisterController::class, 'login']);
Route::any('verify', [\App\Http\Controllers\Api\RegisterController::class, 'verify']);
Route::post('password/email',  [\App\Http\Controllers\Api\ForgotPasswordController::class,'forget']);
Route::any('password/reset', [\App\Http\Controllers\Api\CodeCheckController::class,'index']);
Route::post('password/code/check', [\App\Http\Controllers\Api\CodeCheckController::class,'code_verify']);
// Route::get('guide', [\App\Http\Controllers\Api\CMSController::class, 'guide']);
// Route::get('term/conditions', [\App\Http\Controllers\Api\CMSController::class, 'termanscondition']);

Route::group(['middleware' => ['api','auth:api'], 'prefix' => 'auth'], function ()
{

	Route::post('template_assign',[App\Http\Controllers\Api\TemplateAssignController::class,'assign']);
	Route::get('employee/{employeeID}',[App\Http\Controllers\Api\UserController::class,'employee_detail']);
	
	Route::get('category-list',[App\Http\Controllers\Api\CategoryController::class,'index']);
	Route::get('department_list',[App\Http\Controllers\Api\DepartmentController::class,'index']);
	Route::post('add_department',[App\Http\Controllers\Api\DepartmentController::class,'store']);
	Route::post('update_department/{id}',[App\Http\Controllers\Api\DepartmentController::class,'update']);
	Route::get('employee_list/{companyid}',[App\Http\Controllers\Api\UserController::class,'employee_list']);
	Route::post('add_employee',[App\Http\Controllers\Api\UserController::class,'add_employee']);
	Route::post('add_company',[App\Http\Controllers\Api\UserController::class,'add_company']);
    Route::resource('resumes', \App\Http\Controllers\Api\ResumeController::class);
	Route::resource('cover-letter', \App\Http\Controllers\Api\CoverLetterController::class);
	Route::delete('resumes-delete-all', [\App\Http\Controllers\Api\ResumeController::class, 'deleteAllResumes']);
    Route::resource('mail', \App\Http\Controllers\Api\MailController::class);
	Route::resource('/survey', \App\Http\Controllers\Api\SurveyController::class);
	Route::resource('/career-blog', \App\Http\Controllers\Api\CareerBlogController::class);
	Route::post('document', [App\Http\Controllers\Api\DocumentController::class, 'store']);
	Route::post('document/{id}', [App\Http\Controllers\Api\DocumentController::class, 'destroy']);
	Route::get('document/index', [App\Http\Controllers\Api\DocumentController::class, 'index']);
	Route::post('signature', [App\Http\Controllers\Api\SignatureController::class, 'store']);
	Route::post('signature/{id}', [App\Http\Controllers\Api\SignatureController::class, 'destroy']);
	Route::get('signature/index', [App\Http\Controllers\Api\SignatureController::class, 'index']);
	Route::post('image', [App\Http\Controllers\Api\ImageController::class, 'store']);
	Route::get('image/index', [App\Http\Controllers\Api\ImageController::class, 'index']);
	Route::post('image/{id}', [App\Http\Controllers\Api\ImageController::class, 'destroy']);
	

	
    // Route::post('/rider-arrived/{rideid}', [App\Http\Controllers\Api\BookRideController::class, 'rider_arrived']);
    // Route::post('/bookride', [App\Http\Controllers\Api\BookRideController::class, 'bookRide']);
    // Route::get('/ride/{rideID}', [App\Http\Controllers\Api\BookRideController::class, 'getbookride']);
    // Route::get('wallet',[App\Http\Controllers\Api\UserController::class,'wallet']);

    // Route::post('/message', function (Request $request) {
    //     // Create a dummy message for testing
    //     $message = [
    //         'chat_id' => $request->chat_id,
    //         'target_id' => $request->target_id,
    //         'text' => $request->text,
    //         'createdAt' => $request->createdAt,
    //         'user' => $request->user,
    //     ];

    //     // Broadcast the event
    //     broadcast(new MessageSent((object)$message))->toOthers();

    //     return response()->json(['status' => 'Message broadcasted']);
    // });

	// Route::POST('send_message',[App\Http\Controllers\Api\MessageController::class,'sendMessage']);
	// Route::get('chat_list',[App\Http\Controllers\Api\MessageController::class,'chat_list']);
	// Route::get('message_list',[App\Http\Controllers\Api\MessageController::class,'message_list']);
	// Route::get('product',[App\Http\Controllers\Api\ProductController::class,'index']);
    Route::post('profile', [\App\Http\Controllers\Api\UserController::class, 'profile']);
	// Route::resource('document',App\Http\Controllers\Api\DocumentController::class);
	// Route::resource('pdf',App\Http\Controllers\Api\PDFController::class);

    // Route::post('/donate', [App\Http\Controllers\Api\DonateController::class, 'store']);
    // Route::post('/checkout', [App\Http\Controllers\Api\OrderController::class, 'store']);
    // Route::get('/orders/list', [App\Http\Controllers\Api\OrderController::class, 'index']);

    // Route::post('review',[App\Http\Controllers\Api\UserController::class,'review']);
    // Route::get('review',[App\Http\Controllers\Api\UserController::class,'review_list']);
    // Route::post('support',[App\Http\Controllers\Api\UserController::class,'support']);
    // Route::post('user_status',[App\Http\Controllers\Api\UserController::class,'status_update']);
    // Route::get('support',[App\Http\Controllers\Api\UserController::class,'support_list']);
    // Route::post('/city_price',[\App\Http\Controllers\CityPriceController::class,'fetch_price']);
    // Route::get('reasons',[App\Http\Controllers\Api\UserController::class,'reason_list']);

    // Route::group(['prefix' => 'rider'], function () {
    //     Route::get('assign-ride',[App\Http\Controllers\Api\Rider\RideController::class,'index']);
    //     Route::post('ride_update/{rideID}',[App\Http\Controllers\Api\Rider\RideController::class,'rider_ride_update']);
    //     Route::post('ride_on_the_way/{rideID}',[App\Http\Controllers\Api\Rider\RideController::class,'ride_on_the_way']);
    //     Route::post('update_location/{rideID}',[App\Http\Controllers\Api\Rider\RideController::class,'update_location']);
    //     Route::get('ride_history', [App\Http\Controllers\Api\Rider\RideController::class, 'ride_history']);
    // });
    
    // Route::group(['prefix' => 'customer'], function () {
	
    //     Route::post('review',[App\Http\Controllers\Api\UserController::class,'review']);
    //     Route::get('journey',[App\Http\Controllers\Api\Customer\BookingController::class,'journey']);
    //     Route::get('car_list',[App\Http\Controllers\Api\Customer\BookingController::class,'car_list']);
    //     Route::post('ride_update/{rideID}',[App\Http\Controllers\Api\Customer\RideController::class,'ride_update']);
    //     Route::get('ride_history', [App\Http\Controllers\Api\Customer\BookingController::class, 'ride_history']);
        //     Route::resource('quote',App\Http\Controllers\Api\Member\QuoteController::class);
	//     Route::post('hiring/create',[App\Http\Controllers\Api\Member\QuoteController::class,'hiring_store']);
	//     Route::post('search',[App\Http\Controllers\Api\Member\QuoteController::class,'search']);
    //     route::post('update_status/{id}',[App\Http\Controllers\Api\Member\QuoteController::class,'update_status']);
    //     route::get('quote_ongoing',[App\Http\Controllers\Api\Member\QuoteController::class,'ongoing']);
    //     route::get('bid',[App\Http\Controllers\Api\Member\QuoteController::class,'bid_list']);
	//     Route::post('bid/{id}',[App\Http\Controllers\Api\Member\QuoteController::class,'bid_update']);
	//     Route::resource('bid_help',App\Http\Controllers\Api\Member\BidHelpController::class);
    //     route::get('negotiator',[App\Http\Controllers\Api\Member\QuoteController::class,'negotitator_list']);
    // });

    // Route::group(['prefix' => 'negotiator'], function () {
	//     Route::resource('bid',App\Http\Controllers\Api\Negotiator\BidController::class);
	//     Route::get('bid_help',[App\Http\Controllers\Api\Negotiator\BidController::class,'bid_help_list']);
	//     Route::post('search/{type}',[App\Http\Controllers\Api\Negotiator\BidController::class,'search']);
	//     Route::post('bid_help/{id}',[App\Http\Controllers\Api\Negotiator\BidController::class,'bid_help_update']);
    //     Route::get('quote/recommended',[App\Http\Controllers\Api\Negotiator\BidController::class,'quote_recommend_list']);
    //     Route::post('hiring/update/{id}',[App\Http\Controllers\Api\Negotiator\BidController::class,'hiring_update']);
	//     Route::get('hiring/list',[App\Http\Controllers\Api\Negotiator\BidController::class,'hiring_list']);
    //     Route::get('quote/complete',[App\Http\Controllers\Api\Negotiator\BidController::class,'quote_complete_list']);
    //     Route::get('quote_detail/{id}',[App\Http\Controllers\Api\Negotiator\BidController::class,'quote_detail']);
    //     Route::get('quote/working',[App\Http\Controllers\Api\Negotiator\BidController::class,'quote_working_list']);
	//     Route::post('profile_update',[App\Http\Controllers\Api\UserController::class,'negotiator_profile_update']);
	//     Route::post('photo_update',[App\Http\Controllers\Api\UserController::class,'negotiator_photo_update']);
	//     Route::post('coverphoto_update',[App\Http\Controllers\Api\UserController::class,'negotiator_coverphoto_update']);
    // });

    // Route::get('notification', [\App\Http\Controllers\Api\UserController::class, 'un_reead_notification']);
    // Route::post('/notification',[\App\Http\Controllers\Api\UserController::class,'read_notification']);
    // Route::post('/checkout', [App\Http\Controllers\Api\OrderController::class, 'store']);
    // Route::get('shipping', [\App\Http\Controllers\Api\ShippingController::class, 'index']);
    // Route::get('category', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
    // Route::get('brand', [\App\Http\Controllers\Api\BrandController::class, 'index']);
    // Route::get('product', [\App\Http\Controllers\Api\ProductController::class, 'index']);
    // Route::get('product/{brand}', [\App\Http\Controllers\Api\ProductController::class, 'brand_product']);
	// Route::resource('cart',App\Http\Controllers\Api\CartController::class);

	// Route::resource('trophy',App\Http\Controllers\Api\TrophyController::class);
    // Route::post('set_goal', [\App\Http\Controllers\Api\GoalController::class, 'set_goal']);
    // Route::get('goal/list', [\App\Http\Controllers\Api\GoalController::class, 'list']);
    // Route::post('addcard', [\App\Http\Controllers\UserCardController::class, 'addcard']);
	// Route::post('updatecard', [\App\Http\Controllers\UserCardController::class, 'updatecard']);
    Route::get('company_detail', [\App\Http\Controllers\Api\UserController::class, 'companyinfo']);
    // Route::get('user', [\App\Http\Controllers\Api\RegisterController::class, 'user']);
    // Route::get('orders', [\App\Http\Controllers\Api\OrderController::class, 'orders']);
    // Route::get('children_orders', [\App\Http\Controllers\Api\OrderController::class, 'childorders']);
    // Route::post('order/{status}', [\App\Http\Controllers\Api\OrderController::class, 'orders_status']);
    // Route::get('transaction', [\App\Http\Controllers\TransactionController::class, 'index']);
    // Route::post('withdraw', [\App\Http\Controllers\TranasactionController::class, 'store']);
    // Route::get('withdraw/list', [\App\Http\Controllers\TranasactionController::class, 'index']);
    Route::post('change_password', [\App\Http\Controllers\Api\RegisterController::class, 'change_password']);
	// Route::post('cuurent/plan', [\App\Http\Controllers\Api\UserController::class, 'current_plan']);
    // Route::get('financial/breakdowns/{date}', [\App\Http\Controllers\Api\FinancialController::class, 'financialdata']);
    // Route::post('financial/breakdowns/post', [\App\Http\Controllers\Api\FinancialController::class, 'financialpost']);
    //Route::get('company/info', [\App\Http\Controllers\Api\UserController::class, 'companyinfo']);
    // Route::post('contact/submit', [\App\Http\Controllers\Api\ContactController::class, 'contact_info']);
    Route::get('logout', [\App\Http\Controllers\Api\RegisterController::class, 'logout']);
});
