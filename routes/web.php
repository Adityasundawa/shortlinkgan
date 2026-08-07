<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MicrositeLinkController;
use App\Http\Controllers\Admin\ShortedLinkController;
use App\Http\Controllers\BulkShortLinkController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DomainDecentralizeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectAnalyticController;
use App\Http\Controllers\ProjectAnalyticUserController;
use App\Http\Controllers\TeamManagementController;
use App\Http\Controllers\TemplateMicrositeController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|s
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/dashboard', '/seoadmin')->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('/seoadmin')->middleware(['auth'])->group(function () {

    // Dashboard Route
    Route::get('/', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/', [DashboardController::class, 'dashboard'])->name('admin.dashboard');

    // Team Management
    Route::prefix('/team-management')->middleware('role:admin')->group(function () {
        Route::get('/', [TeamManagementController::class, 'list'])->name('admin.list_team');

        Route::prefix('/new')->group(function () {
            Route::get('/', [TeamManagementController::class, 'create'])->name('admin.create_new_team');
            Route::post('createteam', [TeamManagementController::class, 'store'])->name('admin.store_new_team');
        });

        Route::get('/edit-team/{id}', [TeamManagementController::class, 'edit'])->name('admin.edit_team');
        Route::post('/edit-team/{id}', [TeamManagementController::class, 'update'])->name('admin.edit_team');
        Route::post('/delete-team/{id}', [TeamManagementController::class, 'delete'])->name('admin.delete_team');
    });

    // User Management
    Route::prefix('/user-management')->middleware('role:admin')->group(function () {
        Route::get('/', [UserManagementController::class, 'list'])->name('admin.list_user');

        Route::get('/new', [UserManagementController::class, 'create'])->name('admin.create_new_user');
        Route::post('/create', [UserManagementController::class, 'store'])->name('admin.store_new');
        Route::post('/check-duplicate', [UserManagementController::class, 'checkDuplicate'])->name('admin.check_duplicate');

        Route::get('/edit-user/{id}', [UserManagementController::class, 'edit'])->name('admin.edit_user');
        Route::post('/edit-user/{id}', [UserManagementController::class, 'update'])->name('admin.edit_user');
        Route::get('/delete-user/{id}', [UserManagementController::class, 'delete'])->name('admin.delete_user');
    });

    // Domain Decentralizeds
    Route::prefix('/domain-decentralized')->middleware('role:admin')->group(function () {
        Route::get('/', [DomainDecentralizeController::class, 'index'])->name('admin.domain_decentralized');
        Route::post('/store', [DomainDecentralizeController::class, 'store'])->name('admin.domain_decentralized.store');
        Route::post('/update', [DomainDecentralizeController::class, 'update'])->name('admin.domain_decentralized.update');
        Route::patch('/switch-status', [DomainDecentralizeController::class, 'switchStatus'])->name('admin.domain_decentralized.switchStatus');
        Route::get('/domain/{id}', [DomainDecentralizeController::class, 'detail'])->name('admin.domain_decentralized.detail');
        Route::delete('/delete', [DomainDecentralizeController::class, 'destroy'])->name('admin.domain_decentralized.delete');
    });

    // Project Analytic
    Route::prefix('/project-analytic')->group(function () {
        Route::get('/', [ProjectAnalyticController::class, 'list'])->name('project_analytic.list');
        Route::post('/', [ProjectAnalyticController::class, 'list'])->name('project_analytic.list');
        Route::patch('/update', [ProjectAnalyticController::class, 'update'])->name('project_analytic.udpate');
        Route::delete('/delete', [ProjectAnalyticController::class, 'destroy'])->name('project_analytic.delete');
        Route::get('/project/{project_id}/analytic', [ProjectAnalyticController::class, 'analytic'])->name('project_analytic.analytic');
        Route::get('/project/{project_id}/analytic/json', [ProjectAnalyticController::class, 'analyticJSON'])->name('project_analytic.analytic_json');
    });

    // Project Analytic User
    Route::prefix('/project-analytic-user')->group(function () {
        Route::get('/', [ProjectAnalyticUserController::class, 'list'])->name('user_project_analytic.list');
        Route::post('/', [ProjectAnalyticUserController::class, 'list'])->name('user_project_analytic.list');
        Route::patch('/update', [ProjectAnalyticUserController::class, 'update'])->name('user_project_analytic.udpate');
        Route::delete('/delete', [ProjectAnalyticUserController::class, 'destroy'])->name('user_project_analytic.delete');
        Route::get('/project/{project_id}/analytic', [ProjectAnalyticUserController::class, 'analytic'])->name('user_project_analytic.analytic');
        Route::get('/project/{project_id}/analytic/json', [ProjectAnalyticUserController::class, 'analyticJSON'])->name('user_project_analytic.analytic_json');
    });

    // Campaign / Short Link
    Route::prefix('/short-link')->group(function () {
        Route::get('/', [ShortedLinkController::class, 'shorted'])->name('view.shortedLink');
        Route::get('/statistic/{id}', [ShortedLinkController::class, 'shortedstatistic'])->name('view.shortedLink-statistic');
        Route::post('/update/{id}', [ShortedLinkController::class, 'update'])->name('view.update');
        Route::delete('/destroy', [ShortedLinkController::class, 'destroy'])->name('view.shortedLink-destroy');
        Route::get('/get-data/{id}', [ShortedLinkController::class, 'getDataById'])->name('view.get-dataById'); // JSON response
    });

    Route::prefix('/bulk-short-link')->group(function () {
        Route::get('/', [BulkShortLinkController::class, 'list'])->name('bulk.list');
        Route::post('/', [BulkShortLinkController::class, 'store'])->name('bulk.store');
    });

    // Dashboard Short Link
    Route::prefix('/dashboard-short-url')->group(function () {
        Route::post('/store', [DashboardController::class, 'store'])->name('admin.short.store');
        Route::post('/update', [DashboardController::class, 'update'])->name('admin.short.update');
        Route::get('/edit-meta/{id}', [DashboardController::class, 'editMeta'])->name('admin.short.editmeta');
        Route::post('/edit-meta/{id}', [DashboardController::class, 'updateMeta'])->name('admin.short.updatemeta');
    });

    Route::prefix('/')->group(function () {
        Route::resource('/campaign', CampaignController::class);
        Route::get('campaign/{project_id}/edit', [CampaignController::class, 'edit'])->name('campaign.editnew');
        Route::post('campaign/{project_id}/update', [CampaignController::class, 'update'])->name('campaign.update');

        Route::get('/campaign/analytic/{campaign}', [CampaignController::class, 'showAnalyticView'])
            ->name('campaign.analytic.view');

        // 2. RUTE API DATA JSON (Dipanggil oleh fetchAndUpdateData)
        // URL: /api/campaign/analytic/{campaign_id}/data
        Route::get('/api/campaign/analytic/{campaign}', [CampaignController::class, 'getAnalyticData'])
            ->name('campaign.analytic.data');

    });

    // Mircosite (Template like Linktree)
    Route::prefix('/microsite')->group(function () {
        Route::get('/', [MicrositeLinkController::class, 'index'])->name('admin.microsite');
        Route::post('/', [MicrositeLinkController::class, 'index'])->name('admin.microsite');

        Route::get('/create', [MicrositeLinkController::class, 'create'])->name('admin.microsite.create');
        Route::post('/create', [MicrositeLinkController::class, 'store'])->name('admin.microsite.create');

        Route::get('/create/microsite/{id}', [MicrositeLinkController::class, 'create_microsite'])->name('admin.microsite.create.microsite');

        Route::post('/get-preview', [TemplateMicrositeController::class, 'get_preview'])->name('ajax.get-preview');
        Route::post('/store-microsite', [MicrositeLinkController::class, 'store_microsite'])->name('ajax.store-microsite');

        Route::get('/get-data/{id}', [MicrositeLinkController::class, 'getDataById'])->name('ajax.getDataById');
        Route::post('/update-data', [MicrositeLinkController::class, 'update'])->name('ajax.updateData');
        Route::delete('/delete-profile-photo/{id}', [MicrositeLinkController::class, 'deleteProfilePhoto'])->name('delete.profile.photo');
        Route::delete('/delete-button/{id}', [MicrositeLinkController::class, 'deleteButton'])->name('deleteButton');
        Route::delete('/delete-popunder/{id}', [MicrositeLinkController::class, 'deletePopunderId'])->name('deletePopunderId');
        Route::delete('/delete', [MicrositeLinkController::class, 'destroy'])->name('microsite-destroy');
        Route::get('/analytics/{project_id}', [MicrositeLinkController::class, 'show'])->name('admin.microsite.analytics');

        Route::get('/project/{project_id}/microsite/json', [MicrositeLinkController::class, 'analyticJSON'])->name('microsite_analytic.analytic_json');
    });
});

require __DIR__.'/auth.php';
