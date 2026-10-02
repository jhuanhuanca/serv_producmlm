<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CompanyRankController;
use App\Http\Controllers\Api\CompensationPlanController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\FiveDayFundamentalController;
use App\Http\Controllers\Api\ImcPackageController;
use App\Http\Controllers\Api\InventoryImageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RegistrationOptionsController;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\StarProductController;
use App\Http\Controllers\Api\StarterPackageController;
use App\Http\Controllers\Api\TechnicalSheetController;
use App\Http\Controllers\Api\WellnessNeedController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:catalog'])->prefix('v1')->group(function (): void {
    Route::get('/inventory-images/{uuid}', [InventoryImageController::class, 'show'])
        ->where('uuid', '[0-9a-fA-F-]{36}');
});

Route::middleware(['service.token', 'throttle:catalog'])->prefix('v1')->group(function (): void {
    Route::post('/inventory-images', [InventoryImageController::class, 'store']);

    Route::get('/registration-options', RegistrationOptionsController::class);

    Route::get('/companies', [CompanyController::class, 'index']);
    Route::get('/companies/{id}', [CompanyController::class, 'show']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::put('/companies/{id}', [CompanyController::class, 'update']);
    Route::delete('/companies/{id}', [CompanyController::class, 'destroy']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::get('/products/{productId}/technical-sheet', [TechnicalSheetController::class, 'show']);
    Route::post('/products/{productId}/technical-sheet', [TechnicalSheetController::class, 'store']);
    Route::put('/technical-sheets/{id}', [TechnicalSheetController::class, 'update']);

    Route::get('/companies/{companyId}/documents', [DocumentController::class, 'index']);
    Route::post('/companies/{companyId}/documents', [DocumentController::class, 'store']);
    Route::put('/documents/{id}', [DocumentController::class, 'update']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);

    Route::get('/companies/{companyId}/ranks', [CompanyRankController::class, 'index']);
    Route::post('/companies/{companyId}/ranks', [CompanyRankController::class, 'store']);
    Route::put('/ranks/{id}', [CompanyRankController::class, 'update']);
    Route::delete('/ranks/{id}', [CompanyRankController::class, 'destroy']);

    Route::get('/companies/{companyId}/compensation-plans', [CompensationPlanController::class, 'index']);
    Route::post('/companies/{companyId}/compensation-plans', [CompensationPlanController::class, 'store']);
    Route::put('/compensation-plans/{id}', [CompensationPlanController::class, 'update']);

    Route::get('/companies/{companyId}/five-day-fundamentals', [FiveDayFundamentalController::class, 'index']);
    Route::post('/companies/{companyId}/five-day-fundamentals', [FiveDayFundamentalController::class, 'store']);
    Route::put('/five-day-fundamentals/{id}', [FiveDayFundamentalController::class, 'update']);
    Route::delete('/five-day-fundamentals/{id}', [FiveDayFundamentalController::class, 'destroy']);

    Route::get('/companies/{companyId}/star-products', [StarProductController::class, 'index']);
    Route::post('/companies/{companyId}/star-products', [StarProductController::class, 'store']);
    Route::put('/star-products/{id}', [StarProductController::class, 'update']);
    Route::delete('/star-products/{id}', [StarProductController::class, 'destroy']);

    Route::get('/companies/{companyId}/wellness-needs', [WellnessNeedController::class, 'index']);
    Route::post('/companies/{companyId}/wellness-needs', [WellnessNeedController::class, 'store']);
    Route::put('/wellness-needs/{id}', [WellnessNeedController::class, 'update']);
    Route::delete('/wellness-needs/{id}', [WellnessNeedController::class, 'destroy']);

    Route::get('/companies/{companyId}/imc-packages', [ImcPackageController::class, 'index']);
    Route::post('/companies/{companyId}/imc-packages', [ImcPackageController::class, 'store']);
    Route::put('/imc-packages/{id}', [ImcPackageController::class, 'update']);
    Route::delete('/imc-packages/{id}', [ImcPackageController::class, 'destroy']);

    Route::get('/companies/{companyId}/starter-packages', [StarterPackageController::class, 'index']);
    Route::post('/companies/{companyId}/starter-packages', [StarterPackageController::class, 'store']);
    Route::put('/starter-packages/{id}', [StarterPackageController::class, 'update']);
    Route::delete('/starter-packages/{id}', [StarterPackageController::class, 'destroy']);

    Route::get('/support-tickets', [SupportTicketController::class, 'index']);
    Route::post('/support-tickets', [SupportTicketController::class, 'store']);
    Route::get('/support-tickets/{id}', [SupportTicketController::class, 'show']);
    Route::put('/support-tickets/{id}', [SupportTicketController::class, 'update']);
    Route::post('/support-tickets/{id}/replies', [SupportTicketController::class, 'reply']);
});
