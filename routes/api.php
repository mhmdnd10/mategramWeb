<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\FollowController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/password/reset/{token}', function ($token) {
    return response()->json(['token' => $token, 'message' => 'Use this token to reset password.']);
})->name('password.reset');


Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::middleware(['auth:sanctum'])->post('/logout', [AuthController::class, 'logout']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/email/verification-notification',function (Request $request){
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Veriifcation link sent']);
    });

    Route::post('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request){
        $request->fulfill();
        return response()->json(['message' => 'Email verified successfully!']);

    })->name('verification.verify');
    Route::get('/search/users', [AuthController::class, 'search']);

});

Route::post('/forgot-password',[AuthController::class,'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

Route::middleware('auth:sanctum')->post('/change-password',[AuthController::class,'changePassword']);


Route::middleware('auth:sanctum')->group(function(){
    Route::post('/posts',[PostController::class,'store']);
    Route::get('/posts',[PostController::class,'index']);
    Route::get('/posts/{post_id}',[PostController::class,'show']);
    Route::delete('/posts/{post_id}',[PostController::class,'destroy']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/comments/{post_id}',[CommentController::class,'store']);
    Route::get('/comments/{post_id}',[CommentController::class,'index']);
    Route::get('/comments/{post_id}/{id}',[CommentController::class,'show']);
    Route::delete('/comments/{post_id}/{id}',[CommentController::class,'destroy']);
});


Route::middleware('auth:sanctum')->group(function(){
    Route::post('/likes/{post_id}',[LikeController::class,'store']);
});
Route::get('/likes/{post_id}',[LikeController::class,'index']);
Route::get('/likes/{post_id}/count',[LikeController::class,'count']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/notifications',[NotificationController::class,'store']);
    Route::get('/notifications',[NotificationController::class,'index']);
    Route::get('/notifications/{id}',[NotificationController::class,'show']);
    Route::delete('/notifications/{id}',[NotificationController::class,'destroy']);
});



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/stories', [StoryController::class, 'store']);
    Route::get('/stories', [StoryController::class, 'index']);
    Route::get('/stories/user/{userId}', [StoryController::class, 'userStories']);
    Route::delete('/stories/{id}', [StoryController::class, 'destroy']);

});




Route::middleware('auth:sanctum')->group(function () {
    Route::post('follow', [FollowController::class, 'follow']);
    Route::post('unfollow', [FollowController::class, 'unfollow']);
});
Route::get('followers/{userId}', [FollowController::class, 'followers']);
Route::get('following/{userId}', [FollowController::class, 'following']);
Route::get('followers/count/{user_id}', [FollowController::class, 'followerCount']);
Route::get('following/count/{user_id}', [FollowController::class, 'followingCount']);



