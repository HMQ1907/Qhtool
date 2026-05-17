<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ReportErrorController;
use Illuminate\Support\Facades\Route;

Route::redirect("/", "/campaigns");

Route::get("/campaigns", [CampaignController::class, "index"])->name("campaign.index");
Route::post("/campaigns", [CampaignController::class, "store"])->name("campaign.store");
Route::get("/campaigns/{campaign}", [CampaignController::class, "show"])->name("campaign.show");

Route::post("report-errors", ReportErrorController::class)->name("report-errors");
