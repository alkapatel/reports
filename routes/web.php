<?php
Route::model('user', 'App\Models\User');

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

Route::get('cron-task-notifications', 'admin\DailyReportController@cronTaskNotification');
Route::get('cron-general-daily-report', 'admin\DailyReportController@cronGeneral');
Route::get('cron-daily-report', 'admin\DailyReportController@cron');

Route::get('/daily-report', 'admin\DailyReportController@getReport')->name("daily_report");
//bopal members
	Route::post('members/check-otp-num', 'admin\MembersController@checkOtpNum');
	Route::get('members/otpform', 'admin\MembersController@otpForm');
	Route::post('members/check-mobile', 'admin\MembersController@checkMobile');

	Route::any('members/view', 'admin\MembersController@viewData');
	Route::any('members/edit/{id}', 'admin\MembersController@memberEdit')->name('members.memberEdit');
	Route::put('members/{member-update}', 'admin\MembersController@memberUpdate')->name('members.memberUpdate');
	Route::any('members/data', 'admin\MembersController@data')->name('members.data');
	
	Route::group(['middleware' => 'admin_auth'], function(){
    	Route::any('members/send-sms', 'admin\MembersController@sendSms')->name('members.send-sms');
    	Route::get('members/sms-form', 'admin\MembersController@SmsForm');    
	});

	Route::resource('members', 'admin\MembersController');
	Route::any('members-family/data', 'admin\FamilyMemberController@data')->name('members-family.data');
	Route::resource('members-family', 'admin\FamilyMemberController');

Route::get('clear-cache', function () {
	$exitCode = Artisan::call('cache:clear');
	$exitCode = Artisan::call('view:clear');
	$exitCode = Artisan::call('route:clear');
	$exitCode = Artisan::call('config:clear');
	$exitCode = Artisan::call('debugbar:clear');
	return ["status" => 1, "msg" => "Cache cleared successfully!"];
});

/***************    Admin routes  **********************************/
    
    Route::get('/', 'admin\AdminLoginController@getLogin')->name("admin_login");
    Route::get('login', 'admin\AdminLoginController@getLogin')->name("admin_login");
    Route::post('login', 'admin\AdminLoginController@postLogin')->name("check_admin_login");
    
    Route::group(['middleware' => 'admin_auth'], function(){

    Route::any('/email','admin\SendMailController@sentemail');
    Route::any('/emailsend','mailController@sentemail');

		
//Dashboard
        Route::get('logout', 'admin\AdminLoginController@getLogout')->name("logout");

        Route::get('dashboard', 'admin\AdminController@index')->name("admin_dashboard");
        Route::get('dashboard/calendar', 'admin\AdminController@getWorkingDays');

		Route::get('bank-details', 'admin\AdminController@bank_details');

        Route::get('change-password', 'admin\AdminController@changePassword')->name("change_password");
        Route::post('change-password', 'admin\AdminController@postChangePassword')->name("update_password");

        Route::get('profile', 'admin\AdminController@editProfile')->name("edit_profile");
        Route::post('profile', 'admin\AdminController@updateProfile')->name("update_profile");
        
//Users

    Route::any('user-types/data', 'admin\UserTypesController@data')->name('user-types.data');
    Route::resource('user-types', 'admin\UserTypesController');

    Route::any('users/data', 'admin\UsersController@data')->name('users.data');
    Route::resource('users', 'admin\UsersController');

	Route::any('user-logs/data', 'admin\AdminUserLogsController@data')->name('user-logs.data');
	Route::resource('user-logs', 'admin\AdminUserLogsController'); 

//Leaves	
    Route::any('leave-request/userData', 'admin\LeaveRequestController@userData')->name('leave-request.userData');
    Route::get('leave-request/leave-create', 'admin\LeaveRequestController@userCreate')->name('leave-request.userCreate');
    Route::post('leave-request/leave-store', 'admin\LeaveRequestController@userStore')->name('leave-request.userStore');

    Route::any('leave-request/status', 'admin\LeaveRequestController@changeStatus');
    Route::any('leave-request/data', 'admin\LeaveRequestController@data')->name('leave-request.data');
 	Route::resource('leave-request', 'admin\LeaveRequestController');

//Tasks
    Route::any('clients/data', 'admin\ClientsController@data')->name('clients.data');
    Route::resource('clients', 'admin\ClientsController');

    Route::any('client-users/data', 'admin\ClientUsersController@data')->name('client-users.data');
    Route::resource('client-users', 'admin\ClientUsersController');

	Route::post('projects/project-store', 'admin\ProjectsController@clientStore')->name('projects.clientStore');
	Route::any('projects/clientData', 'admin\ProjectsController@clientData')->name('projects.client.data');
 	Route::any('projects/data', 'admin\ProjectsController@data')->name('projects.data');
 	Route::resource('projects', 'admin\ProjectsController');
 	
	Route::any('tasks/clientData', 'admin\TasksController@clientData')->name('task.client.data');
    Route::any('tasks/userData', 'admin\TasksController@userData')->name('task.user.data');
    Route::any('tasks/view', 'admin\TasksController@viewData');
    Route::any('tasks/data', 'admin\TasksController@data')->name('tasks.data');
    Route::resource('tasks', 'admin\TasksController');
	
	Route::post('credentials/credential-store', 'admin\CredentialController@clientStore')->name('credentials.clientStore');
	Route::any('credentials/getusers', 'admin\CredentialController@getUsersList')->name('getUsersList');
	Route::get('credentials/download/{id}', 'admin\CredentialController@downloadFile');
    Route::any('credentials/view', 'admin\CredentialController@viewData');
	Route::any('credentialsClient/data', 'admin\CredentialController@clientData')->name('credential.client.data');
	Route::any('credentialsUser/data', 'admin\CredentialController@userData')->name('credentialsUser.data');
    Route::any('credentials/data', 'admin\CredentialController@data')->name('credentials.data');
    Route::resource('credentials', 'admin\CredentialController');
		
	Route::get('download-monthly-reports', 'admin\TasksController@getMonthlyReport');
    Route::any('download-monthly-reports/ReportDownload', 'admin\TasksController@DownloadMonthlyReport');
    Route::get('download-monthly-reports/ReportPreview', 'admin\TasksController@PreviewMonthlyReport');
	
	Route::any('estimated-tasks/userData', 'admin\EstimatedTaskController@userData')->name('estimated-tasks.user.data');
    Route::any('estimated-tasks/view', 'admin\EstimatedTaskController@viewData');
    Route::any('estimated-tasks/data', 'admin\EstimatedTaskController@data')->name('estimated-tasks.data');
    Route::resource('estimated-tasks', 'admin\EstimatedTaskController');
		
	Route::any('task-report/data', 'admin\UserTaskReportsController@data')->name('task-report.data');
    Route::resource('task-report', 'admin\UserTaskReportsController');

//Masters
	Route::any('admin-actions/data', 'admin\AdminActionController@data')->name('admin-actions.data');
	Route::resource('admin-actions', 'admin\AdminActionController');
		
	Route::get('sent-email/view/{id}', 'admin\EmailSentController@viewEmailData');
    Route::any('sent-email/data', 'admin\EmailSentController@data')->name('sent-email.data');
    Route::resource('sent-email', 'admin\EmailSentController');
	
    Route::get('salary_slip/view', 'admin\SalarySlipController@viewData');
	Route::any('salary_slip/download', 'admin\SalarySlipController@download_salary_slip');
    Route::any('salary_slip/userData', 'admin\SalarySlipController@userData')->name('slary_slip.userData');
    Route::any('salary_slip/data', 'admin\SalarySlipController@data')->name('salary_slip.data');
    Route::resource('salary_slip', 'admin\SalarySlipController');
    Route::any('getuserdetail', 'admin\SalarySlipController@getuserdetail')->name('getuserdetail');
		
	Route::any('holidays/data', 'admin\HolidaysController@data')->name('holidays.data');
    Route::resource('holidays', 'admin\HolidaysController');
		
	Route::any('expense/view', 'admin\ExpenseController@viewData');
    Route::get('expense/download/{id}', 'admin\ExpenseController@downloadFile');
    Route::any('expense/data', 'admin\ExpenseController@data')->name('expense.data');        
    Route::resource('expense', 'admin\ExpenseController');
		
	Route::get('invoices/view', 'admin\InvoicesController@viewData');
    Route::any('invoices/download', 'admin\InvoicesController@download_invoice');
	Route::any('invoices/clientData', 'admin\InvoicesController@clientData')->name('invoices.client.data');
    Route::any('invoices/data', 'admin\InvoicesController@data')->name('invoices.data');
    Route::resource('invoices', 'admin\InvoicesController');
    

//User Permission
	Route::any('modules/data', 'admin\AdminModulesController@data')->name('modules.data');
	Route::resource('modules', 'admin\AdminModulesController');
        
	Route::any('module-pages/data', 'admin\AdminModulePagesController@data')->name('module-pages.data');
	Route::resource('module-pages', 'admin\AdminModulePagesController');

	Route::get('user-type-rights', 'admin\AdminController@rights')->name("list-assign-rights");
	Route::post('user-type-rights', 'admin\AdminController@rights')->name("assign-rights");
    
    Route::get('users-documents/download/{id}', 'admin\DocumentController@downloadFile');
    Route::any('users-documents/data', 'admin\DocumentController@data')->name('users-documents.data');        
    Route::resource('users-documents', 'admin\DocumentController');     
    //Route::any('employee-document', 'admin\DocumentController@downloadFile'); 
		
//AppraisalForm
    Route::get('appraisal-form/{id}/view', 'admin\AppraisalFormController@viewData');
    Route::any('appraisal-form/data', 'admin\AppraisalFormController@data')->name('appraisal-form.data');
    Route::resource('appraisal-form', 'admin\AppraisalFormController');
//Members
    Route::any('sms-sent-log/data', 'admin\SmsSentLogController@data')->name('sms-sent-log.data');
    Route::resource('sms-sent-log', 'admin\SmsSentLogController');
    
	Route::any('member-logs/data', 'admin\MemberLogsController@data')->name('member-logs.data');
    Route::resource('member-logs', 'admin\MemberLogsController');
    });    
