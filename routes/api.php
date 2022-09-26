<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ArticleController as Article;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CharacterController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\DiscussionController;
use App\Http\Controllers\Api\EpisodeController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\MovieCategoryController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\MusicController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\PodcastController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SeasonController;
use App\Http\Controllers\Api\SlideController;
use App\Http\Controllers\Api\RatingTypeController;

use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Http\Controllers\ArticleCommentController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AvatarController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/user/{userID}', [AuthController::class, 'user']);
    Route::put('/update_profile/{userID}', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail']);
    Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

});

// Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
// Route::post('reset-password', [NewPasswordController::class, 'reset']);

// Route::apiResource('articles', ArticleController::class);
// Route::apiResource('avatars', AvatarController::class);

// Route::post('article-comment/{id}', [ArticleCommentController::class, 'store']);
// Route::put('article-comment/{id}', [ArticleCommentController::class, 'update']);
// Route::delete('article-comment/{id}', [ArticleCommentController::class, 'destroy']);

Route::apiResource('articles', Article::class);
Route::apiResource('article-category', CategoryArticleController::class);
// Route::apiResource('comments', CommentController::class);

// Route::apiResource('products', ProductController::class);
// Route::apiResource('product-category', CategoryController::class);
// Route::apiResource('brands', BrandController::class);

Route::apiResource('persons', PersonController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('characters', CharacterController::class);

Route::apiResource('movies', MovieController::class);
Route::apiResource('movie-category', MovieCategoryController::class);
Route::apiResource('genres', GenreController::class);
Route::apiResource('countries', CountryController::class);
Route::apiResource('seasons', SeasonController::class);
Route::apiResource('episodes', EpisodeController::class);
Route::apiResource('reviews', ReviewController::class);
// Route::apiResource('discussions', DiscussionController::class);
Route::apiResource('rating-type', RatingTypeController::class);

Route::apiResource('musics', MusicController::class);
Route::apiResource('podcasts', PodcastController::class);
Route::apiResource('slides', SlideController::class);