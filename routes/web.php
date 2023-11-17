<?php

use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('dashboard')
    ->middleware(['auth', 'active', 'verified'])->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/getData', [DashboardController::class, 'getData'])->name('dashboard.getData');

        // Edit Profile User
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // User
        Route::get('/users', [UserController::class, 'index'])->name('user.table');
        Route::get('/users/add', [UserController::class, 'create'])->name('user.add');
        Route::post('/users/store', [UserController::class, 'store'])->name('user.store');
        Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::patch('/users/update/{id}', [UserController::class, 'update'])->name('user.update');
        Route::patch('/users/reset/{id}', [UserController::class, 'changePassword'])->name('user.changePassword');
        Route::get('/users/activate/{id}', [UserController::class, 'active'])->name('user.active');
        Route::get('/users/inActive/{id}', [UserController::class, 'hold'])->name('user.inActive');
        Route::get('/users/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');


        // students
        Route::get('/students/activate/{id}', [StudentController::class, 'active'])->name('students.active');
        Route::get('/students/inActive/{id}', [StudentController::class, 'hold'])->name('students.inActive');
        Route::get('/students/old', [StudentController::class, 'old'])->name('students.oldTable');
        Route::post('/students/getData', [StudentController::class, 'getData'])->name('students.getData');
        Route::get('/students/fillLastSPP', [StudentController::class, 'fillLastSPP'])->name('students.fillLastSPP');

        // discounts
        Route::get('/discounts/activate/{id}', [DiscountController::class, 'active'])->name('discount.active');
        Route::get('/discounts/inActive/{id}', [DiscountController::class, 'hold'])->name('discount.inActive');
        Route::post('/discounts/add/student', [DiscountController::class, 'addStudent'])->name('discount.addStudent');
        Route::get('/discounts/remove/student/{id}', [DiscountController::class, 'removeStudent'])->name('discount.removeStudent');

        // school year
        Route::get('/schoolYears/activate/{id}', [SchoolYearController::class, 'active'])->name('schoolYears.active');
        Route::get('/schoolYears/inActive/{id}', [SchoolYearController::class, 'hold'])->name('schoolYears.inActive');

        // transactions
        Route::get('/transactions/fillId', [TransactionController::class, 'fillId'])->name('transactions.fillId');
        Route::post('/transactions/pay', [TransactionController::class, 'pay'])->name('transactions.pay');
        Route::get('/transactions/{id}/details', [TransactionController::class, 'details'])->name('transactions.details');
        Route::get('/transactions/{id}/print', [TransactionController::class, 'print'])->name('transactions.print');

        // reports
        Route::post('/report/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::post('/report/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::post('/report/yearly', [ReportController::class, 'yearly'])->name('reports.yearly');
        Route::post('/report/schoolYear', [ReportController::class, 'schoolYear'])->name('reports.schoolYear');
        Route::post('/report/semester', [ReportController::class, 'semester'])->name('reports.semester');
        Route::post('/report/discount', [ReportController::class, 'discount'])->name('reports.discount');
        Route::post('/report/getData', [ReportController::class, 'getData'])->name('reports.getData');
        Route::post('/report/arrears', [ReportController::class, 'arrears'])->name('reports.arrears');
        Route::post('/report/getDataArrears', [ReportController::class, 'getDataArrears'])->name('reports.getDataArrears');
        Route::post('/report/getDataDiscount', [ReportController::class, 'getDataDiscount'])->name('reports.getDataDiscount');
        Route::post('/report/monthly/getData', [ReportController::class, 'getDiagramDataMonthly'])->name('reports.getDiagramDataMonthly');
        Route::post('/report/semester/getData', [ReportController::class, 'getDiagramDataSemester'])->name('reports.getDiagramDataSemester');
        Route::post('/report/yearly/getData', [ReportController::class, 'getDiagramDataYearly'])->name('reports.getDiagramDataYearly');
        Route::post('/report/schoolYear/getData', [ReportController::class, 'getDiagramDataSchoolYear'])->name('reports.getDiagramDataSchoolYear');

        // Print Report
        Route::post('/report/print/arrears', [ReportController::class, 'arrearsPrint'])->name('reports.printArrears');
        Route::post('/report/print/resume', [ReportController::class, 'resumePrint'])->name('reports.printResume');
        Route::post('/report/print/discount', [ReportController::class, 'resumeDiscount'])->name('reports.printDiscount');

        // broadcast
        Route::post('/broadcast/all', [BroadcastController::class, 'all'])->name('broadcast.all');

        // Resource routing
        Route::resources([
            'students' => StudentController::class,
            'discount' => DiscountController::class,
            'fees' => FeeController::class,
            'schoolYears' => SchoolYearController::class,
            'transactions' => TransactionController::class,
            'settings' => SettingController::class,
            'broadcast' => BroadcastController::class,
        ]);
    });
require __DIR__ . '/auth.php';
