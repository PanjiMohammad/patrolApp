<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Register
Route::get('/register', 'Auth\LoginController@newRegister')->name('register');
Route::post('/register', 'Auth\LoginController@postRegister')->name('post.newRegister');

// Login
Route::get('/', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/post-login', 'Auth\LoginController@postLogin')->name('post.newLogin');

// Forgot Password
Route::get('/forgot-password', 'Auth\LoginController@resetPassword')->name('forgotPassword');
Route::post('/post-reset-password', 'Auth\LoginController@postResetPassword')->name('postForgotPassword');
Route::post('/post-reset-password-security', 'Auth\LoginController@securityPostResetPassword')->name('security.postForgotPassword');

// Logout
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Auth::routes();
// Route::match(['get', 'post'], '/register', function () {
//     return redirect('/login');
// })->name('register');

// for admin
Route::group(['prefix' => 'administrator', 'middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/home/getEvents', 'HomeController@getEvents')->name('home.getEvents');

    // Admin -> Content Setting
    Route::get('/account-setting/{id}', 'HomeController@accountSetting')->name('user.acountSetting');
    Route::put('/post-account-setting', 'HomeController@postAccountSetting')->name('user.postAccountSetting');

    // Security
    Route::group(['prefix' => 'security'], function () {
        Route::get('/', 'SecurityController@index')->name('security.index');
        Route::get('/getDatatables', 'SecurityController@getDatatables')->name('security.getDatatables');
        Route::get('/add-security', 'SecurityController@create')->name('security.create');
        Route::post('/store-security', 'SecurityController@store')->name('security.store');
        Route::delete('/delete-security/{id}', 'SecurityController@destroy')->name('security.destroy');
        Route::get('/edit-security/{id}', 'SecurityController@edit')->name('security.edit');
        Route::put('/security/update-security', 'SecurityController@update')->name('security.update');
    });

    // Location
    Route::group(['prefix' => 'location'], function () {
        Route::get('/', 'LocationController@index')->name('location.index');
        Route::get('/getDatatables', 'LocationController@getDatatables')->name('location.getDatatables');
        Route::get('/add-location', 'LocationController@create')->name('location.create');
        Route::post('/store-location', 'LocationController@store')->name('location.store');
        Route::delete('/delete-location/{id}', 'LocationController@destroy')->name('location.destroy');
        Route::get('/edit-location/{id}', 'LocationController@edit')->name('location.edit');
        Route::put('/location/update-location', 'LocationController@update')->name('location.update');
        Route::get('/location-detail/{id}', 'LocationController@show')->name('location.detail'); 
    });

    // Schedule
    Route::group(['prefix' => 'schedule'], function () {
        Route::get('/', 'ScheduleController@index')->name('schedule.index');
        Route::get('/getDatatables', 'ScheduleController@getDatatables')->name('schedule.getDatatables');
        Route::get('/add-schedule', 'ScheduleController@create')->name('schedule.create');
        Route::post('/store-schedule', 'ScheduleController@store')->name('schedule.store');
        Route::delete('/delete-schedule/{id}', 'ScheduleController@destroy')->name('schedule.destroy');
        Route::get('/edit-schedule/{id}', 'ScheduleController@edit')->name('schedule.edit');
        Route::put('/schedule/update-schedule', 'ScheduleController@update')->name('schedule.update');
    });
    
    // Report
    Route::group(['prefix' => 'report'], function () {
        Route::get('/', 'ScheduleController@reportIndex')->name('report.index');
        Route::get('/getReportDatatables', 'ScheduleController@getReportDatatables')->name('report.getDatatables');
        Route::get('/{daterange}', 'ScheduleController@reportPdf')->name('report.report');
    });

    // Route::group(['prefix' => 'reports'], function() {
    //     Route::get('/order', 'HomeController@orderReport')->name('report.order');
    //     Route::get('/reportorder/{daterange}', 'OrderController@orderReportPdf')->name('report.order_pdf');
    //     Route::get('/return', 'OrderController@returnReport')->name('report.return');
    //     Route::get('/reportreturn/{daterange}', 'OrderController@returnReportPdf')->name('report.return_pdf');
    // });
});

// for security
Route::group(['prefix' => 'security', 'namespace' => 'Security', 'middleware' => 'security'], function() {
    
    // Admin -> Content Setting
    Route::get('/account-setting/{id}', 'SecurityController@accountSetting')->name('security.acountSetting');
    Route::put('/post-account-setting', 'SecurityController@postAccountSetting')->name('security.postAccountSetting');

    // dashboard
    Route::get('/dashboard', 'SecurityController@index')->name('security.dashboard');
    Route::get('/dashboard/getEvents', 'SecurityController@getEvents')->name('security.getEvents');
    
    // Admin -> Content Setting
    Route::get('/account-setting/{id}', 'SecurityController@accountSetting')->name('security.acountSetting');
    Route::put('/post-account-setting', 'SecurityController@postAccountSetting')->name('security.postAccountSetting');

    // Absensi
    Route::get('/absence', 'AbsenceController@index')->name('absence.index');
    Route::get('/getDatatables', 'AbsenceController@getDatatables')->name('absence.getDatatables');
    Route::get('/absence/{id}', 'AbsenceController@detail')->name('absence.detail');
    Route::post('/store-absence', 'AbsenceController@postAbsence')->name('absence.store');
    Route::get('/absence/incident-report/{id}', 'AbsenceController@detailNew')->name('absence.newDetail');
    Route::post('/store-indcident-report', 'AbsenceController@postNewAbsence')->name('absence.incidentReportStore');

    // Export Pdf
    Route::get('/report/{daterange}', 'AbsenceController@reportPdf')->name('absence.report');
    // Route::delete('/delete-absence/{id}', 'AbsenceController@destroy')->name('absence.destroy');
    // Route::get('/edit-absence/{id}', 'AbsenceController@edit')->name('absence.edit');
    // Route::put('/absence/update-absence', 'AbsenceController@update')->name('absence.update');
});
