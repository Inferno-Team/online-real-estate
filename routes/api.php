<?php

use App\Http\Controllers\RealEstateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [UserController::class, 'login']);
Route::post('/signup', [UserController::class, 'signUp']);
Route::post('/logout', [UserController::class, 'logout']);
Route::get('/generate',[RealEstateController::class, 'generateRealEstates']);
Route::get('/recent_real_estate', [RealEstateController::class, 'recentRealEstate']);
Route::get('/real_estate_rooms/{id}', [RealEstateController::class, 'realEstateRooms']);
Route::get('/get_estate_details/{id}', [RealEstateController::class, 'estateDetails']);

Route::group(['middleware' => ['auth:sanctum']], function ($route) {

    $route->post('/add_real_estate', [RealEstateController::class, 'addRealEstate']);
    $route->post('/edit_image', [RealEstateController::class, 'editEstateImage']);
    $route->post('/edit_video360', [RealEstateController::class, 'editVideo360']);
    $route->post('/upload_room_images', [RealEstateController::class, 'uploadRoomImages']);
    $route->get('/get_my_real_estate', [RealEstateController::class, 'getMyRealEstate']);
    $route->post('/add_real_estate_room/{id}',[RealEstateController::class,'addRealEstateRoom']);
    $route->post('/filter_by_buy_type',[RealEstateController::class,'filterByBuyType']);
    $route->post('/filter_by_estate_type',[RealEstateController::class,'filterByEstateType']);
    $route->post('/filter_full',[RealEstateController::class,'filterFull']);
    $route->get('/get_real_estate_inside_circle',[RealEstateController::class,'getRealEstateInsideCircle']);
});
