<?php

use App\Http\Controllers\CDatatable;
use Illuminate\Support\Facades\Route;

Route::prefix('datatable')->name('datatable.')->middleware(['auth'])
    ->controller(CDatatable::class)->group(fn() => [
        Route::get('departments', 'departments')->name('departments'),
        Route::get('employees', 'employees')->name('employees'),
        Route::get('products', 'products')->name('products'),
        Route::get('products/trash', 'productTrash')->name('products.trash'),
        Route::get('capa-types', 'capaTypes')->name('capa-types'),
        Route::get('capa-types/trash', 'capaTypeTrash')->name('capa-types.trash'),
        Route::get('positions', 'positions')->name('positions'),
        Route::get('facility-change-authorizations', 'facilityChangeAuthorizations')->name('facility-change-authorizations'),
        Route::get('halal-assesments', 'halalAssesments')->name('halal-assesments'),
        Route::get('regulatory-assesments', 'regulatoryAssesments')->name('regulatory-assesments'),
        Route::get('scope-of-changes', 'scopeOfChanges')->name('scope-of-changes'),
        Route::get('stimuli-of-changes', 'stimuliOfChanges')->name('stimuli-of-changes'),
        Route::get('change-requests', 'changeRequests')->name('change-requests'),
        Route::get('affected-products/{changeRequest}', 'affectedProducts')->name('affected-products'),
        Route::get('related-department-assessment/{changeRequest}', 'relatedDepartmentAssessment')->name('related-department-assessment'),
        Route::get('follow-up-implementations/{changeRequest}', 'followUpImplementations')->name('follow-up-implementations'),
        Route::get('issues', 'issues')->name('issues'),

    ]);
