<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\BioDataDownloadController;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\ResultsAndTranscriptsController;
use App\Http\Controllers\admin\StockController;
use App\Http\Controllers\Auth\CustomForgotPasswordController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\documentation\BaseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Parents\HomeController as ParentsHomeController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Student\ClassDelegateController;
use App\Http\Controllers\Student\HomeController as StudentHomeController;
use App\Http\Controllers\Teacher\ClassController;
use App\Http\Controllers\Teacher\HomeController as TeacherHomeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Transactions;
use App\Http\Resources\SubjectResource;
use App\Models\CampusDegree;
use App\Models\Resit;
use App\Models\StudentSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use \App\Models\Subjects;

Route::get('/clear', function () {
    echo Session::get('applocale');
    $clearcache = Artisan::call('cache:clear');
    echo "Cache cleared<br>";

    $clearview = Artisan::call('view:clear');
    echo "View cleared<br>";

    $clearconfig = Artisan::call('config:cache');
    echo "Config cleared<br>";

    $optimize = Artisan::call('optimize:clear');
    echo "Optimized app<br>";

});


Route::get('checks', function(){
    DB::exec('ALTER TABLE program_banks RENAME COLUMN school_unit_id TO school_units_id');
});


Route::get('publish_all', function(){
    DB::table('results')->update(['published'=>1]);
    return;
});

Route::get('promotion/class_target/{class_id}', [Homecontroller::class, 'class_target'])->name('promotion.class.target');
Route::get('set_local/{lang}', [Controller::class, 'set_local'])->name('lang.switch');

Route::get('payment-form',[TransactionController::class,'paymentForm'])->name('payment_form');
Route::post('make-payments',[TransactionController::class,'makePayments'])->name('make_payments');
Route::get('complete-transaction/{transaction_id}',[StudentHomeController::class,'complete_transaction'])->name('complete_transaction');
Route::get('failed-transaction/{transaction_id}',[StudentHomeController::class,'failed_transaction'])->name('failed_transaction');
// Route::get('get-transaction-status/{transaction_id}',[TransactionController::class,'getTransactionStatus'])->name('get_transaction_status');
Route::post('mtn-momo',[TransactionController::class,'mtnCallBack'])->name('mtn_callback');

Route::post('login', [CustomLoginController::class, 'login'])->name('login.submit');
Route::get('login', [CustomLoginController::class, 'showLoginForm'])->name('login');
Route::get('registration', [Controller::class, 'registration'])->name('registration');
Route::post('check_matricule', [Controller::class, 'check_matricule'])->name('check_matricule');
Route::post('createAccount', [Controller::class, 'createAccount'])->name('createAccount');
Route::post('logout', [CustomLoginController::class, 'logout'])->name('logout');
Route::get('create_parent', [CustomLoginController::class, 'create_parent'])->name('create_parent');
Route::post('create_parent', [CustomLoginController::class, 'save_parent'])->name('create_parent');

Route::post('reset_password_with_token/password/reset', [CustomForgotPasswordController::class, 'validatePasswordRequest'])->name('reset_password_without_token');
Route::get('reset_password_with_token/{token}/{email}', [CustomForgotPasswordController::class, 'resetForm'])->name('reset');
Route::post('reset_password_with_token', [CustomForgotPasswordController::class, 'resetPassword'])->name('reset_password_with_token');
Route::post('recover_username', [CustomForgotPasswordController::class, 'recover_username'])->name('recover_username');

Route::get('', 'WelcomeController@home');
Route::get('home', 'WelcomeController@home');

// Route::middleware('password_reset')->group(function(){
// });

// DOCUMENTATION MANAGEMENT ROUTES
Route::name('documentation.')->prefix('documentation')->middleware('isAdmin')->group(function(){
    Route::get('/', [BaseController::class, 'index'])->name('index');
    Route::get('/permission/{slug}', [BaseController::class, 'permission_root'])->name('permission_root');
    Route::get('/teacher/{slug}', [BaseController::class, 'teacher_index'])->name('teacher_index');
    Route::get('/show/{id}', [BaseController::class, 'show'])->name('show');
    Route::get('/create/{parent?}', [BaseController::class, 'create'])->name('create');
    Route::post('/create/{parent?}', [BaseController::class, 'store']);
    Route::get('/edit/{id}', [BaseController::class, 'edit'])->name('edit');
    Route::post('/edit/{id}', [BaseController::class, 'update']);
    Route::get('/destroy/{id}', [BaseController::class, 'destroy'])->name('destroy');
});
// END OF DOCUMENTATION MANAGEMENT ROUTES

Route::prefix('admin')->name('admin.')->middleware('isAdmin')->group(function () {

    Route::get('transcript/index', [\App\Http\Livewire\Admin\Transcript\Index::class, '__invoke'])->name('transcript.index');
    Route::get('transcript/results/{student}', [\App\Http\Livewire\Admin\Transcript\Results::class, '__invoke'])->name('transcript.results');

    Route::get('', 'Admin\HomeController@index')->name('home');
    Route::get('home', 'Admin\HomeController@index')->name('home');
    Route::get('background_image', 'Admin\HomeController@set_background_image')->name('set_background_image');
    Route::post('background_image', 'Admin\HomeController@save_background_image');
    Route::get('set_watermark', 'Admin\HomeController@set_watermark')->name('set_watermark');
    Route::post('set_watermark', 'Admin\HomeController@save_watermark');
    Route::get('course/date_line', 'Admin\HomeController@courses_date_line')->name('course.date_line');
    Route::post('course/date_line', 'Admin\HomeController@save_courses_date_line');
    Route::get('setayear', 'Admin\HomeController@setayear')->name('setayear');
    Route::post('setayear/{id}', 'Admin\HomeController@setAcademicYear')->name('createacademicyear');
    Route::get('setsemester', 'Admin\HomeController@setsemester')->name('setsemester');
    Route::post('setsemester/{id}', 'Admin\HomeController@postsemester')->name('postsemester');
    Route::post('setminsemesterfee/{id}', 'Admin\HomeController@postsemesterminfee')->name('postsemester.minfee');
    Route::get('setcontacts/{id?}', 'Admin\HomeController@school_contacts')->name('setcontacts');
    Route::post('setcontacts/{id?}', 'Admin\HomeController@save_school_contact');
    Route::get('dropcontacts/{id}', 'Admin\HomeController@drop_school_contacts')->name('dropcontacts');
    Route::get('deletebatch/{id}', 'Admin\HomeController@deletebatch')->name('deletebatch');
    Route::get('sections', 'Admin\ProgramController@sections')->name('sections');
    Route::get('sub_units_of/{id}', 'Admin\ProgramController@subunitsOf')->name('subunits');

    Route::get('sub_units/{parent_id}', 'Admin\ProgramController@index')->name('units.index');
    Route::get('new_units/{parent_id}', 'Admin\ProgramController@create')->name('units.create');
    Route::get('units/{parent_id}/edit', 'Admin\ProgramController@edit')->name('units.edit');
    Route::resource('units', 'Admin\ProgramController')->except(['index', 'create', 'edit']);
    Route::get('units/{program_level_id}/subjects', 'Admin\ProgramController@subjects')->name('units.subjects');
    Route::get('units/{program_level_id}/drop_level', 'Admin\ProgramController@_drop_program_level')->name('units.drop_level');
    Route::get('sections/{section_id}/subjects/{id}', 'Admin\ClassSubjectController@edit')->name('edit.class_subjects');
    Route::get('sections/{section_id}/subjects/{id}/delete', 'Admin\ClassSubjectController@delete')->name('delete.class_subjects');
    Route::put('sections/{section_id}/subjects/{id}', 'Admin\ClassSubjectController@update')->name('units.class_subjects.update');
    
    Route::get('sections/{section_id}/subjects/{id}/content', 'Admin\ProgramController@course_content')->name('course.content');
    Route::post('sections/{section_id}/subjects/{id}/content', 'Admin\ProgramController@save_course_content');

    Route::get('units/{parent_id}/subjects/manage', 'Admin\ProgramController@manageSubjects')->name('units.subjects.manage_class_subjects');
    Route::post('units/{parent_id}/subjects/manage', 'Admin\ProgramController@saveSubjects')->name('units.subjects.manage');

    Route::get('units/{parent_id}/student', 'Admin\ProgramController@students')->name('students.index');
    Route::get('students/inactive', 'Admin\ProgramController@inactive_students')->name('students.inactive');
    Route::get('student/sections', 'Admin\ProgramController@student_sections')->name('student.section');
    Route::get('student/{student_id}/section', 'Admin\ProgramController@change_student_section')->name('student.section.change');
    Route::post('student/{student_id}/section', 'Admin\ProgramController@update_student_section');
    
    Route::get('student_list/select/{filter?}', 'Admin\ProgramController@program_levels_list_index')->name('student.bulk.index');
    Route::get('student_list/bulk/{filter}/{item_id}/{year_id?}', 'Admin\ProgramController@bulk_program_levels_list')->name('student.bulk.list');
    Route::get('messages/bulk/{filter}/{item_id}/{recipients}/{year_id?}', 'Admin\ProgramController@bulk_message_notifications')->name('messages.bulk');
    Route::post('messages/bulk/{filter}/{item_id}/{recipients}/{year_id?}', 'Admin\ProgramController@bulk_message_notifications_save');
    Route::get('class_list/{year_id?}', 'Admin\ProgramController@program_levels_list')->name('class.list');
    Route::get('programs/assign_level', 'Admin\ProgramController@assign_program_level')->name('programs.set_levels');
    Route::post('programs/assign_level', 'Admin\ProgramController@store_program_level');
    Route::get('programs/{id}/levels', 'Admin\ProgramController@program_levels')->name('programs.levels');
    Route::get('programs/{id}/levels/{level_id}/add', 'Admin\ProgramController@add_program_level')->name('programs.levels.add');
    Route::get('programs/{id}/levels/{levle_id}/drop', 'Admin\ProgramController@drop_program_level')->name('programs.levels.drop');
    Route::get('programs/index', 'Admin\ProgramController@program_index')->name('programs.index');
    
    Route::get('banks/{id?}', 'Admin\FeesController@banks')->name('banks');
    Route::get('fee/fee_settings/{campus_id?}', 'Admin\FeesController@fee_settings')->name('fee_settings');
    Route::get('fee/fee_banks/{campus_id}/{program_id?}', 'Admin\FeesController@fee_banks')->name('fee_banks');
    Route::post('fee/fee_banks/{campus_id}/{program_id?}', 'Admin\FeesController@save_fee_banks');
    Route::post('banks/{id?}', 'Admin\FeesController@save_bank');
    Route::get('fee', 'Admin\FeesController@fee')->name('fee');
    Route::get('fee/situation', 'Admin\FeesController@fee_situation')->name('fee.situation');
    Route::get('rgfee/situation', 'Admin\FeesController@registration_fee_situation')->name('reg.fee.situation');
    Route::get('rgfee/situation/list', 'Admin\FeesController@registration_fee_situation_list')->name('reg.fee.situation.list');
    Route::get('fee/history/{student_id}', 'Admin\FeesController@fee_history')->name('fee.history');
    Route::get('fee/situation/list', 'Admin\FeesController@fee_situation_list')->name('fee.situation.list');
    Route::get('fee/fee_list', 'Admin\FeesController@fee_list');
    Route::get('print_fee', 'Admin\FeesController@printFee')->name('print_fee');
    Route::get('print_fee/{student_id}', 'Admin\FeesController@printStudentFee')->name('print_fee.student');
    Route::get('fee/classes', 'Admin\FeesController@classes')->name('fee.classes');
    Route::get('fee/drive', 'Admin\FeesController@drive')->name('fee.drive');
    Route::get('fee/drive/listing', 'Admin\FeesController@drive_listing')->name('fee.drive_listing');
    Route::get('fee/collect', 'Admin\FeesController@collect')->name('fee.collect');
    Route::get('fee/reg/collect', 'Admin\FeesController@collect_registration')->name('fee.registration.collect');
    Route::get('fee/daily_report', 'Admin\FeesController@daily_report')->name('fee.daily_report');
    Route::get('fee/{id}', 'Admin\FeesController@fee')->name('fee.list');
    Route::delete('fee/{id}', 'Admin\FeesController@delete')->name('fee.destroy');
    Route::get('fee/{class_id}/report', 'Admin\FeesController@report')->name('fee.report');
    Route::get('fee/{class_id}/student', 'Admin\FeesController@student')->name('fee.student');
    Route::get('import_fee/', 'Admin\FeesController@import')->name('import_fee');
    Route::post('import_fee/', 'Admin\FeesController@import_save');
    Route::post('fee/undo_import/{import_reference}', 'Admin\FeesController@import_undo')->name('fee.import.undo');

    Route::get('sections/{id}', 'Admin\PayIncomeController@getSections')->name('getSections');
    Route::get('classes/{id}', 'Admin\PayIncomeController@getClasses')->name('getClasses');
    Route::get('search/students/{name}', 'Admin\PayIncomeController@searchStudent')->name('searchStudent');
    Route::get('search/students/', 'Admin\PayIncomeController@get_searchStudent')->name('get_searchStudent');
    Route::get('search/users/', 'Admin\PayIncomeController@get_searchUser')->name('get_searchUser');

    Route::get('scholarships', 'Scholarship\ScholarshipController@index')->name('scholarship.index');
    Route::get('scholarship/create', 'Scholarship\ScholarshipController@create')->name('scholarship.create');
    Route::post('scholarships', 'Scholarship\ScholarshipController@store')->name('scholarship.store');
    Route::get('scholarships/students_eligible', 'Scholarship\UserScholarshipController@students_eligible')->name('scholarship.eligible');
    Route::post('scholarships/students/{id}/award', 'Scholarship\UserScholarshipController@store')->name('scholarship.award');
    Route::get('scholarships/students/{id}/award', 'Scholarship\UserScholarshipController@create')->name('scholarship.award.create');
    Route::get('scholarships/scholars', 'Scholarship\UserScholarshipController@index')->name('scholarship.awarded_students');
    Route::post('scholarships/scholars', 'Scholarship\UserScholarshipController@getScholarsPerYear')->name('scholarship.scholars');
    Route::post('scholarships/delete/{id}', 'Scholarship\UserScholarshipController@delete_scholarship')->name('scholarship.delete');
    Route::get('scholarships/{id}', 'Scholarship\ScholarshipController@show')->name('scholarship.show');
    Route::get('scholarships/{id}/edit', 'Scholarship\ScholarshipController@edit')->name('scholarship.edit');

    Route::put('scholarships/{id}/', 'Scholarship\ScholarshipController@update')->name('scholarship.update');

    Route::get('incomes', 'Admin\IncomeController@index')->name('income.index');
    Route::get('incomes/create', 'Admin\IncomeController@create')->name('income.create');
    Route::post('incomes', 'Admin\IncomeController@store')->name('income.store');
    Route::get('incomes/{id}/report', 'Admin\IncomeController@report')->name('income.report');
    Route::get('incomes/{id}/edit', 'Admin\IncomeController@edit')->name('income.edit');
    Route::put('incomes/{id}/', 'Admin\IncomeController@update')->name('income.update');
    Route::delete('incomes/{id}/delete', 'Admin\IncomeController@destroy')->name('income.destroy');
    Route::get('incomes/pay_income/create', 'Admin\PayIncomeController@create')->name('income.pay_income.create');
    Route::get('incomes/pay_income/create_cash', 'Admin\PayIncomeController@create_cash')->name('income.pay_income.create_cash');
    Route::get('incomes/pay_income/create_cash/save', 'Admin\PayIncomeController@save_create_cash')->name('income.pay_income.save_cash');
    Route::get('incomes/pay_income/collect/{class_id}/{student_id}', 'Admin\PayIncomeController@collect')->name('income.pay_income.collect');
    Route::get('incomes/{id}', 'Admin\IncomeController@show')->name('income.show');
    Route::post('incomes/collect_income/{class_id}/{student_id}', 'Admin\PayIncomeController@store')->name('pay_income.store');
    Route::get('incomes/paid_income/list', 'Admin\PayIncomeController@index')->name('pay_income.index');
    Route::post('incomes/paid_income/list', 'Admin\PayIncomeController@download');
    Route::get('incomes/{student_id}/paid_income/{pay_income_id}/delete', 'Admin\PayIncomeController@delete_income')->name('income.delete');
    Route::get('{student_id}/incomes/{pay_income_id}/print_reciept', 'Admin\PayIncomeController@print')->name('income.print_reciept');
    Route::post('incomes/pay_income/list', 'Admin\PayIncomeController@getPayIncomePerClassYear')->name('pay_income.per_year');


    Route::get('expenses', 'Admin\Expense\ExpenseController@index')->name('expense.index');
    Route::get('expenses/create', 'Admin\Expense\ExpenseController@create')->name('expense.create');
    Route::post('expenses', 'Admin\Expense\ExpenseController@store')->name('expense.store');
    Route::get('expenses/{id}', 'Admin\Expense\ExpenseController@show')->name('expense.show');
    Route::get('expenses/{id}/edit', 'Admin\Expense\ExpenseController@edit')->name('expense.edit');
    Route::put('expenses/{id}/', 'Admin\Expense\ExpenseController@update')->name('expense.update');
    Route::delete('expenses/{id}/delete', 'Admin\Expense\ExpenseController@destroy')->name('expense.destroy');

    Route::prefix('fee/{class_id}')->name('fee.')->group(function () {
        Route::resource('list', 'Admin\ListController');
    });
    Route::prefix("fee/{student_id}")->name('fee.student.')->group(function () {
        Route::resource('payments', 'Admin\PaymentController');
        Route::get('payments/{item_id}/print', 'Admin\PaymentController@print')->name('payments.print');
        Route::get('payments/reg/create', 'Admin\PaymentController@reg_create')->name('payments.reg.create');
        Route::post('payments/reg/create', 'Admin\PaymentController@reg_store');
    });


    Route::resource('subjects', 'Admin\SubjectController');
    Route::post('subjects/create/next', 'Admin\SubjectController@next')->name('courses.create_next');
    Route::get('subjects/create/{background}/{semester}', 'Admin\SubjectController@_create')->name('courses._create');
    Route::get('classmaster', 'Admin\UserController@classmaster')->name('users.classmaster');
    Route::post('classmaster', 'Admin\UserController@saveClassmaster')->name('users.classmaster');
    Route::delete('classmaster', 'Admin\UserController@deleteMaster')->name('users.classmaster');
    Route::get('classmaster/create', 'Admin\UserController@classmasterCreate')->name('users.classmaster.create');        
    

    Route::prefix('result')->name('result.')->group(function(){
        Route::get('settings', 'Admin\ResultSettingsController@index')->name('settings');
        Route::get('import', 'Admin\ResultController@import')->name('import');
        Route::post('import', 'Admin\ResultController@importPost')->name('import');
        Route::get('export', 'Admin\ResultController@export')->name('export');
        Route::post('export', 'Admin\ResultController@exportPost')->name('export');
        Route::get('report', 'Admin\ResultController@report')->name('report');
        Route::post('report', 'Admin\ResultController@report_show')->name('report.show');
        Route::get('individual_results', 'Admin\ResultController@individual_results')->name('individual_results');
        Route::get('individual_results/{student_id}/print', 'Admin\ResultController@print_individual_results')->name('individual_results.print');
        Route::get('class_results', 'Admin\ResultController@class_results')->name('class_results');
        Route::post('class_results', 'Admin\ResultController@class_results');
        Route::get('individual_results/instances/{searchValue}', 'Admin\ResultController@individual_instances')->name('individual.instances');
        Route::get('publishing', 'Admin\ResultController@result_publishing')->name('publishing');
        Route::get('publish/{year}/{semester}', 'Admin\ResultController@publish_results')->name('publish');
        Route::get('unpublish/{year}/{semester}', 'Admin\ResultController@unpublish_results')->name('unpublish');

        Route::post('store_results', 'Admin\ResultController@store_results')->name('store_result');
    
    
        // ADDED RESULT ROUTES FOR OFFLINE SYSTEM
        Route::prefix('ca')->name('ca.')->group(function(){
            Route::get('{class_id?}', 'Admin\ResultController@ca_result')->name('index');
            Route::get('{class_id}/{course_id}/import', 'Admin\ResultController@ca_import')->name('import');
            Route::post('{class_id}/{course_id}/import', 'Admin\ResultController@ca_import_save')->name('import.save');
            Route::get('{class_id}/{course_id}/fill', 'Admin\ResultController@ca_fill')->name('fill');
            Route::post('{class_id}/{course_id}/fill', 'Admin\ResultController@ca_fill_save')->name('fill');
            Route::get('set_dateline/{background_id?}', 'Admin\ResultSettingsController@setCaUploadLatestDate')->name('dateline.set');
            Route::post('set_dateline/{background_id?}', 'Admin\ResultSettingsController@saveCaUploadLatestDate')->name('dateline.set');
        });

        Route::prefix('exam')->name('exam.')->group(function(){
            Route::get('{class_id?}', 'Admin\ResultController@exam_result')->name('index');
            Route::get('{class_id}/{course_id}/import', 'Admin\ResultController@exam_import')->name('import');
            Route::post('{class_id}/{course_id}/import', 'Admin\ResultController@exam_import_save')->name('import');
            Route::get('{class_id}/{course_id}/fill', 'Admin\ResultController@exam_fill')->name('fill');
            Route::post('{class_id}/{course_id}/fill', 'Admin\ResultController@exam_fill_save')->name('fill');
            Route::get('set_dateline/{background_id?}', 'Admin\ResultSettingsController@setExamUploadLatestDate')->name('dateline.set');
            Route::post('set_dateline/{background_id?}', 'Admin\ResultSettingsController@saveExamUploadLatestDate')->name('dateline.set');
        });

        Route::prefix('coded')->name('coded.')->group(function(){
            Route::get('/', [Admin\CodedResultsController::class, 'index'])->name('index');
            // manage exam coding
            Route::get('{year_id}/{semester_id}/courses', [Admin\CodedResultsController::class, 'import_course_codes'])->name('courses');
            Route::post('{year_id}/{semester_id}/courses', [Admin\CodedResultsController::class, 'save_course_codes']);
            Route::get('{year_id}/{semester_id}/{course_id}', [Admin\CodedResultsController::class, 'course'])->name('course');
            Route::post('{year_id}/{semester_id}/{course_id}', [Admin\CodedResultsController::class, 'save_course_code']);
            Route::get('{year_id}/{semester_id}/{course_id}/drop', [Admin\CodedResultsController::class, 'drop_course_code'])->name('course.undo');
            Route::get('{year_id}/{semester_id}/{course_id}/students', [Admin\CodedResultsController::class, 'import_student_codes'])->name('students');
            Route::post('{year_id}/{semester_id}/{course_id}/students', [Admin\CodedResultsController::class, 'save_student_codes']);
            Route::post('{year_id}/{semester_id}/{course_id}/students/undo', [Admin\CodedResultsController::class, 'undo_student_code_import'])->name('students.undo');

            // CA only
            Route::get('{year_id}/{semester_id}/{course_id}/import/ca', [Admin\CodedResultsController::class, 'import_ca_only'])->name('course.import.ca');
            Route::post('{year_id}/{semester_id}/{course_id}/import/ca', [Admin\CodedResultsController::class, 'save_ca_only']);
            Route::get('{year_id}/{semester_id}/{course_id}/import/ca/undo/{class_id?}', [Admin\CodedResultsController::class, 'undo_ca_import'])->name('course.import.ca.undo');

            // normal EXAM import
            Route::get('{year_id}/{semester_id}/{course_id}/exam/import', [Admin\CodedResultsController::class, 'import_exam'])->name('course.import.exam');
            Route::post('{year_id}/{semester_id}/{course_id}/exam/import', [Admin\CodedResultsController::class, 'save_exam']);
            Route::post('{year_id}/{semester_id}/{course_id}/exam/import/undo', [Admin\CodedResultsController::class, 'undo_exam_import'])->name('course.import.exam.undo');

            // coded EXAM import
            Route::get('{year_id}/{semester_id}/{course_id}/exam/coded/import', [Admin\CodedResultsController::class, 'import_coded_exam'])->name('course.import.exam.coded');
            Route::post('{year_id}/{semester_id}/{course_id}/exam/coded/import', [Admin\CodedResultsController::class, 'save_coded_exam']);
            Route::get('{year_id}/{semester_id}/{course_id}/exam/coded/import/undo/{class_id?}', [Admin\CodedResultsController::class, 'undo_coded_exam_import'])->name('course.import.exam.coded.undo');
            
        });

        Route::get('imports', 'Admin\ResultController@imports_index')->name('imports');
        // END OF ADDED RESULT ROUTES FOR OFFLINE SYSTEM
    });

    Route::get('users/{user_id}/subjects', 'Admin\UserController@createSubject')->name('users.subjects.add');
    Route::delete('users/{user_id}/subjects', 'Admin\UserController@dropSubject')->name('users.subjects.drop');
    Route::post('users/{user_id}/subjects', 'Admin\UserController@saveSubject')->name('users.subjects.save');

    Route::get('users/{user_id}/subjects/{subject_id}/content/{content_id?}', 'Admin\SubjectController@course_content')->name('users.content.index');
    Route::post('users/{user_id}/subjects/{subject_id}/content/{content_id?}', 'Admin\SubjectController@save_course_content');
    Route::get('users/{user_id}/subjects/{subject_id}/content/{content_id}/edit', 'Admin\SubjectController@edit_course_content')->name('users.content.edit');
    Route::post('users/{user_id}/subjects/{subject_id}/content/{content_id}/edit', 'Admin\SubjectController@update_course_content');
    Route::get('users/{user_id}/subjects/{subject_id}/content/{content_id}/delete', 'Admin\SubjectController@delete_course_content')->name('users.content.delete');

    Route::get('users/search', [Controller::class, 'search_user'])->name('users.search');
    Route::prefix('users/wages')->name('users.wages.')->group(function(){
        Route::get('', [AdminHomeController::class, 'wages'])->name('index');
        Route::get('create/{teacher_id}', [AdminHomeController::class, 'create_wages'])->name('create');
        Route::post('create/{teacher_id}', [AdminHomeController::class, 'save_wages']);
        Route::get('drop/{teacher_id}/{wage_id}/drop', [AdminHomeController::class, 'drop_wages'])->name('drop');
    });
    Route::get('users/password/reset', [AdminHomeController::class, 'abs_password_reset'])->name('users.reset_password');
    Route::post('users/password/reset', [AdminHomeController::class, 'abs_reset_password']);


    Route::resource('users', 'Admin\UserController');
    Route::get('students/init_promotion', 'Admin\StudentController@initialisePromotion')->name('students.init_promotion');
    Route::get('students/promotion', 'Admin\StudentController@promotion')->name('students.promotion');
    Route::post('students/promote', 'Admin\StudentController@promote')->name('students.promote');
    Route::get('students/promotion/approve/{promotion_id?}', 'Admin\StudentController@trigger_approval')->name('students.trigger_approval');
    Route::post('students/promotion/approve', 'Admin\StudentController@approvePromotion')->name('students.approve_promotion');
    Route::get('students/promotion/cancelPromotion/{promotion_id}', 'Admin\StudentController@cencelPromotion')->name('students.cancel_promotion');
    Route::get('students/promotions/{program_id?}', 'Admin\StudentController@promotion_history')->name('students.promotions');
    Route::get('students/init_demotion', 'Admin\StudentController@initialiseDemotion')->name('students.init_demotion');
    Route::get('students/demotion', 'Admin\StudentController@demotion')->name('students.demotion');
    Route::get('students/demote/{promotion_id}', 'Admin\StudentController@demote')->name('students.demote');
    Route::get('demotion_target/{id}', 'Admin\StudentController@unitDemoteTarget')->name('demotion_target');
    Route::get('promotion_target/{id}', 'Admin\StudentController@unitTarget')->name('promotion_target');
    Route::get('promotion_batch/{id}', 'Admin\StudentController@promotionBatch')->name('promotion_batch');
    // Custom promotion and repeat
    Route::get('students/custom_promotion', 'Admin\StudentController@custom_promotion')->name('students.custom_promotion');
    // Route::post('students/custom_promotion', 'Admin\StudentController@custom_promote');
    Route::get('students/repeat', 'Admin\StudentController@repeat_students')->name('students.repeat_students');
    Route::post('students/repeat', 'Admin\StudentController@repeat_students_save');


    Route::get('students/import', 'Admin\StudentController@import')->name('students.import');
    Route::post('students/import', 'Admin\StudentController@importPost')->name('students.import');
    Route::post('students/clear', 'Admin\StudentController@clearStudents')->name('students.clear');
    Route::post('student/matricule/prefix/{program_id}/{level_id}', 'Admin\ProgramController@storeStudentMatricPrefix')->name('students.matricule.prefix');
    Route::get('student/matricule', 'Admin\StudentController@matric')->name('students.matricule');
    Route::post('student/matricule', 'Admin\StudentController@matricPost')->name('students.matricule');
    Route::post('student/{id}/password/reset', 'Admin\StudentController@reset_password')->name('student.password.reset');
    Route::resource('student', 'Admin\StudentController');
    Route::post('students', 'Admin\StudentController@getStudentsPerClass')->name('getStudent.perClassYear');
    Route::get('result/bypass/{student_id?}', 'Admin\StudentController@studentResultBypass')->name('result.bypass');
    Route::get('result/bypass/cancel/{id}', 'Admin\StudentController@cancelResultBypass')->name('result.bypass.cancel');
    Route::get('result/bypass/{student_id}/set', 'Admin\StudentController@setStudentResultBypass')->name('result.bypass.set');
    Route::get('student/change_status/{student_id}', 'Admin\StudentController@change_status')->name('student.change_status');
    Route::resource('result_release', 'Admin\ResultController');


    Route::get('boarding_fee/create', 'Admin\BoardingFeeController@create')->name('boarding_fee');
    Route::post('boarding_fee', 'Admin\BoardingFeeController@store')->name('boarding_fee.store');
    // Route::post('boarding_fee/{id}/installments', 'Admin\BoardingFeeController@addInstallments')->name('boarding_fee.installments.store');
    // Route::get('boarding_fee/{id}/installments/{installment_id}', 'Admin\BoardingFeeController@editBoardingPaymentInstallment')->name('boarding_fee.installments.edit');
    // Route::put('boarding_fee/{id}/installments/{installment_id}', 'Admin\BoardingFeeController@updateBoardingPaymentInstallment')->name('boarding_fee.installments.update');
    // Route::delete('boarding_fee/{id}/installments/{installment_id}', 'Admin\BoardingFeeController@deleteBoardingPaymentInstallment')->name('boarding_fee.installments.destroy');
    Route::get('boarding_fee', 'Admin\BoardingFeeController@index')->name('boarding_fee.index');
    Route::get('boarding_fee/{id}/edit', 'Admin\BoardingFeeController@edit')->name('boarding_fee.edit');
    Route::get('boarding_fee/{id}/installments', 'Admin\BoardingFeeController@createInstallments')->name('boarding_fee.installments');
    Route::put('boarding_fee/{id}', 'Admin\BoardingFeeController@update')->name('boarding_fee.update');
    Route::delete('boarding_fee/{id}', 'Admin\BoardingFeeController@destroy')->name('boarding_fee.destroy');
    Route::get('total_boarding_fee/{id}/',  'Admin\CollectBoardingFeeController@totalBoardingAmount')->name('getTotalBoardingAmount');
    Route::get('sub-units/{parent_id}','Admin\ProgramController@getSubUnits')->name('getSubUnits');


    Route::get('collect/boarding_fee/{class_id}/{student_id}', 'Admin\CollectBoardingFeeController@collect')->name('collect_boarding_fee.collect');
    Route::get('collect/boarding_fee', 'Admin\CollectBoardingFeeController@create')->name('collect_boarding_fee.create');
    Route::post('collect/boarding_fee/{class_id}/{student_id}', 'Admin\CollectBoardingFeeController@store')->name('collect_boarding_fee.store');
    Route::get('collected/boarding_fees/', 'Admin\CollectBoardingFeeController@index')->name('collect_boarding_fee.index');
    Route::get('collected/boarding_fees/{student_id}/{id}/edit', 'Admin\CollectBoardingFeeController@edit')->name('collect_boarding_fee.edit');
    Route::put('collected/boarding_fees/{student_id}/{id}', 'Admin\CollectBoardingFeeController@update')->name('collect_boarding_fee.update');
    Route::get('collected/boarding_fees/{student_id}/{id}', 'Admin\CollectBoardingFeeController@show')->name('collect_boarding_fee.show');
    Route::post('collected/boarding_fees', 'Admin\CollectBoardingFeeController@getBoardingFeePerYear')->name('boarding_fees_year');
    Route::post('collect/boarding_fees/{student_id}/{id}', 'Admin\CollectBoardingFeeController@collectBoardingFeeDetails')->name('boarding_fees_details');
    Route::get('students/{student_id}/boarding_fees/{id}/print', 'Admin\CollectBoardingFeeController@printBoardingFee')->name('boarding_fee.print');


    Route::resource('roles','Admin\RolesController');
    Route::get('permissions', 'Admin\RolesController@permissions')->name('roles.permissions');
    Route::get('assign_role', 'Admin\RolesController@rolesView')->name('roles.assign');
    Route::post('assign_role', 'Admin\RolesController@rolesStore')->name('roles.assign.post');
    Route::post('roles/destroy/{id}', 'Admin\RolesController@destroy')->name('roles.destroy');
    Route::get('school/debts', 'Admin\SchoolDebtsController@index')->name('debts.schoolDebts');
    Route::post('school/debts', 'Admin\SchoolDebtsController@getStudentsWithDebts')->name('debts.getStudentWithDebts');
    Route::get('school/debts/{id}', 'Admin\SchoolDebtsController@getStudentDebts')->name('debts.showDebts');
    Route::post('school/debts/{id}', 'Admin\SchoolDebtsController@collectStudentDebts')->name('debts.collectDebts');
    

    Route::get('course_registration/date_line/{campus}/{semester}', 'Admin\HomeController@course_date_line')->name('courses.registration.date_line');
    Route::get('programs/settings', 'Admin\HomeController@program_settings')->name('program_settings');
    Route::post('programs/settings', 'Admin\HomeController@post_program_settings');
    Route::get('custom_resit/create/{background_id?}', 'Admin\HomeController@custom_resit_create')->name('custom_resit.create');
    Route::post('custom_resit/create/{background_id?}', 'Admin\HomeController@custom_resit_save');
    Route::get('custom_resit/edit/{id}', 'Admin\HomeController@custom_resit_edit')->name('custom_resit.edit');
    Route::post('custom_resit/edit/{id}', 'Admin\HomeController@custom_resit_update');
    Route::get('custom_resit/delete/{id}', 'Admin\HomeController@custom_resit_delete')->name('custom_resit.delete');
    
    Route::get('periods', 'Admin\HomeController@course_periods')->name('periods');
    Route::post('periods', 'Admin\HomeController@save_course_periods');
    Route::get('periods/{period_id}', 'Admin\HomeController@edit_course_periods')->name('periods.edit');
    Route::post('periods/{period_id}', 'Admin\HomeController@update_course_periods');
    Route::get('periods/delete/{period_id}', 'Admin\HomeController@delete_course_period')->name('periods.delete');


    Route::prefix('statistics')->name('stats.')->group(function(){
        Route::get('sudents', 'Admin\StatisticsController@students')->name('students');
        Route::get('fees', 'Admin\StatisticsController@fees')->name('fees');
        Route::get('reg/fees', 'Admin\StatisticsController@registration_fees')->name('regfees');
        Route::get('results', 'Admin\StatisticsController@results')->name('results');
        Route::get('income', 'Admin\StatisticsController@income')->name('income');
        Route::get('expenditure', 'Admin\StatisticsController@expenditure')->name('expenditure');
        Route::get('fees/{class_id}', 'Admin\StatisticsController@unitFees')->name('unit-fees');
        Route::get('ie_report', 'Admin\StatisticsController@ie_report')->name('ie_report');
        Route::get('ie_report/monthly', 'Admin\StatisticsController@ie_monthly_report')->name('ie.report');
    });
    Route::prefix('campuses')->name('campuses.')->group(function(){
        Route::get('/', 'Admin\CampusesController@index')->name('index');
        Route::get('/create', 'Admin\CampusesController@create')->name('create');
        Route::get('/edit/{id}', 'Admin\CampusesController@edit')->name('edit');
        Route::post('/store', 'Admin\CampusesController@store')->name('store');
        Route::post('/update/{id}', 'Admin\CampusesController@update')->name('update');
        Route::get('/update/{id}', 'Admin\CampusesController@delete')->name('delete');
        Route::get('/{id}/programs', 'Admin\CampusesController@programs')->name('programs');
        Route::get('/{id}/programs/{program_id}/set_fee', 'Admin\CampusesController@set_program_fee')->name('set_fee');
        Route::get('/{id}/programs/{program_id}/add', 'Admin\CampusesController@add_program')->name('add_program');
        Route::get('/{id}/programs/{program_id}/drop', 'Admin\CampusesController@drop_program')->name('drop_program');
        Route::post('/{id}/programs/{program_id}/set_fee', 'Admin\CampusesController@save_program_fee');
    });
    Route::prefix('schools')->name('schools.')->group(function(){
        Route::get('/', 'Admin\SchoolsController@index')->name('index');
        Route::get('/create', 'Admin\SchoolsController@create')->name('create');
        Route::get('/edit/{id}', 'Admin\SchoolsController@edit')->name('edit');
        Route::get('/preview/{id}', 'Admin\SchoolsController@preview')->name('preview');
        Route::post('/store', 'Admin\SchoolsController@store')->name('store');
        Route::post('/update/{id}', 'Admin\SchoolsController@update')->name('update');
        Route::get('/update/{id}', 'Admin\SchoolsController@delete')->name('delete');
    });
    
    Route::get('grading/set_type/{program_id}', 'Admin\ProgramController@set_program_grading_type')->name('grading.set_type');
    Route::post('grading/set_type/{program_id}', 'Admin\ProgramController@save_program_grading_type')->name('grading.post_type');

    Route::prefix('semesters')->name('semesters.')->group(function(){
        Route::get('{program_id}', 'Admin\ProgramController@semesters')->name('index');
        Route::get('create/{program_id}', 'Admin\ProgramController@create_semester')->name('create');
        Route::get('edit/{program_id}/{id}', 'Admin\ProgramController@edit_semester')->name('edit');
        Route::get('delete/{id}', 'Admin\ProgramController@delete_semester')->name('delete');
        Route::post('store/{program_id}', 'Admin\ProgramController@store_semester')->name('store');
        Route::post('update/{program_id}/{id}', 'Admin\ProgramController@update')->name('update');
        Route::get('set_type/{program_id}', 'Admin\ProgramController@set_program_semester_type')->name('set_type');
        Route::post('set_type/{program_id}', 'Admin\ProgramController@post_program_semester_type');
        Route::get('result/set_datelines/{semester_id}', 'Admin\ProgramController@set_result_datelines')->name('result.set_datelines');
        Route::post('result/set_datelines/{semester_id}', 'Admin\ProgramController@set_result_datelines_save');
    });

    Route::prefix('imports')->name('imports.')->group(function(){
        Route::get('import_ca', 'Admin\ImportCenter@import_ca')->name('import_ca');
        Route::post('import_ca', 'Admin\ImportCenter@import_ca_save');
        Route::get('clear_ca', 'Admin\ImportCenter@clear_ca')->name('clear_ca');
        Route::post('clear_ca', 'Admin\ImportCenter@clear_ca_save');
        Route::get('import_exam', 'Admin\ImportCenter@import_exam')->name('import_exam');
        Route::post('import_exam', 'Admin\ImportCenter@import_exam_save');
        Route::get('clear_exam', 'Admin\ImportCenter@clear_exam')->name('clear_exam');
        Route::post('clear_exam', 'Admin\ImportCenter@clear_exam_save');
        Route::get('clear_fee', 'Admin\ImportCenter@clear_fee')->name('clear_fee');
        Route::post('clear_fee', 'Admin\ImportCenter@clear_fee_save');
    });

    
    
    Route::name('stock.')->prefix('stock')->group(function(){
        Route::get('/', 'Admin\StockController@index')->name('index');
        Route::get('/create', 'Admin\StockController@create')->name('create');
        Route::get('/save', 'Admin\StockController@save')->name('save');
        Route::get('/report/{id}/{year?}', 'Admin\StockController@report')->name('report');
        Route::get('/report/{id}/{year}/print', 'Admin\StockController@print_report')->name('report.print');
        Route::get('/edit/{id}', 'Admin\StockController@edit')->name('edit');
        Route::get('/update/{id}', 'Admin\StockController@update')->name('update');
        Route::get('/receive/{id}', 'Admin\StockController@receive')->name('receive');
        Route::get('/receive/{id}/cancel', 'Admin\StockController@cancel_receive')->name('cancel_receive');
        Route::get('/accept/{id}', 'Admin\StockController@accept')->name('accept');
        Route::get('/share/{id}', 'Admin\StockController@send')->name('share');
        Route::get('/share/{id}/cancel', 'Admin\StockController@cancel_send')->name('cancel_share');
        Route::get('/send/{id}', 'Admin\StockController@__send')->name('send');
        Route::get('/delete/{id}', 'Admin\StockController@delete')->name('delete');
        Route::name('campus.')->prefix('/campus/{campus_id}')->group(function(){
            Route::get('/index', 'Admin\StockController@campus_index')->name('index');
            Route::get('/receive/{id}', 'Admin\StockController@campus_receive')->name('receive');
            Route::get('/accept/{id}', 'Admin\StockController@campus_accept')->name('accept');
            Route::get('/giveout/{id}', 'Admin\StockController@campus_giveout')->name('giveout');
            Route::get('/give/{id}', 'Admin\StockController@post_campus_giveout')->name('give');
            Route::get('/restore/{id}', 'Admin\StockController@restore')->name('restore');
            Route::get('/report/{id}', 'Admin\StockController@campus_report')->name('report');
            Route::get('/return/{id}', 'Admin\StockController@__restore')->name('return');
            Route::get('/student_stock/delete/{id}', 'Admin\StockController@delete_student_stock')->name('student_stock.delete');
            Route::get('/givable/report', 'Admin\StockController@campus_givable_report')->name('givable.report');
            Route::get('/receivable/report', 'Admin\StockController@campus_receivable_report')->name('receivable.report');
        });
    });

    Route::get('extra-fee/{student_id?}', [AdminHomeController::class, 'extraFee'])->name('extra-fee');
    Route::get('extra-fee/{student_id}/save', [AdminHomeController::class, 'extraFeeSave'])->name('extra-fee.save');
    Route::get('extra-fee/{student_id}/destroy/{extra_fee_id}', [AdminHomeController::class, 'extraDestroy'])->name('extra-fee.destroy');
    Route::get('set_letter_head', [AdminHomeController::class, 'set_letter_head'])->name('set_letter_head');
    Route::post('set_letter_head/save', [AdminHomeController::class, 'save_letter_head'])->name('save_letter_head');


    
    
    // ROUTES FOR RESULT OPERATIONS
    Route::prefix('res_and_trans')->name('res_and_trans.')->group(function () {
        Route::post('spr_sheet', [ResultsAndTranscriptsController::class, 'spread_sheet'])->name('spr_sheet');
        Route::post('fre_dis', [ResultsAndTranscriptsController::class, 'frequency_distribution'])->name('fre_dis');
        Route::post('ca_only', [ResultsAndTranscriptsController::class, 'ca_only'])->name('ca_only');
        Route::post('passfail_report', [ResultsAndTranscriptsController::class, 'passfail_report'])->name('passfail_report');
        Route::post('sem_res_report', [ResultsAndTranscriptsController::class, 'semester_results_report'])->name('sem_res_report');
        Route::post('grd_sheet', [ResultsAndTranscriptsController::class, 'grades_sheet'])->name('grd_sheet');
        Route::name('transcripts.')->prefix('transcripts')->group(function () {
            Route::get('config', [ResultsAndTranscriptsController::class, 'configure_transcript'])->name('config');
            Route::post('config', [ResultsAndTranscriptsController::class, 'configure_save_transcript']);
            Route::get('edit_config/{id}', [ResultsAndTranscriptsController::class, 'configure_edit_transcript'])->name('config.edit');
            Route::post('update_config/{id}', [ResultsAndTranscriptsController::class, 'configure_update_transcript']);
            Route::get('delete_config/{id}', [ResultsAndTranscriptsController::class, 'configure_delete_transcript'])->name('config.delete');
            Route::get('completed', [ResultsAndTranscriptsController::class, 'completed_transcripts'])->name('completed');
            Route::get('pending', [ResultsAndTranscriptsController::class, 'pending_transcripts'])->name('pending');
            Route::get('undone', [ResultsAndTranscriptsController::class, 'undone_transcripts'])->name('undone');
            Route::get('set_done/{id}', [ResultsAndTranscriptsController::class, 'set_done_transcripts'])->name('set_done');
        });
    });

    Route::prefix('resits')->name('resits.')->group(function(){
        Route::get('index', [AdminHomeController::class, 'resits_index'])->name('index');
        Route::get('course_list/{resit_id}', [AdminHomeController::class, 'resit_course_list'])->name('course_list');
        Route::get('course_list/{resit_id}/{subject_id}/download', [AdminHomeController::class, 'resit_course_list_download'])->name('course_list.download');
    });

    Route::get('reset_password', 'Controller@reset_password')->name('reset_password');
    Route::post('reset_password', 'Controller@reset_password_save')->name('reset_password');
    
    Route::get('charges/set', 'Admin\HomeController@set_charges')->name('charges.set');
    Route::post('charges/set', 'Admin\HomeController@save_charges')->name('charges.save');
    
    Route::get('user/block/{user_id}', 'Admin\HomeController@block_user')->name('block_user');
    Route::get('user/activate/{user_id}', 'Admin\HomeController@activate_user')->name('activate_user');

    // ATTENDANCE ROUTE GROUP
    Route::name('attendance.')->prefix('attendance')->group(function(){
        // recording attendance
        Route::get('teachers/init_attendance', [AttendanceController::class, 'init_teacher_attendance'])->name('teacher.init');
        Route::get('teachers/take_attence/{matric}/{subject_id}', [AttendanceController::class, 'take_teacher_attendance'])->name('teacher.record');
        Route::post('teachers/take_attence/{matric}/{subject_id}', [AttendanceController::class, 'save_teacher_attendance'])->name('teacher.record');
        Route::get('teachers/subjects/{matric}', [AttendanceController::class, 'teacher_subjects'])->name('teacher.subjects');
        Route::get('teachers/attendance/{attendance_id}/checkout', [AttendanceController::class, 'checkout_teacher'])->name('teacher.checkout');
        Route::post('teachers/attendance/{attendance_id}/checkout', [AttendanceController::class, 'save_checkout_teacher']);
        Route::get('teachers/attendance/{attendance_id}/delete', [AttendanceController::class, 'delete_teacher_attendance'])->name('teacher.drop');

        // Attendance reporting
        Route::get('teachers/report/{type}/{campus_id?}/{teacher_id?}', [AttendanceController::class, 'attendance_report'])->name('report');
        Route::get('teachers/report/{type}/{campus_id}/{teacher_id}/print', [AttendanceController::class, 'attendance_report'])->name('report.print');
    });

    Route::prefix('bio_data')->name('bio_data.')->group(function(){
        Route::get("download", [BioDataDownloadController::class, 'initialize'])->name('download');
        Route::post("download", [BioDataDownloadController::class, 'download']);
    });

    Route::prefix('headOfSchools')->name('headOfSchools.')->group(function(){
        Route::get('users', [Admin\HeadOfSchoolController::class, 'users'])->name('users');
        Route::get('index', [Admin\HeadOfSchoolController::class, 'schools'])->name('index');
        Route::get('departments/{school_id}', [Admin\HeadOfSchoolController::class, 'departments'])->name('departments');
        Route::get('programs/{school_id}/{department_id?}', [Admin\HeadOfSchoolController::class, 'programs'])->name('programs');
        Route::get('classes/{school_id}/{department_id?}/{program_id?}', [Admin\HeadOfSchoolController::class, 'classes'])->name('classes');
        Route::get('students/{school_id}/{level_id?}', [Admin\HeadOfSchoolController::class, 'school_students'])->name('students');
        Route::get('department/students/{department_id}/{level_id?}', [Admin\HeadOfSchoolController::class, 'department_students'])->name('department.students');
        Route::get('program/students/{program_id}/{level_id?}', [Admin\HeadOfSchoolController::class, 'program_students'])->name('program.students');
        Route::get('class/students/{class_id}', [Admin\HeadOfSchoolController::class, 'class_students'])->name('class.students');
        Route::get('setStatus/{hos_id}/{status}', [Admin\HeadOfSchoolController::class, 'set_status'])->name('setStatus');
        Route::get('delete/{hos_id}', [Admin\HeadOfSchoolController::class, 'delete'])->name('delete');
        Route::get('create', [Admin\HeadOfSchoolController::class, 'create'])->name('create');
        Route::post('create', [Admin\HeadOfSchoolController::class, 'save']);

    });

    Route::prefix('delegates')->name('delegates.')->group(function(){
        Route::get('/', [Admin\ClassDelegateController::class, 'index'])->name('index');
        Route::get('create', [Admin\ClassDelegateController::class, 'create'])->name('create');
        Route::post('/create', [Admin\ClassDelegateController::class, 'store']);
        Route::post('/update/{delegate_id}', [Admin\ClassDelegateController::class, 'update'])->name('update');
        Route::post('/change_student/{delegate_id}', [Admin\ClassDelegateController::class, 'change_student'])->name('change_student');
    });

    Route::name('dep_courses.')->prefix('dep_courses')->group(function(){
        Route::get('', [Admin\DepartmentalCourseController::class, 'index'])->name('index');
        Route::get('{dep_id}', [Admin\DepartmentalCourseController::class, 'courses'])->name('courses');
        Route::get('{dep_id}/create', [Admin\DepartmentalCourseController::class, 'create'])->name('create');
        Route::post('{dep_id}/create', [Admin\DepartmentalCourseController::class, 'store']);
        Route::get('{dep_course_id}/drop', [Admin\DepartmentalCourseController::class, 'drop'])->name('drop');
    });

    Route::prefix('clearance')->name('clearance.')->group(function(){
        Route::get('fee', [Admin\ClearanceController::class, 'fee_clearance'])->name('fee');
        Route::get('fee/generate/{student_id}', [Admin\ClearanceController::class, 'generate_fee_clearance'])->name('fee.generate');
        Route::post('fee/generate/{student_id}', [Admin\ClearanceController::class, 'save_fee_clearance']);
    });

});

Route::name('user.')->prefix('user')->middleware('isTeacher')->group(function () {
    Route::get('',  'Teacher\HomeController@index')->name('home');
    Route::get('notifications',  'Teacher\HomeController@notifications')->name('notifications');
    Route::get('class_list/{department_id}/{campus_id?}',  'Teacher\ClassController@program_levels_list')->name('class_list');
    Route::get('course_list',  'Teacher\ClassController@program_courses')->name('course_list');
    Route::get('class', 'Teacher\ClassController@index')->name('class');
    Route::get('students/init_promotion', 'Admin\StudentController@teacherInitPromotion')->name('students.init_promotion');
    Route::get('students/promote', 'Admin\StudentController@teacherPromotion')->name('students.promotion');
    Route::post('students/promote', 'Admin\StudentController@pend_promotion')->name('students.promote');
    Route::get('class/rank', 'Teacher\ClassController@classes')->name('rank.class');
    Route::get('class/master_sheet', 'Teacher\ClassController@master_sheet')->name('master_sheet');
    Route::get('rank_student/{class}', 'Teacher\ClassController@rank')->name('class.rank_student');
    Route::get('student/{class_id}/detail', 'Teacher\ClassController@student')->name('student.show');
    Route::get('student/{class_id}', 'Teacher\ClassController@students')->name('class.student');
    Route::get('{class_id}/student/{term_id}/report_card/{student_id}', 'Teacher\ClassController@reportCard')->name('student.report_card');
    
    
    Route::get('subject', 'Teacher\SubjectController@index')->name('subject');
    Route::get('course/management', 'Teacher\SubjectController@course_management')->name('course.management');
    Route::get('course/coverage', 'Teacher\SubjectController@course_coverage_index')->name('course.coverage.index');
    Route::get('course/coverage/{subject_id}', 'Teacher\SubjectController@course_coverage_show')->name('course.coverage.show');


    Route::get('subject/{subject}/result', 'Teacher\SubjectController@result')->name('result');
    Route::post('subject/{subject}/result', 'Teacher\SubjectController@store')->name('store_result');
    Route::get('subjects/notes/{class_id}/{id}', 'Teacher\SubjectNotesController@show')->name('subject.show');
    Route::get('subjects/students/{class_id}/{course_id}', 'Teacher\SubjectController@course_list')->name('subject.students');
    // Course Objectives
    Route::get('subject/{subject_id}/objective', 'Teacher\SubjectController@course_objective')->name('subject.objective');
    Route::post('subject/{subject_id}/objective', 'Teacher\SubjectController@course_objective_save');
    // COURSE CONTENT ROUTES
    Route::get('subjects/{subject_id}/content/{parent_id?}/{level?}', 'Teacher\SubjectController@course_content')->name('subject.content');
    Route::get('subjects/{subject_id}/edit_content/{topic_id}', 'Teacher\SubjectController@course_content_edit')->name('subject.content.edit');
    Route::post('subjects/{subject_id}/edit_content/{topic_id}', 'Teacher\SubjectController@course_content_update');
    Route::post('subjects/{subject_id}/content/topics/{parent_id?}/{level?}', 'Teacher\SubjectController@create_content_save')->name('subject.topics');

    Route::get('subjects/result_template/{class_id}/{course_id}/{campus_id}', 'Teacher\SubjectController@result_template')->name('subject.result_template');
    Route::put('subjects/notes/{id}', 'Teacher\SubjectNotesController@publish_notes')->name('subject.note.publish');
    Route::post('subjects/notes/{class_id}/{id}', 'Teacher\SubjectNotesController@store')->name('subject.note.store');
    Route::delete('subjects/notes/{id}', 'Teacher\SubjectNotesController@destroy')->name('subject.note.destroy');
    Route::get('{user_id}/subjects', 'Teacher\UserController@createSubject')->name('teacher.subjects.add');
    Route::post('{user_id}/teacher_subjects', 'Teacher\UserController@dropSubject')->name('teacher.subjects.drop');
    Route::post('{user_id}/subjects', 'Teacher\UserController@saveSubject')->name('teacher.subjects.save');
    Route::resource('teacher', 'Teacher\UserController');
    Route::prefix('programs')->name('programs.')->group(function(){
        Route::get('{department_id}', 'Teacher\HomeController@program_index')->name('index');
        Route::get('{program_id}/levels', 'Teacher\HomeController@program_levels')->name('levels');
        Route::get('{program_level_id}/courses', 'Teacher\HomeController@unit_courses')->name('courses');
        Route::get('{program_level_id}/manage_courses', 'Teacher\HomeController@manage_courses')->name('manage_courses');
        Route::post('{program_level_id}/manage_courses', 'Teacher\HomeController@save_courses')->name('save_courses');
        Route::get('{program_level_id}/course_report', 'Teacher\HomeController@course_report')->name('course_report');
    });
    Route::get('edit/{program_level_id}/{subject_id}/class_courses', 'Teacher\HomeController@edit_course')->name('edit.class_courses');
    Route::post('edit/{program_level_id}/{subject_id}/class_courses', 'Teacher\HomeController@update_course');
    Route::post('delete/{program_level_id}/{subject_id}/class_courses', 'Teacher\HomeController@delete_course')->name('delete.class_courses');
    Route::get('reset_password', 'Controller@reset_password')->name('reset_password');
    Route::post('reset_password', 'Controller@reset_password_save')->name('reset_password');

    Route::name('course.log.')->prefix('course_log/{subject_id}')->group(function(){
        Route::get('/', [TeacherHomeController::class, 'course_log_index'])->name('index');
        Route::get('sign/{attendance_id}/{topic_id}/{campus_id?}', [TeacherHomeController::class, 'course_log_sign'])->name('sign');
        Route::post('sign/{attendance_id}/{topic_id}/{campus_id?}', [TeacherHomeController::class, 'course_log_save']);
        Route::get('drop/{log_id}', [TeacherHomeController::class, 'delete_course_log'])->name('drop');
    });

    Route::prefix('results')->name('results.')->group(function(){
        Route::get('index', 'Teacher\ResultsController@index')->name('index');
        Route::get('fill_ca', 'Teacher\ResultsController@fill_ca')->name('fill_ca');
        Route::post('store_ca', 'Teacher\ResultsController@store_result')->name('store_result');
    });

    Route::name('attendance.')->prefix('attendance')->group(function(){
        Route::get('bycourse/index', [TeacherHomeController::class, 'attendance_bycourse_index'])->name('by_course.index');
        Route::get('bycourse/{subject_id}/show', [TeacherHomeController::class, 'attendance_bycourse'])->name('by_course');
        Route::get('bymonth/index', [TeacherHomeController::class, 'attendance_bymonth_index'])->name('by_month.index');
        Route::get('bymonth/{month}/show', [TeacherHomeController::class, 'attendance_bymonth'])->name('by_month');
    });

    Route::name('course.attendance.')->prefix('course/attendance')->group(function(){
        Route::get('', [ClassController::class, 'attendannce_index'])->name('index');
        Route::get('setup/{teacher_course_id}', [ClassController::class, 'setup_attendance_course'])->name('setup');
        Route::get('record/{teacher_course_id}', [ClassController::class, 'record_attendance'])->name('record');
        Route::post('record/{attendance_id}', [ClassController::class, 'record_attendance_save'])->name('record');
        Route::get('drop/{student_attendance_id}', [ClassController::class, 'drop_student_attendance'])->name('drop');
    });

    // Route::get('{program_level_id}/class_courses', 'Teacher\HomeController@manage_courses')->name('edit.class_courses');
    Route::prefix('{class_id}/course/{course_id}')->name('class_course.')->group(function () {
        Route::get('ca/fill', 'Teacher\HomeController@course_ca_fill')->name('ca.result');
        Route::get('ca/import', 'Teacher\HomeController@course_ca_import')->name('ca.import');
        Route::post('ca/import', 'Teacher\HomeController@course_ca_import_save');
        Route::get('exam/fill', 'Teacher\HomeController@course_exam_fill')->name('exam.result');
        Route::get('exam/import', 'Teacher\HomeController@course_exam_import')->name('exam.import');
        Route::post('exam/import', 'Teacher\HomeController@course_exam_import_save');
    }
    );
});

Route::prefix('student')->name('student.')->middleware(['isStudent'])->group(function () {
    Route::get('', 'Student\HomeController@index')->name('home');
    Route::get('edit_profile', 'Student\HomeController@edit_profile')->name('edit_profile')->withoutMiddleware('isStudent');
    Route::post('update_profile', 'Student\HomeController@update_profile')->name('update_profile')->withoutMiddleware('isStudent');
    Route::get('subject', 'Student\HomeController@subject')->name('subject');
    Route::get('result/ca', 'Student\HomeController@result')->name('result.ca');
    Route::post('result/ca', 'Student\HomeController@ca_result');
    Route::get('result/ca/download', 'Student\HomeController@ca_result_download');
    Route::get('result/exam', 'Student\HomeController@result')->name('result.exam');
    Route::post('result/exam', 'Student\HomeController@exam_result');
    Route::get('result/exam/download', 'Student\HomeController@exam_result_download');


    Route::get('fee/tution', 'Student\HomeController@fee')->name('fee.tution');
    Route::get('fee/others', 'Student\HomeController@other_incomes')->name('fee.other_incomes');
    Route::get('fee/pay', 'Student\HomeController@pay_fee')->name('pay_fee');
    Route::post('fee/pay', 'Student\HomeController@pay_fee_momo')->name('pay_fee');
    Route::get('others/pay/{id?}', 'Student\HomeController@pay_other_incomes')->name('pay_others');
    Route::post('others/pay/{id?}', 'Student\HomeController@pay_other_incomes_momo')->name('pay_others');
    Route::get('platform/pay', 'Student\HomeController@pay_platform_charges')->name('platform_charge.pay');
    Route::post('charges/pay', 'Student\HomeController@pay_charges_save')->name('charge.pay')->withoutMiddleware('isStudent');
    Route::get('result/pay', 'Student\HomeController@pay_semester_results')->name('result.pay');
    Route::get('transcript/pay', 'Student\HomeController@pay_transcript_charges')->name('transcript.pay');

    Route::prefix('tranzak')->name('tranzak.')->group(function(){
        Route::get('fee/pay', [StudentHomeController::class, 'tranzak_pay_fee'])->name('pay_fee');
        Route::post('fee/pay', [StudentHomeController::class, 'tranzak_pay_fee_momo']);
        Route::get('others/pay/{id?}', [StudentHomeController::class, 'tranzak_pay_other_incomes'])->name('pay_others');
        Route::post('others/pay/{id?}', [StudentHomeController::class, 'tranzak_pay_other_incomes_momo']);
        Route::get('result/pay', [StudentHomeController::class, 'tranzak_pay_semester_results'])->name('result.pay');
        Route::post('result/pay', [StudentHomeController::class, 'tranzak_pay_semester_results_momo']);
        Route::get('transcript/pay', [StudentHomeController::class, 'tranzak_pay_transcript_charges'])->name('transcript.pay');
        Route::get('payment_history', [StudentHomeController::class, 'tranzak_payment_history'])->name('online.payments.history');
        Route::get('processing', [StudentHomeController::class, 'tranzak_payment_processing'])->name('processing')->withoutMiddleware('isStudent');
        Route::post('processing', [StudentHomeController::class, 'tranzak_payment_processing_complete'])->withoutMiddleware('isStudent');
        Route::get('processing/{type}', [StudentHomeController::class, 'tranzak_processing'])->name('processing')->withoutMiddleware('isStudent');
        Route::post('processing/{type}', [StudentHomeController::class, 'tranzak_complete'])->withoutMiddleware('isStudent');
    });

    Route::get('subjects/{id}/notes', 'Student\HomeController@subjectNotes')->name('subject.notes');
    Route::get('boarding_fees/details', 'Student\HomeController@boarding')->name('boarding');
    Route::post('boarding_fees/details/', 'Student\HomeController@getBoardingFeesYear')->name('boarding_fees_details');
    Route::prefix('courses')->name('courses.')->group(function(){
        Route::get('registration', 'Student\HomeController@course_registration')->name('registration');
        Route::post('registration', 'Student\HomeController@register_courses');
        Route::get('registered', 'Student\HomeController@registered_courses')->name('registered');
        Route::get('form_b', 'Student\HomeController@form_b')->name('form_b');
        Route::get('drop', 'Student\HomeController@drop_course')->name('drop');
        Route::get('add', 'Student\HomeController@add_course')->name('add');
        Route::get('content/{subject_id}', 'Student\HomeController@course_content_index')->name('content');
    });
    Route::get('note/index/{course_id}', 'Student\HomeController@course_notes')->name('note.index');
    Route::get('assignment/index/{course_id}', 'Student\HomeController@assignment')->name('assignment.index');
    Route::get('notification/index/{course_id}', 'Student\HomeController@notification')->name('notification.index');
    Route::get('notification/show/{course_id}', 'Student\HomeController@show_notification')->name('notification.show');
    Route::prefix('notification')->name('notification.')->group(function(){
        Route::get('/', 'Student\HomeController@_notifications_index')->name('home');
        Route::get('/class/{class_id}/{campus_id}', 'Student\HomeController@_class_notifications')->name('class');
        Route::get('/department/{department_id}/{campus_id}', 'Student\HomeController@_department_notifications')->name('department');
        Route::get('/program/{program_id}/{campus_id}', 'Student\HomeController@_program_notifications')->name('program');
        Route::get('/school/{campus_id?}', 'Student\HomeController@_school_notifications')->name('school');
        Route::get('/view/{id}', 'Student\HomeController@_program_notifications_show')->name('view');
    });
    Route::prefix('material')->name('material.')->group(function(){
        Route::get('/', 'Student\HomeController@_notifications_index')->name('home');
        Route::get('/class/{class_id}/{campus_id}', 'Student\HomeController@_class_material')->name('class');
        Route::get('/department/{department_id}/{campus_id}', 'Student\HomeController@_department_material')->name('department');
        Route::get('/program/{program_id}/{campus_id}', 'Student\HomeController@_program_material')->name('program');
        Route::get('/school/{campus_id?}', 'Student\HomeController@_school_material')->name('school');
    });
    Route::get('resit/registration', 'Student\HomeController@resit_registration')->name('resit.registration');
    Route::post('resit/registration', 'Student\HomeController@register_resit');
    Route::post('resit/registration/payment', 'Student\HomeController@resit_payment')->name('resit.registration.payment');
    Route::post('resit/registration/pay', 'Student\HomeController@resit_pay')->name('resit.registration.pay');
    Route::get('resit/registered_courses', 'Student\HomeController@registered_resit_courses')->name('resit.registered_courses');
    Route::get('resit/pending_courses', 'Student\HomeController@pending_courses')->name('resit.pending_courses');
    Route::get('resit/index', 'Student\HomeController@resit_index')->name('resit.index');
    Route::get('resit/download/{resit_id}', 'Student\HomeController@resit_download')->name('resit.download_courses');
    Route::get('registered_courses/{year?}/{semester?}/{student?}', 'Student\HomeController@registerd_courses')->name('registered_courses');
    Route::get('class-subjects/{level}', 'Student\HomeController@class_subjects')->name('class-subjects');
    Route::get('search_course', 'Student\HomeController@search_course')->name('search_course');
    Route::get('courses/download/{year}/{semester}', 'Student\HomeController@download_courses')->name('courses.download');
    Route::get('stock/report/{year}', 'Student\HomeController@stock_report')->name('stock.report');
    Route::name('transcript.')->prefix('transcripts')->group(function () {
        Route::get('apply/{config_id?}', 'Student\HomeController@apply_transcript')->name('apply');
        Route::post('apply/{config_id?}', 'Student\HomeController@apply_save_transcript');
        Route::get('hostory', 'Student\HomeController@transcript_history')->name('history');
    });
    Route::get('reset_password', 'Controller@reset_password')->name('reset_password');
    Route::post('reset_password', 'Controller@reset_password_save')->name('reset_password');

    Route::get('online_payments/history', 'Student\HomeController@online_payment_history')->name('online.payments.history');

    Route::prefix('tzk/payment')->name('tranzak.payment.')->group(function(){
        Route::get('processing/{type}', [StudentHomeController::class, 'tranzak_processing'])->name('processing');
        Route::get('complete/{type}', [StudentHomeController::class, 'tranzak_complete'])->name('complete');
    });

    Route::prefix('delegate')->name('delegate.')->group(function(){
        Route::get('courses', 'Student\ClassDelegateController@index')->name('index');
        Route::get('record_attendance/{course_id}', 'Student\ClassDelegateController@record_attendance')->name('record_attendance');
        Route::get('check_in/{teacher_subject_id}', 'Student\ClassDelegateController@check_in')->name('check_in');
        Route::post('check_in/{teacher_subject_id}', 'Student\ClassDelegateController@check_in_save');
        Route::get('check_out/{attendance_id}', 'Student\ClassDelegateController@check_out')->name('check_out');
        Route::post('check_out/{attendance_id}', 'Student\ClassDelegateController@check_out_save');
        Route::post('attendance.delete/{attendance_id}', 'Student\ClassDelegateController@drop_attendance')->name('attendance.drop');
        Route::get('course/log/{attendance_id}/init', 'Student\ClassDelegateController@course_log_init')->name('course.log.init');
        Route::get('course/log/{attendance_id}/{topic_id}', 'Student\ClassDelegateController@course_log')->name('course.log');
        Route::post('course/log/{attendance_id}/{topic_id}', 'Student\ClassDelegateController@course_log_save');
    });
});

Route::get('download_form_b/{year}/{semester}', 'Student\HomeController@download_courses_api');


Route::prefix('parents')->name('parents.')->middleware(['parents', 'parent.charges'])->group(function(){
    Route::get('home', [ParentsHomeController::class, 'index'])->name('home');
    Route::get('results/{child_id}', [ParentsHomeController::class, 'results_index'])->name('results');
    Route::post('results/{child_id}', [ParentsHomeController::class, 'results']);
    Route::get('fees/{child_id}', [ParentsHomeController::class, 'fees'])->name('fees');
    Route::prefix('tranzak')->name('tranzak.')->group(function(){
        Route::get('fee/pay/{student_id}', [ParentsHomeController::class, 'tranzak_pay_fee'])->name('pay_fee');
        Route::post('fee/pay/{student_id}', [ParentsHomeController::class, 'tranzak_pay_fee_momo']);
        Route::get('others/pay/{student_id}/{id?}', [ParentsHomeController::class, 'tranzak_pay_other_incomes'])->name('pay_others');
        Route::post('others/pay/{student_is}/{id?}', [ParentsHomeController::class, 'tranzak_pay_other_incomes_momo']);
        Route::get('processing/{type}', [ParentsHomeController::class, 'tranzak_processing'])->name('processing')->withoutMiddleware('parent.charges');
        Route::post('processing/{type}', [ParentsHomeController::class, 'tranzak_complete'])->withoutMiddleware('parent.charges');
        Route::get('platform/pay', [ParentsHomeController::class, 'tranzak_platform'])->name('platform_charge.pay')->withoutMiddleware('parent.charges');
        Route::post('platform/pay', [ParentsHomeController::class, 'tranzak_platform_pay'])->withoutMiddleware('parent.charges');
    });
    Route::get('contact_school', [ParentsHomeController::class, 'contact_school'])->name('contact_school');

});
// Route::post('student/charges/pay', 'Student\HomeController@pay_charges_save')->name('student.charge.pay');
Route::get('platform/pay', 'Student\HomeController@pay_platform_charges')->name('platform_charge.pay');
Route::get('student/charges/complete_transaction/{ts_id}', 'Student\HomeController@complete_charges_transaction')->name('student.charges.complete');
Route::get('student/charges/failed_transaction/{ts_id}', 'Student\HomeController@failed_charges_transaction')->name('student.charges.failed');

Route::get('section-children/{parent}', 'HomeController@children')->name('section-children');
Route::get('section-subjects/{parent}', 'HomeController@subjects')->name('section-subjects');
Route::get('student-search/{name}', 'HomeController@student')->name('student-search');
Route::get('student-search', 'HomeController@student_get')->name('student-search-get');
Route::get('search-all-students/{name}', 'HomeController@searchStudents')->name('search-all-students');
Route::get('search-all-students', 'HomeController@searchStudents_get')->name('get-search-all-students');
Route::get('search-students', 'HomeController@search_students')->name('search_students');
Route::get('search-students/per_campus_class_per_year', 'HomeController@search_students_per_cammpus_class_per_year')->name('search_students.per_campus_class_per_year');
Route::get('student-fee-search', 'HomeController@fee')->name('student-fee-search');
Route::get('student_rank', 'HomeController@rank')->name('student_rank');
Route::post('student_rank', 'HomeController@rankPost')->name('student_rank');

Route::prefix('course/notification')->name('course.notification.')->group(function(){
    Route::get('{course_id}', 'Teacher\SubjectController@notifications')->name('index');
    Route::get('{course_id}/create', 'Teacher\SubjectController@create_notification')->name('create');
    Route::post('{course_id}/save', 'Teacher\SubjectController@save_notification')->name('save');
    Route::get('{course_id}/edit/{id}', 'Teacher\SubjectController@edit_notification')->name('edit');
    Route::post('{course_id}/update/{id}', 'Teacher\SubjectController@update_notification')->name('update');
    Route::get('{course_id}/delete/{id}', 'Teacher\SubjectController@drop_notification')->name('drop');
    Route::get('{course_id}/show/{id}', 'Teacher\SubjectController@show_notification')->name('show');
});

Route::name('faqs.')->prefix('faqs')->group(function(){
    Route::get('', 'FAQsController@index')->name('index');
    Route::get('create', 'FAQsController@create')->name('create');
    Route::post('create', 'FAQsController@save')->name('save');
    Route::get('edit/{id}', 'FAQsController@edit')->name('edit');
    Route::get('publish/{id}', 'FAQsController@publish')->name('publish');
    Route::get('download/{id}', 'FAQsController@download')->name('download');
    Route::post('update/{id}', 'FAQsController@update')->name('update');
    Route::get('show/{id}', 'FAQsController@show')->name('show');
    Route::get('delete/{id}', 'FAQsController@drop')->name('drop');
});

Route::name('material.')->prefix('{layer}/{layer_id}/material/{campus_id?}')->group(function(){
    Route::get('', 'MaterialController@index')->name('index');
    Route::get('create', 'MaterialController@create')->name('create');
    Route::post('create', 'MaterialController@save')->name('save');
    Route::get('edit/{id}', 'MaterialController@edit')->name('edit');
    Route::get('download/{id}', 'MaterialController@download')->name('download');
    Route::post('update/{id}', 'MaterialController@update')->name('update');
    Route::get('show/{id}', 'MaterialController@show')->name('show');
    Route::get('delete/{id}', 'MaterialController@drop')->name('drop');
});

// ALTERNATIVE NOTIFICATIONS AND MATERIAL APPRAOCH
Route::name('notifications.')->prefix('{layer}/{layer_id}/notifications/{campus_id?}')->group(function(){
    Route::get('/', 'NotificationsController@index')->name('index');
    Route::get('/create', 'NotificationsController@create')->name('create');
    Route::post('/create', 'NotificationsController@save')->name('save');
    Route::get('/delete/{id}', 'NotificationsController@drop')->name('drop');
    Route::get('/edit/{id}', 'NotificationsController@edit')->name('edit');
    Route::post('/update/{id}', 'NotificationsController@update')->name('update');
    Route::get('/show/{id}', 'NotificationsController@show')->name('show');
});

// Messages
Route::name('messages.')->prefix('messages')->group(function(){
    Route::get('create', [NotificationsController::class, 'create_message'])->name('create');
    Route::post('create', [NotificationsController::class, 'create_message_save']);
    Route::get('sent', [NotificationsController::class, 'sent_messages'])->name('sent');
});

Route::get('search/students/boarders/{name}', 'HomeController@getStudentBoarders')->name('getStudentBoarder');

Route::get('/campuses/{id}/programs', [Controller::class, 'sorted_campus_program_levels'])->name('campus.programs');
Route::get('semesters/{background}', function(Request $request){
    return \App\Models\Semester::where('background_id', $request->background)->get();
})->name('semesters');
Route::get('class_subjects/{program_level_id}', function($program_level_id){
    // return $program_level_id;
    $courses = \App\Models\ClassSubject::where(['class_subjects.class_id'=>$program_level_id])
            ->whereNull('class_subjects.deleted_at')
            ->join('subjects', ['subjects.id'=>'class_subjects.subject_id'])
            ->get(['subjects.*', 'class_subjects.coef as cv']);
            // return $courses;
            return response()->json(SubjectResource::collection($courses));
})->name('class_subjects');
Route::get('campus/{campus}/program_levels', [Controller::class, 'sorted_campus_program_levels'])->name('campus.program_levels');
Route::get('program_levels', [Controller::class, 'sorted_program_levels'])->name('program_levels');
// Route::get('class_subjects/{program_level_id}', function($program_level_id){
//     // return $program_level_id;
//     $courses = \App\Models\ClassSubject::where(['class_subjects.class_id'=>$program_level_id])
//             ->join('subjects', ['subjects.id'=>'class_subjects.subject_id'])
//             ->get('subjects.*');
//             return $courses;
//             // return response()->json(SubjectsResource::collection($courses));
// })->name('class_subjects');
Route::get('getColor/{label}', [HomeController::class, 'getColor'])->name('getColor');

Route::get('search_subjects', function (Request $request) {
    $data = $request->name;
    $subjects = Subjects::where('code', 'LIKE', '%' . $data . '%')
        ->orWhere('name', 'LIKE', '%' . $data . '%')->orderBy('name')->paginate(20);
    return $subjects;
})->name('search_subjects');

Route::get('search_courses', function (Request $request) {
    $data = $request->name;
    $subjects = Subjects::where('code', 'LIKE', '%' . $data . '%')
        ->orWhere('name', 'LIKE', '%' . $data . '%')->orderBy('name')->take(20)->get()
        ->map(function($row){
            $row->_semester = $row->semester->name;
            $row->_level = $row->level->level;
            return $row;
        });
    return $subjects;
})->name('search_courses');

Route::get('get-income-item/{income_id}', function(Request $request, $income_id){
    return \App\Models\Income::find($income_id);
})->name('get-income-item');

Route::get('mode/{locale}', function ($batch) {
    session()->put('mode', $batch);

    return redirect()->back();
})->name('mode');

Route::get('trace_resits', function(){
    $data['resits'] = Resit::all();
    $resit_ids = $data['resits']->pluck('id')->toArray();
    $data['resit_students'] = StudentSubject::whereIn('resit_id', $resit_ids)->get();
    return $data;
});
Route::get('next_year/{yr}', function($yr){
    return \App\Models\Batch::find($yr+1);
})->name('next_year');

Route::get('getDeptPrograms/{dept_id}', function($dept_id){
    return response()->json(['programs' => \App\Models\SchoolUnits::where('parent_id', $dept_id)->get()]);
})->name('department.programs');
Route::get('getProgLevels/{prog_id}', function($prog_id){
    return response(['levels'=>\App\Models\Level::join('program_levels', 'program_levels.level_id', '=', 'levels.id')->where('program_levels.program_id', $prog_id)->get(['levels.*'])]) ;
})->name('program.levels');


// Route::get('courses/{matric}', function($matric){
//     $mat = str_replace('_', '/', $matric);
//     $student = \App\Models\Students::where('matric', $mat)->first();
//     dd($student->registered_courses(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->get());
// });
// Route::get('results/{matric}', function($matric){
//     $mat = str_replace('_', '/', $matric);
//     $student = \App\Models\Students::where('matric', $mat)->first();
//     dd($student->result()->orderBy('semester_id')->get()??null);
// });
// Route::get('_courses', function(){
//     $courses = \App\Models\StudentSubject::where('year_id', '!=', \App\Helpers\Helpers::instance()->getCurrentAccademicYear())->whereNotNull('resit_id')->get()->groupBy('resit_id');
//     dd($courses);
// });

Route::any('{any?}', [CustomLoginController::class, 'login']);
