<?php

use App\Http\Controllers\CIndex;
use App\Http\Controllers\CProfile;
use App\Http\Controllers\Capa\CIssue;
use App\Http\Controllers\Capa\CIssueResolution;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CSetLanguage;
use App\Http\Controllers\Master\CProduct;
use App\Http\Controllers\Master\CCapaType;
use App\Http\Controllers\Master\CEmployee;
use App\Http\Controllers\Master\CPosition;
use App\Http\Controllers\Master\CDepartment;
use App\Http\Controllers\ChangeRequest\CChangeRequest;
use App\Http\Controllers\Master\Attributes\CScopeOfChange;
use App\Http\Controllers\ChangeRequest\CRegulatoryAssesment;
use App\Http\Controllers\ChangeRequest\CFollowUpImplementation;



Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [CIndex::class, 'index'])->name('dashboard');
    Route::post('/set-language', CSetLanguage::class)->name('set-language');
    Route::prefix('master-data')->name('master-data.')->group(function () {
        // Master Data
        Route::resource('departments', CDepartment::class);
        Route::resource('employees', CEmployee::class);
        Route::resource('positions', CPosition::class);
        // Product
        Route::get('trash/products', [CProduct::class, 'trash'])->name('products.trash');
        Route::delete('products/delete', [CProduct::class, 'delete'])->name('products.delete');
        Route::post('products/restore', [CProduct::class, 'restore'])->name('products.restore');
        Route::resource('products', CProduct::class);
        // Capa Types
        Route::get('trash/capa-types', [CCapaType::class, 'trash'])->name('capa-types.trash');
        Route::delete('capa-types/delete', [CCapaType::class, 'delete'])->name('capa-types.delete');
        Route::post('capa-types/restore', [CCapaType::class, 'restore'])->name('capa-types.restore');
        Route::resource('capa-types', CCapaType::class);
        // Attributes
        Route::prefix('attribute-types')->name('attribute-types.')->group(function () {
            Route::resource('scope-of-changes', CScopeOfChange::class);
        });
    });
    Route::prefix('change-requests')->name('change-requests.')->group(function () {

        Route::resource('regulatory-assessments', CRegulatoryAssesment::class);
        Route::resource('ppic-assessments', \App\Http\Controllers\PPICAssessmentController::class);
        Route::resource('follow-up-implementations', CFollowUpImplementation::class);
        Route::get('/', [CChangeRequest::class, 'index'])->name('index');
        Route::get('/create', [CChangeRequest::class, 'create'])->name('create');
        Route::post('/store/change-initiation', [CChangeRequest::class, 'changeInitiation'])->name('change-initiation');
        Route::post('/store/impact-risk-assessment', [CChangeRequest::class, 'impactRiskAssesment'])->name('impact-risk-assessment');
        Route::post('/store/impact-of-change-assessment', [CChangeRequest::class, 'impactOfChangeAssesment'])->name('impact-of-change-assessment');
        Route::post('/store', [CChangeRequest::class, 'store'])->name('store');
        Route::post('/save-as-draft', [CChangeRequest::class, 'saveAsDraft'])->name('save-as-draft');
        Route::get('/edit/{changeRequest}', [CChangeRequest::class, 'edit'])->name('edit');
        Route::get('/{changeRequest}', [CChangeRequest::class, 'show'])->name('show');
        Route::delete('/destroy/{changeRequest}', [CChangeRequest::class, 'destroy'])->name('destroy');
        Route::post('/review/validate', [CChangeRequest::class, 'reviewValidate'])->name('review.validate');
        Route::post('/review/store/{changeRequest}', [CChangeRequest::class, 'reviewStore'])->name('review.store');
        Route::post('/approve/{changeRequest}', [CChangeRequest::class, 'approve'])->name('approve');
        Route::post('/approve/validate/{changeRequest}', [CChangeRequest::class, 'approveValidate'])->name('approve.validate');
        Route::get('/qa-approval/{changeRequest}', [CChangeRequest::class, 'qaApproval'])->name('qa-approval');
        Route::post('/action-plan/reject/{actionPlan}', [CChangeRequest::class, 'rejectActionPlan'])->name('action-plan.reject');
        Route::post('/action-plan/approve/{actionPlan}', [CChangeRequest::class, 'approveActionPlan'])->name('action-plan.approve');
        Route::post('/action-plan/submit', [CChangeRequest::class, 'savePendingActionPlan'])->name('action-plan.submit');
        Route::post('/action-plan/proof-of-work-upload', [CChangeRequest::class, 'proofUpload'])->name('upload.proof-of-work');
        Route::post('/overdue-request/store', [CChangeRequest::class, 'requestOverdue'])->name('overdue-request.store');
        Route::post('/overdue-request/approve', [CChangeRequest::class, 'approveOverdue'])->name('overdue-request.approve');
        Route::post('/overdue-request/reject', [CChangeRequest::class, 'rejectOverdue'])->name('overdue-request.reject');
        Route::post('/action-plan/close', [CChangeRequest::class, 'closeActionPlan'])->name('action-plan.close');
        Route::post('/close-request/{changeRequest}', [CChangeRequest::class, 'closeRequest'])->name('close-request');
        Route::put('/related-department-assessment/{relatedDepartmentAssessment}', [CChangeRequest::class, 'updateRelatedDepartmentAssessment'])->name('related-department-assessment.update');
        Route::get('/print/{changeRequest}', [CChangeRequest::class, 'print'])->name('print');
    });
    Route::prefix('capa')->name('capa.')->group(function () {
        Route::get('issue/print/{issue}', [CIssue::class, 'print'])->name('issue.print');
        Route::post('issue/accept/{issue}', [CIssue::class, 'accept'])->name('issue.accept');
        Route::post('issue/approve/{issue}', [CIssueResolution::class, 'approve'])->name('issue.approve');
        Route::post('issue/reject/{issue}', [CIssueResolution::class, 'reject'])->name('issue.reject');
        Route::resource('issues', CIssue::class);
        Route::resource('issue/resolution', CIssueResolution::class);
    });

    Route::post('profile/change-photo', [CProfile::class, 'changePhoto'])->name('profile.change-photo');
    Route::resource('profile', CProfile::class);
});
