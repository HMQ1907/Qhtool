<?php

use App\Http\Controllers\Auth\ShowLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportErrorController;
use Illuminate\Support\Facades\Route;

Route::get("/login", ShowLoginController::class)->name("login");
Route::post("/login", LoginController::class)
    ->middleware("guest")
    ->name("login.store");
Route::post("/logout", LogoutController::class)
    ->middleware("auth")
    ->name("logout");

Route::redirect("/", "/campaigns");

Route::middleware(["auth"])->group(function () {
    Route::get("/campaigns", [CampaignController::class, "index"])->name("campaign.index");
    Route::post("/campaigns", [CampaignController::class, "store"])->name("campaign.store");
    Route::get("/campaigns/{campaign}", [CampaignController::class, "show"])->name("campaign.show");

    Route::get("/profile", ProfileController::class)
        ->name("profile");
});

Route::post("report-errors", ReportErrorController::class)->name("report-errors");
