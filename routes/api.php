<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function(){
    Route::post('/users', 'register');
    Route::post('/users/login', 'login');
    Route::middleware(ApiAuthMiddleware::class)->group(function () {
        Route::get('/users/current', 'get');
        Route::patch('/users/current', 'update');
        Route::delete('/users/logout', 'logout');
    });
});

Route::controller(ContactController::class)->group(function(){
    Route::middleware(ApiAuthMiddleware::class)->group(function () {
        Route::post('/contacts', 'ContactCreate');
        Route::get('/contacts', 'ContactSearch');
        Route::get('/contacts/{id}', 'ContactGet')->where('id', '[0-9]+');
        Route::put('/contacts/{id}', 'ContactUpdate')->where('id', '[0-9]+');
        Route::delete('/contacts/{id}', 'ContactRemove')->where('id', '[0-9]+');
    });
});

Route::controller(AddressController::class)->group(function(){
    Route::middleware(ApiAuthMiddleware::class)->group(function () {
        Route::post('/contacts/{idContact}/addresses', 'AddressCreate')->where('idContact', '[0-9]+');
        Route::get('/contacts/{idContact}/addresses', 'AddressList')->where('idContact', '[0-9]+');
        Route::get('/contacts/{idContact}/addresses/{idAddress}', 'AddressGet')->where('idContact', '[0-9]+')->where('idAddress', '[0-9]+');
        Route::put('/contacts/{idContact}/addresses/{idAddress}', 'AddressUpdate')->where('idContact', '[0-9]+')->where('idAddress', '[0-9]+');
        Route::delete('/contacts/{idContact}/addresses/{idAddress}', 'AddressDelete')->where('idContact', '[0-9]+')->where('idAddress', '[0-9]+');
    });
});
