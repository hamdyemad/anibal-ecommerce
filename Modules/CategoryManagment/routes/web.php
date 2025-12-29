<?php

use Illuminate\Support\Facades\Route;
use Modules\CategoryManagment\app\Http\Controllers\DepartmentController;
use Modules\CategoryManagment\app\Http\Controllers\CategoryController;
use Modules\CategoryManagment\app\Http\Controllers\SubCategoryController;

Route::group(
[
	'prefix' => 'category-management',
    'as' => 'category-management.'
], function(){

    // Departments
    Route::get('departments/datatable', [DepartmentController::class, 'datatable'])->name('departments.datatable');
    Route::post('departments/{id}/change-status', [DepartmentController::class, 'changeStatus'])->name('departments.change-status');
    Route::post('departments/{id}/change-view-status', [DepartmentController::class, 'changeViewStatus'])->name('departments.change-view-status');
    Route::resource('departments', DepartmentController::class);

    // Categories
    Route::get('categories/datatable', [CategoryController::class, 'datatable'])->name('categories.datatable');
    Route::post('categories/{id}/change-status', [CategoryController::class, 'changeStatus'])->name('categories.change-status');
    Route::post('categories/{id}/change-view-status', [CategoryController::class, 'changeViewStatus'])->name('categories.change-view-status');
    Route::resource('categories', CategoryController::class);
    // Sub Categories
    Route::get('subcategories/datatable', [SubCategoryController::class, 'datatable'])->name('subcategories.datatable');
    Route::post('subcategories/{id}/change-status', [SubCategoryController::class, 'changeStatus'])->name('subcategories.change-status');
    Route::post('subcategories/{id}/change-view-status', [SubCategoryController::class, 'changeViewStatus'])->name('subcategories.change-view-status');
    Route::resource('subcategories', SubCategoryController::class);
});
