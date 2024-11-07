<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Student\LoginController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExamSessionController;


//prefix "admin"
Route::prefix('admin')->group(function () {

    //middleware "auth"
    Route::group(['middleware' => ['auth']], function () {

        //route dashboard
        Route::get('/dashboard', DashboardController::class)->name('admin.dashboard');
        Route::resource('/lessons', LessonController::class, ['as' => 'admin']);
        Route::resource('/classrooms', ClassroomController::class, ['as' => 'admin']);
        //route student import
        Route::get('/students/import', [\App\Http\Controllers\Admin\StudentController::class, 'import'])->name('admin.students.import');

        //route student store import
        Route::post('/students/import', [\App\Http\Controllers\Admin\StudentController::class, 'storeImport'])->name('admin.students.storeImport');

        //route resource students    
        Route::resource('/students', \App\Http\Controllers\Admin\StudentController::class, ['as' => 'admin']);

        Route::resource('/exams', ExamController::class, ['as' => 'admin']);

        //custom route for create question exam
        Route::get('/exams/{exam}/questions/create', [ExamController::class, 'createQuestion'])->name('admin.exams.createQuestion');

        //custom route for store question exam
        Route::post('/exams/{exam}/questions/store', [ExamController::class, 'storeQuestion'])->name('admin.exams.storeQuestion');

        //custom route for edit question exam
        Route::get('/exams/{exam}/questions/{question}/edit', [ExamController::class, 'editQuestion'])->name('admin.exams.editQuestion');

        //custom route for update question exam
        Route::put('/exams/{exam}/questions/{question}/update', [ExamController::class, 'updateQuestion'])->name('admin.exams.updateQuestion');

        Route::delete('/exams/{exam}/questions/{question}/destroy', [ExamController::class, 'destroyQuestion'])->name('admin.exams.destroyQuestion');


        //route student import
        Route::get('/exams/{exam}/questions/import', [ExamController::class, 'import'])->name('admin.exam.questionImport');

        //route student import
        Route::post('/exams/{exam}/questions/import', [ExamController::class, 'storeImport'])->name('admin.exam.questionStoreImport');

        Route::resource('/exam_sessions', ExamSessionController::class, ['as' => 'admin']);

        //custom route for enrolle create
        Route::get('/exam_sessions/{exam_session}/enrolle/create', [ExamSessionController::class, 'createEnrolle'])->name('admin.exam_sessions.createEnrolle');

        //custom route for enrolle store
        Route::post('/exam_sessions/{exam_session}/enrolle/store', [ExamSessionController::class, 'storeEnrolle'])->name('admin.exam_sessions.storeEnrolle');

        Route::delete('/exam_sessions/{exam_session}/enrolle/{exam_group}/destroy', [ExamSessionController::class, 'destroyEnrolle'])->name('admin.exam_sessions.destroyEnrolle');

        Route::post('/exam_sessions/{id}/generate-token', [ExamSessionController::class, 'generateToken']);
        Route::post('/exam_sessions/update-status', [ExamSessionController::class, 'updateStatus'])->name('exam.status.update');



        //route index reports
        Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');

        //route index reports filter
        Route::get('/reports/filter', [ReportController::class, 'filter'])->name('admin.reports.filter');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
    });
});

//route homepage
Route::get('/', function () {

    //cek session student
    if (auth()->guard('student')->check()) {
        return redirect()->route('student.dashboard');
    }

    //return view login
    return \Inertia\Inertia::render('Student/Login/Index');
});

Route::post('/students/login', LoginController::class)->name('student.login');

Route::prefix('student')->group(function () {

    //middleware "student"
    Route::group(['middleware' => 'student'], function () {

        //route dashboard
        Route::get('/dashboard', App\Http\Controllers\Student\DashboardController::class)->name('student.dashboard');
        Route::get('/exam-confirmation/{id}', [App\Http\Controllers\Student\ExamController::class, 'confirmation'])->name('student.exams.confirmation');

        //route exam start
        Route::get('/exam-start/{id}', [App\Http\Controllers\Student\ExamController::class, 'startExam'])->name('student.exams.startExam');

        //route exam show
        Route::get('/exam/{id}/{page}', [App\Http\Controllers\Student\ExamController::class, 'show'])->name('student.exams.show');

        Route::put('/exam-duration/update/{grade_id}', [App\Http\Controllers\Student\ExamController::class, 'updateDuration'])->name('student.exams.update_duration');

        Route::post('/exam-answer', [App\Http\Controllers\Student\ExamController::class, 'answerQuestion'])->name('student.exams.answerQuestion');

        Route::post('/exam-end', [App\Http\Controllers\Student\ExamController::class, 'endExam'])->name('student.exams.endExam');

        Route::get('/exam-result/{exam_group_id}', [App\Http\Controllers\Student\ExamController::class, 'resultExam'])->name('student.exams.resultExam');

        Route::post('/exam/close-tab', [App\Http\Controllers\Student\ExamController::class, 'closeTab'])->name('student.exam.closeTab');
    });
});
