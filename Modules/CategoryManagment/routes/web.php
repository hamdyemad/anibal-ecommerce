<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\CategoryManagment\app\Http\Controllers\ActivityController;
use Modules\CategoryManagment\app\Http\Controllers\CategoryManagmentController;
use Modules\CategoryManagment\app\Http\Controllers\DepartmentController;
use Modules\CategoryManagment\app\Http\Controllers\CategoryController;
use Modules\CategoryManagment\app\Http\Controllers\SubCategoryController;

Route::group(
[
	'prefix' => 'admin/category-management',
    'as' => 'admin.category-management.'
], function(){
    // Activities
    Route::get('activities/datatable', [ActivityController::class, 'datatable'])->name('activities.datatable');
    Route::get('activities/search', [ActivityController::class, 'activitySearch'])->name('activities.search');
    Route::resource('activities', ActivityController::class);
    
    // Departments
    Route::get('departments/datatable', [DepartmentController::class, 'datatable'])->name('departments.datatable');
    Route::get('departments/search-activities', [DepartmentController::class, 'searchActivities'])->name('departments.search-activities');
    Route::resource('departments', DepartmentController::class);
    
    // Categories
    Route::get('categories/datatable', [CategoryController::class, 'datatable'])->name('categories.datatable');
    Route::get('categories/search-activities', [CategoryController::class, 'searchActivities'])->name('categories.search-activities');
    Route::resource('categories', CategoryController::class);
    
    // Sub Categories
    Route::get('subcategories/datatable', [SubCategoryController::class, 'datatable'])->name('subcategories.datatable');
    Route::resource('subcategories', SubCategoryController::class);
});