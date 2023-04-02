<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/register', App\Http\Controllers\Api\RegisterController::class)->name('register');
Route::post('/login', App\Http\Controllers\Api\LoginController::class)->name('login');

Route::middleware('auth:api')->get('/test', App\Http\Controllers\Api\TestController::class)
->name('test');


Route::post('/logout', App\Http\Controllers\Api\LogoutController::class)->name('logout');

Route::middleware("auth:api")->group(
    function () {
        // ADMIN AND SUPERADMIN GROUPING
        Route::group(
            [
                "middleware" =>  "role:1"
            ],
            function () {
                // Product
                Route::group(
                    [
                        "controller" => ProductController::class,
                        "prefix" => "/products",
                        "as" => "product.",
                    ],
                    function () {
                        Route::get("/", "index")->name("index")->withoutMiddleware("role:1");
                        Route::get("/{id}", "show")->name("show")->withoutMiddleware("role:1");
                        Route::post("/", "store")->name("store");
                        Route::put("/{id}", "update")->name("update");
                        Route::delete("/{id}", "destroy")->name("destroy");
                    }
                );
            }
        );

        Route::group(
            [
                "middleware" =>  "role:2"
            ],
            function () {
                // Transaction
        Route::group(
            [
                "controller" => TransactionController::class,
                "prefix" => "/transaction",
                "as" => "transaction."
            ],
            function () {
                Route::get("/", "index")->name("index")->withoutMiddleware("role:2");
                Route::get("/{id}", "show")->name("show")->withoutMiddleware("role:2");
                Route::post("/{id}", "store")->name("store");
                Route::put("/{id}", "update")->name("update");
                Route::delete("/{id}", "destroy")->name("destroy");
            }
        );
            }
        );












    }
);
