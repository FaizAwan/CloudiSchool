<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider within a group which | contains the "web" middleware group. Now create something great! | */

// Storage Proxy (Fix for shared hosting with disabled symlinks)
Route::get('/storage/{path}', function ($path) {
    if (strpos($path, '..') !== false) abort(403);
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) abort(404);
    
    $mime = mime_content_type($fullPath);
    return response()->file($fullPath, ['Content-Type' => $mime]);
})->where('path', '.*');

Route::get('/', function () {
    $blogs = \App\Models\Blog::where('status', 'published')->latest()->take(6)->get();
    return view('welcome', compact('blogs'));
})->name('landing');

// Public Blog Routes
Route::get('/blogs', [App\Http\Controllers\BlogController::class , 'index'])->name('blogs.index');
Route::get('/blogs/{slug}', [App\Http\Controllers\BlogController::class , 'show'])->name('blogs.show');

// Public SaaS Pages
Route::get('/about', [App\Http\Controllers\PageController::class , 'about'])->name('about');
Route::get('/careers', [App\Http\Controllers\PageController::class , 'careers'])->name('careers');
Route::get('/contact', [App\Http\Controllers\ContactController::class , 'show'])->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class , 'store'])->name('contact.store');
Route::get('/privacy-policy', [App\Http\Controllers\PageController::class , 'privacy'])->name('privacy');
Route::get('/terms', [App\Http\Controllers\PageController::class , 'terms'])->name('terms');
Route::get('/help-center', [App\Http\Controllers\PageController::class , 'helpCenter'])->name('help-center');
Route::get('/community', [App\Http\Controllers\PageController::class , 'community'])->name('community');
Route::get('/status', [App\Http\Controllers\PageController::class , 'status'])->name('status');



Auth::routes();


Route::get('/home', 'App\\Http\\Controllers\\HomeController@index')->name('home');

// Gracefully handle /view
Route::get('/view', function () {
    // Fallback: send stray GET /view hits to the dashboard
    return redirect()->route('home');
})->name('view.redirect');

// Handle legacy POST /view from reportsCollectiveFees search form
Route::post('/view', function () {
    $request = request();
    return redirect()->route('reportsCollectiveFees', [
    'class_id' => $request->input('class_id'),
    'fromMonth' => $request->input('fromMonth'),
    'fromYear' => $request->input('fromYear'),
    'toMonth' => $request->input('toMonth'),
    'toYear' => $request->input('toYear'),
    ]);
})->name('view.post.redirect');


// Student credential generation (Superadmin only)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/student-credentials', [App\Http\Controllers\AdminController::class , 'generateStudentCredentials'])->name('admin.student-credentials');
});

// Teachers Management - accessible to authenticated tenant users
Route::middleware(['auth'])->group(function () {
    Route::get('/teachers', [App\Http\Controllers\TeachersController::class , 'index'])->name('teachers');
    Route::post('/addTeacher', [App\Http\Controllers\TeachersController::class , 'addTeacher'])->name('addTeacher');
    Route::post('/teachers/update', [App\Http\Controllers\TeachersController::class , 'updateTeacher'])->name('teachers.update');
    Route::get('/teachers/view/{id}', [App\Http\Controllers\TeachersController::class , 'view'])->name('teachers.view');
    Route::get('/teachers/delete/{id}', [App\Http\Controllers\TeachersController::class , 'delete'])->name('teachers.delete');
});

// ========================================
// ALL FEATURES RESTRICTED TO SUPERADMIN ONLY
// ========================================

// ALL FEATURES RESTRICTED TO SUPERADMIN ONLY
// ========================================

Route::middleware(['auth', 'role:superadmin'])->group(function () {

    // Schools Management (superadmin write operations only)


    // Students Management (moved to auth-only below)

    // Teachers Management (moved to auth-only group below)

    // Parents - Superadmin-only endpoints removed; tenant endpoints added in auth group below

    // Classes Management (moved to auth-only below)

    // Fees Management (moved to auth-only routes)

    // Challans Management (moved to auth-only routes)

    // Reports (moved to auth-only routes)



    // Users Management
    Route::get('/users', [App\Http\Controllers\HomeController::class , 'users'])->name('users');




    // ========================================
    // EXAM MODULE ROUTES (Superadmin Only)
    // ========================================

    // Subjects Management (moved to auth-only below)

    // Term Subjects Wizard (class/term quick enable + marks)

    // Online Exams Routes (moved to auth-only routes)

    // Question Bank Routes
    // Students Management (tenant-scoped)
    Route::get('/students', [App\Http\Controllers\StudentsController::class , 'index'])->name('students');
    Route::get('/students/overview/{id}', [App\Http\Controllers\StudentsController::class , 'overview'])->name('students.overview');
    Route::get('/students/view/{id}', [App\Http\Controllers\StudentsController::class , 'view'])->name('students.view');
    Route::get('/studentsListGRno', [App\Http\Controllers\StudentsController::class , 'studentsListGRno'])->name('studentsListGRno');
    Route::get('/studentsListSLC', [App\Http\Controllers\StudentsController::class , 'studentsListSLC'])->name('studentsListSLC');
    Route::get('/studentsWithSameGRno', [App\Http\Controllers\StudentsController::class , 'studentsWithSameGRno'])->name('studentsWithSameGRno');
    Route::post('/addStudent', [App\Http\Controllers\StudentsController::class , 'addStudent'])->name('addStudent');
    Route::get('/getStudents', [App\Http\Controllers\StudentsController::class , 'getStudents'])->name('getStudents');
    Route::post('/promoteStudent', [App\Http\Controllers\StudentsController::class , 'promoteStudent'])->name('promoteStudent');
    Route::get('/editStudent/{studentID}', [App\Http\Controllers\StudentsController::class , 'editStudent'])->name('editStudent');
    Route::post('/updateStudent', [App\Http\Controllers\StudentsController::class , 'updateStudent'])->name('updateStudent');
    // Add new parent from Students page modal
    Route::post('/addParentFromStudent', [App\Http\Controllers\ParentsController::class , 'addParentFromStudent'])->name('addParentFromStudent');

    // Question Bank Routes


    // Manual Exams Routes (moved to auth-only routes)

    // AJAX endpoints for dependent dropdowns (moved to auth-only routes)

    // Search and utilities (moved to auth-only routes)

    // CSV Import/Export (moved to auth-only routes)

    // Exam Questions Routes (moved to auth-only routes)

    // Print routes (moved to auth-only routes)

    // Behavior attributes management (moved to auth-only routes)

    // Principal remarks (moved to auth-only routes)



    // Principal Remarks Management
    Route::get('/principal-remarks/admin/create', [App\Http\Controllers\PrincipalRemarksController::class , 'create'])->name('principal-remarks.create');
    Route::post('/principal-remarks/admin', [App\Http\Controllers\PrincipalRemarksController::class , 'store'])->name('principal-remarks.store');
    Route::get('/principal-remarks/admin/{id}/edit', [App\Http\Controllers\PrincipalRemarksController::class , 'edit'])->name('principal-remarks.edit');
    Route::put('/principal-remarks/admin/{id}', [App\Http\Controllers\PrincipalRemarksController::class , 'update'])->name('principal-remarks.update');
    Route::delete('/principal-remarks/admin/{id}', [App\Http\Controllers\PrincipalRemarksController::class , 'destroy'])->name('principal-remarks.destroy');
    Route::post('/principal-remarks/admin/{id}/toggle-status', [App\Http\Controllers\PrincipalRemarksController::class , 'toggleStatus'])->name('principal-remarks.toggle-status');

});

// Schools listing accessible to authenticated tenant admins (scoped in controller)
Route::middleware(['auth'])->group(function () {
    // Profile Management Routes (Accessible by all authenticated users)
    Route::get('/profile', [App\Http\Controllers\ProfileController::class , 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class , 'edit'])->name('profile.edit');
    Route::put('/profile/update', [App\Http\Controllers\ProfileController::class , 'update'])->name('profile.update');
    Route::put('/profile/update-role-data', [App\Http\Controllers\ProfileController::class , 'updateRoleData'])->name('profile.update-role-data');
    Route::get('/profile/change-password', [App\Http\Controllers\ProfileController::class , 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/profile/change-password', [App\Http\Controllers\ProfileController::class , 'changePassword'])->name('profile.update-password');
    Route::get('/profile/settings', [App\Http\Controllers\ProfileController::class , 'settings'])->name('profile.settings');
    Route::post('/profile/settings', [App\Http\Controllers\ProfileController::class , 'updateSettings'])->name('profile.update-settings');
    // Question Bank Routes
    Route::get('/question-bank', [App\Http\Controllers\QuestionBankController::class , 'index'])->name('question-bank.index');
    Route::get('/question-bank/create', [App\Http\Controllers\QuestionBankController::class , 'create'])->name('question-bank.create');
    Route::post('/question-bank', [App\Http\Controllers\QuestionBankController::class , 'store'])->name('question-bank.store');
    Route::post('/question-bank/add-to-exam', [App\Http\Controllers\QuestionBankController::class , 'addToExam'])->name('question-bank.add-to-exam');
    Route::delete('/question-bank/bulk-delete', [App\Http\Controllers\QuestionBankController::class , 'bulkDelete'])->name('question-bank.bulk-delete');
    Route::get('/question-bank/{questionBank}', [App\Http\Controllers\QuestionBankController::class , 'show'])->name('question-bank.show');
    Route::get('/question-bank/{questionBank}/edit', [App\Http\Controllers\QuestionBankController::class , 'edit'])->name('question-bank.edit');
    Route::put('/question-bank/{questionBank}', [App\Http\Controllers\QuestionBankController::class , 'update'])->name('question-bank.update');
    Route::delete('/question-bank/{questionBank}', [App\Http\Controllers\QuestionBankController::class , 'destroy'])->name('question-bank.destroy');

    // Exam Schedule Routes
    Route::get('/exam-schedule', [App\Http\Controllers\ExamScheduleController::class , 'index'])->name('exam-schedule.index');
    Route::get('/exam-schedule/create', [App\Http\Controllers\ExamScheduleController::class , 'create'])->name('exam-schedule.create');
    Route::post('/exam-schedule', [App\Http\Controllers\ExamScheduleController::class , 'store'])->name('exam-schedule.store');
    Route::get('/exam-schedule/{examSchedule}', [App\Http\Controllers\ExamScheduleController::class , 'show'])->name('exam-schedule.show');
    Route::get('/exam-schedule/{examSchedule}/edit', [App\Http\Controllers\ExamScheduleController::class , 'edit'])->name('exam-schedule.edit');
    Route::put('/exam-schedule/{examSchedule}', [App\Http\Controllers\ExamScheduleController::class , 'update'])->name('exam-schedule.update');
    Route::delete('/exam-schedule/{examSchedule}', [App\Http\Controllers\ExamScheduleController::class , 'destroy'])->name('exam-schedule.destroy');

    // Exam Reports Routes
    Route::get('/exam-reports', [App\Http\Controllers\ExamReportsController::class , 'index'])->name('exam-reports.index');
    Route::get('/exam-reports/results', [App\Http\Controllers\ExamReportsController::class , 'results'])->name('exam-reports.results');
    Route::get('/exam-reports/analytics', [App\Http\Controllers\ExamReportsController::class , 'analytics'])->name('exam-reports.analytics');
    Route::get('/exam-reports/student-performance', [App\Http\Controllers\ExamReportsController::class , 'studentPerformancePage'])->name('exam-reports.student-performance');

    // Student Exam Routes
    Route::get('/student-exams', [App\Http\Controllers\StudentExamController::class , 'index'])->name('student-exams.index');
    Route::get('/student-exams/{exam}', [App\Http\Controllers\StudentExamController::class , 'show'])->name('student-exams.show');
    Route::post('/student-exams/{exam}/start', [App\Http\Controllers\StudentExamController::class , 'start'])->name('student-exams.start');
    Route::post('/student-exams/{exam}/submit', [App\Http\Controllers\StudentExamController::class , 'submit'])->name('student-exams.submit');

    Route::get('/schools', [App\Http\Controllers\HomeController::class , 'schools'])->name('schools');
    Route::post('/addSchool', [App\Http\Controllers\HomeController::class , 'addSchool'])->name('addSchool');
    Route::post('/save-branch-details', [App\Http\Controllers\HomeController::class , 'updateSchool'])->name('updateSchool');
    Route::get('/fetch-branch-details/{id}', [App\Http\Controllers\HomeController::class , 'getSchool'])->name('schools.show.json');
    Route::get('/getSchools', [App\Http\Controllers\HomeController::class , 'getSchools'])->name('getSchools');
    // Classes Management (tenant-scoped)
    Route::get('/classes', [App\Http\Controllers\ClassesController::class , 'index'])->name('classes');
    Route::post('/addClass', [App\Http\Controllers\ClassesController::class , 'addClass'])->name('addClass');
    Route::post('/updateClass', [App\Http\Controllers\ClassesController::class , 'updateClass'])->name('updateClass');
    Route::get('/getClasses', [App\Http\Controllers\ClassesController::class , 'getClasses'])->name('getClasses');
    Route::get('/deleteClass/{id}', [App\Http\Controllers\ClassesController::class , 'deleteClass'])->name('deleteClass');

    // Sections Management
    Route::get('/sections', [App\Http\Controllers\SectionsController::class , 'index'])->name('sections.index');
    Route::get('/sections/class/{classId}', [App\Http\Controllers\SectionsController::class , 'listByClass'])->name('sections.byClass');
    Route::post('/sections', [App\Http\Controllers\SectionsController::class , 'store'])->name('sections.store');
    Route::put('/sections/{id}', [App\Http\Controllers\SectionsController::class , 'update'])->name('sections.update');
    Route::delete('/sections/{id}', [App\Http\Controllers\SectionsController::class , 'destroy'])->name('sections.destroy');
    // Subjects Management (tenant-scoped in controller)
    Route::get('/subjects', [App\Http\Controllers\SubjectsController::class , 'index'])->name('subjects.index');
    Route::get('/subjects/view/{id}', [App\Http\Controllers\SubjectsController::class , 'view'])->name('subjects.view');
    Route::post('/subjects', [App\Http\Controllers\SubjectsController::class , 'store'])->name('subjects.store');
    Route::put('/subjects/{id}', [App\Http\Controllers\SubjectsController::class , 'update'])->name('subjects.update');
    Route::delete('/subjects/{id}', [App\Http\Controllers\SubjectsController::class , 'destroy'])->name('subjects.destroy');
    Route::post('/subjects/bulk-store', [App\Http\Controllers\SubjectsController::class , 'bulkStore'])->name('subjects.bulkStore');
    Route::post('/subjects/bulk-update', [App\Http\Controllers\SubjectsController::class , 'bulkUpdate'])->name('subjects.bulkUpdate');
    Route::delete('/subjects/bulk-destroy', [App\Http\Controllers\SubjectsController::class , 'bulkDestroy'])->name('subjects.bulkDestroy');
    Route::get('/subjects/by-class/{id}', [App\Http\Controllers\SubjectsController::class , 'byClass'])->name('subjects.by-class');
    Route::get('/term-subjects', [App\Http\Controllers\TermSubjectsController::class , 'index'])->name('term-subjects.index');
    Route::get('/term-subjects/fetch', [App\Http\Controllers\TermSubjectsController::class , 'fetch'])->name('term-subjects.fetch');
    Route::post('/term-subjects/save', [App\Http\Controllers\TermSubjectsController::class , 'save'])->name('term-subjects.save');

    // Timetable & Periods (tenant-scoped)
    Route::get('/weeklyTimetable', [App\Http\Controllers\HomeController::class , 'weeklyTimetable'])->name('weeklyTimetable');
    Route::get('/weeklyTimetable/classes', [App\Http\Controllers\HomeController::class , 'weeklyTimetableByClass'])->name('weeklyTimetable.classes');
    Route::get('/weeklyTimetable/subjects', [App\Http\Controllers\HomeController::class , 'weeklyTimetableBySubject'])->name('weeklyTimetable.subjects');
    Route::get('/periods', [App\Http\Controllers\HomeController::class , 'periods'])->name('periods');
    Route::get('/getPeriods', [App\Http\Controllers\HomeController::class , 'getPeriods'])->name('getPeriods');
    Route::post('/addTimetable', [App\Http\Controllers\HomeController::class , 'addTimetable'])->name('addTimetable');
    Route::get('/deleteTimeTable/{teacherID}/{day}/{period}', [App\Http\Controllers\HomeController::class , 'deleteTimeTable'])->name('deleteTimeTable');
    Route::post('/addPeriod', [App\Http\Controllers\HomeController::class , 'addPeriod'])->name('addPeriod');
    Route::post('/updatePeriod', [App\Http\Controllers\HomeController::class , 'updatePeriod'])->name('updatePeriod');
    Route::get('/deletePeriod/{id}', [App\Http\Controllers\HomeController::class , 'deletePeriod'])->name('deletePeriod');

    // Fees Management (tenant-scoped)
    Route::get('/fees', [App\Http\Controllers\FeesController::class , 'index'])->name('fees');
    Route::get('/feesManagement', [App\Http\Controllers\FeesController::class , 'feesManagement'])->name('feesManagement');

    // Academic Years - General Access
    Route::get('/academic-years', [App\Http\Controllers\AcademicYearController::class , 'index'])->name('academic-years.index');
    Route::get('/academic-years/list', [App\Http\Controllers\AcademicYearController::class , 'list'])->name('academic-years.list');
    Route::post('/academic-years', [App\Http\Controllers\AcademicYearController::class , 'store'])->name('academic-years.store');
    Route::put('/academic-years/{id}', [App\Http\Controllers\AcademicYearController::class , 'update'])->name('academic-years.update');
    Route::post('/academic-years/{id}', [App\Http\Controllers\AcademicYearController::class , 'update'])->name('academic-years.update.post');
    Route::delete('/academic-years/{id}', [App\Http\Controllers\AcademicYearController::class , 'destroy'])->name('academic-years.destroy');
    Route::post('/academic-years/{id}/delete', [App\Http\Controllers\AcademicYearController::class , 'destroy'])->name('academic-years.destroy.post');
    Route::post('/academic-years/{id}/status', [App\Http\Controllers\AcademicYearController::class , 'toggleStatus'])->name('academic-years.status');
    Route::post('/addFeeType', [App\Http\Controllers\FeesController::class , 'addFeeType'])->name('addFeeType');
    Route::post('/updateFeeType', [App\Http\Controllers\FeesController::class , 'updateFeeType'])->name('updateFeeType');
    Route::post('/addFees', [App\Http\Controllers\FeesController::class , 'addFees'])->name('addFees');
    Route::post('/updateFeesGroup', [App\Http\Controllers\FeesController::class , 'updateFeesGroup'])->name('updateFeesGroup');
    Route::post('/deleteFeesGroup', [App\Http\Controllers\FeesController::class , 'deleteFeesGroup'])->name('deleteFeesGroup');
    Route::get('/deleteFeeType/{id}', [App\Http\Controllers\FeesController::class , 'deleteFeeType'])->name('deleteFeeType');
    Route::get('/feeReceipt', [App\Http\Controllers\FeesController::class , 'feeReceipt'])->name('feeReceipt');

    // Challans Management (tenant-scoped)
    Route::get('/challan', [App\Http\Controllers\ChallansController::class , 'index'])->name('challan');
    Route::get('/challanPaid', [App\Http\Controllers\ChallansController::class , 'challanPaid'])->name('challanPaid');
    Route::post('/paidChallan', [App\Http\Controllers\ChallansController::class , 'paidChallan'])->name('paidChallan');
    Route::post('/addChallan', [App\Http\Controllers\ChallansController::class , 'addChallan'])->name('addChallan');
    Route::get('/viewChallan/{id}', [App\Http\Controllers\ChallansController::class , 'viewChallan'])->name('viewChallan');
    Route::get('/challanPaidByID/{challanID}', [App\Http\Controllers\ChallansController::class , 'challanPaidByID'])->name('challanPaidByID');
    Route::get('/editChallan/{id}', [App\Http\Controllers\ChallansController::class , 'editChallan'])->name('editChallan');
    Route::post('/editChallanByChallanID', [App\Http\Controllers\ChallansController::class , 'editChallanByChallanID'])->name('editChallanByChallanID');
    Route::get('/deleteChallanByChallanID/{challanID}', [App\Http\Controllers\ChallansController::class , 'deleteChallanByChallanID'])->name('deleteChallanByChallanID');
    Route::post('/printCountByChallanID', [App\Http\Controllers\ChallansController::class , 'printCountByChallanID'])->name('printCountByChallanID');
    Route::get('/classWiseChallan', [App\Http\Controllers\ChallansController::class , 'classWiseChallan'])->name('classWiseChallan');
    Route::post('/printClassWiseChallans', [App\Http\Controllers\ChallansController::class , 'printClassWiseChallans'])->name('printClassWiseChallans');
    Route::get('/cashBook', [App\Http\Controllers\ChallansController::class , 'cashBook'])->name('cashBook');
    Route::get('/cashBookData', [App\Http\Controllers\ChallansController::class , 'cashBookData'])->name('cashBookData');

    // Reports (tenant-scoped)
    Route::get('/reportsClassWiseTotalStudents', [App\Http\Controllers\ReportsController::class , 'reportsClassWiseTotalStudents'])->name('reportsClassWiseTotalStudents');
    Route::get('/reportsCollectiveFees', [App\Http\Controllers\ReportsController::class , 'reportsCollectiveFees'])->name('reportsCollectiveFees');
    Route::get('/reportsClassWiseTotalFees', [App\Http\Controllers\ReportsController::class , 'reportsClassWiseTotalFees'])->name('reportsClassWiseTotalFees');
    Route::get('/classStudents/{classID}', [App\Http\Controllers\ReportsController::class , 'classStudents'])->name('classStudents');
    Route::get('/classStudentsFees/{classID}', [App\Http\Controllers\ReportsController::class , 'classStudentsFees'])->name('classStudentsFees');
    Route::get('/deleteStudent/{studentID}', [App\Http\Controllers\ReportsController::class , 'deleteStudent'])->name('deleteStudent');

    // Students Management (tenant-scoped)
    Route::get('/students', [App\Http\Controllers\StudentsController::class , 'index'])->name('students');
    Route::get('/students/overview/{id}', [App\Http\Controllers\StudentsController::class , 'overview'])->name('students.overview');
    Route::get('/students/view/{id}', [App\Http\Controllers\StudentsController::class , 'view'])->name('students.view');
    Route::get('/studentsListGRno', [App\Http\Controllers\StudentsController::class , 'studentsListGRno'])->name('studentsListGRno');
    Route::get('/studentsListSLC', [App\Http\Controllers\StudentsController::class , 'studentsListSLC'])->name('studentsListSLC');
    Route::get('/studentsWithSameGRno', [App\Http\Controllers\StudentsController::class , 'studentsWithSameGRno'])->name('studentsWithSameGRno');
    Route::post('/addStudent', [App\Http\Controllers\StudentsController::class , 'addStudent'])->name('addStudent');
    Route::get('/getStudents', [App\Http\Controllers\StudentsController::class , 'getStudents'])->name('getStudents');
    Route::post('/promoteStudent', [App\Http\Controllers\StudentsController::class , 'promoteStudent'])->name('promoteStudent');
    Route::get('/editStudent/{studentID}', [App\Http\Controllers\StudentsController::class , 'editStudent'])->name('editStudent');
    Route::post('/updateStudent', [App\Http\Controllers\StudentsController::class , 'updateStudent'])->name('updateStudent');
    // Add new parent from Students page modal
    Route::post('/addParentFromStudent', [App\Http\Controllers\ParentsController::class , 'addParentFromStudent'])->name('addParentFromStudent');

    Route::get('/attendance/students', [App\Http\Controllers\AttendanceController::class , 'students'])->name('attendance.students');
    Route::post('/attendance/students', [App\Http\Controllers\AttendanceController::class , 'storeStudents'])->name('attendance.students.store');
    Route::get('/attendance/teachers', [App\Http\Controllers\AttendanceController::class , 'teachers'])->name('attendance.teachers');
    Route::post('/attendance/teachers', [App\Http\Controllers\AttendanceController::class , 'storeTeachers'])->name('attendance.teachers.store');
    Route::get('/attendance/reports/students', [App\Http\Controllers\AttendanceController::class , 'studentReports'])->name('attendance.reports.students');
    Route::get('/attendance/reports/teachers', [App\Http\Controllers\AttendanceController::class , 'teacherReports'])->name('attendance.reports.teachers');
    Route::get('/attendance/reports/students-yearly', [App\Http\Controllers\AttendanceController::class , 'studentReportsYearly'])->name('attendance.reports.students-yearly');
    Route::get('/attendance/reports/teachers-yearly', [App\Http\Controllers\AttendanceController::class , 'teacherReportsYearly'])->name('attendance.reports.teachers-yearly');
});

// Public route for principal remarks admin index (read-only)
Route::get('/principal-remarks/admin', [App\Http\Controllers\PrincipalRemarksController::class , 'index'])->name('principal-remarks.index');

// Public static pages
Route::get('/sitemap.xml', function () {
    $content = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://www.getskooli.com/</loc>
    <lastmod>2025-11-20</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>https://www.getskooli.com/login/school</loc>
    <lastmod>2025-11-20</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc>https://www.getskooli.com/register/school</loc>
    <lastmod>2025-11-20</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc>https://www.getskooli.com/contact</loc>
    <lastmod>2025-11-20</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>
</urlset>';
    return response($content, 200)->header("Content-Type", "text/xml");
});

Route::view('/about', 'static.about')->name('about');
Route::view('/privacy', 'static.privacy')->name('privacy');
Route::view('/terms', 'static.terms')->name('terms');
Route::get('/contact', [App\Http\Controllers\ContactController::class , 'show'])->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class , 'store'])->name('contact.store');

// Blog Routes
Route::get('/blogs', [App\Http\Controllers\BlogController::class , 'index'])->name('blogs.index');
Route::get('/blogs/{slug}', [App\Http\Controllers\BlogController::class , 'show'])->name('blogs.show');

// SaaS: Tenant signup (central domain)
Route::get('/register/school', [App\Http\Controllers\Auth\TenantRegistrationController::class , 'show'])->name('tenant.register.show');
Route::post('/register/school', [App\Http\Controllers\Auth\TenantRegistrationController::class , 'store'])->name('tenant.register.store');
Route::post('/register/school/send-otp', [App\Http\Controllers\Auth\TenantRegistrationController::class , 'sendOTP'])->name('tenant.register.send_otp');

// OTP Password Reset Routes
Route::get('/forgot-password/otp', [App\Http\Controllers\Auth\OTPForgotPasswordController::class , 'show'])->name('password.otp.show');
Route::post('/forgot-password/otp/send', [App\Http\Controllers\Auth\OTPForgotPasswordController::class , 'sendOTP'])->name('password.otp.send');
Route::post('/forgot-password/otp/reset', [App\Http\Controllers\Auth\OTPForgotPasswordController::class , 'reset'])->name('password.otp.reset');


// SaaS central admin: tenants listing & billing (superadmin only)
Route::middleware(['auth', 'role:superadmin'])->prefix('saas-admin')->group(function () {
    Route::get('/tenants', [App\Http\Controllers\Central\TenantsAdminController::class , 'index'])->name('saas.tenants.index');
    Route::post('/tenants/{school}/suspend', [App\Http\Controllers\Central\TenantsAdminController::class , 'suspend'])->name('saas.tenants.suspend');
    Route::post('/tenants/{school}/resume', [App\Http\Controllers\Central\TenantsAdminController::class , 'resume'])->name('saas.tenants.resume');

    // Billing routes
    Route::get('/tenants/{school}/plans', [App\Http\Controllers\Central\BillingController::class , 'plans'])->name('billing.plans');
    Route::post('/tenants/{school}/checkout', [App\Http\Controllers\Central\BillingController::class , 'checkout'])->name('billing.checkout');
    Route::get('/tenants/{school}/success', [App\Http\Controllers\Central\BillingController::class , 'success'])->name('billing.success');
    Route::get('/tenants/{school}/cancel', [App\Http\Controllers\Central\BillingController::class , 'cancel'])->name('billing.cancel');
    Route::get('/tenants/{school}/billing-portal', [App\Http\Controllers\Central\BillingController::class , 'portal'])->name('billing.portal');
});

// Stripe webhook (public, verify via Stripe signing secret)
Route::post('/stripe/webhook', [App\Http\Controllers\Central\StripeWebhookController::class , 'handleWebhook'])->name('stripe.webhook');

// Onboarding auto-login (central, simple URL)
Route::get('/onboard/login/{token}', function (string $token) {
    $userId = \Illuminate\Support\Facades\Cache::pull('onboard_login_' . $token);
    if (!$userId) {
        return redirect()->route('login')->withErrors(['email' => 'Onboarding link expired. Please log in.']);
    }
    \Illuminate\Support\Facades\Auth::loginUsingId((int)$userId, true);
    return redirect()->route('home');
})->name('onboard.login');

// Superadmin Login
Route::get('/superadmin', [App\Http\Controllers\Central\SuperAdminAuthController::class , 'showLoginForm'])->name('superadmin.login');
Route::post('/superadmin', [App\Http\Controllers\Central\SuperAdminAuthController::class , 'login'])->name('superadmin.login.post');

// Superadmin dashboard, activities, and impersonation (strict superadmin only)
Route::middleware([\App\Http\Middleware\EnsureSuperAdmin::class])->prefix('superadmin')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Central\SuperAdminAuthController::class , 'logout'])->name('superadmin.logout');

    Route::get('/dashboard', [App\Http\Controllers\Central\SuperAdminController::class , 'dashboard'])->name('superadmin.dashboard');
    Route::get('/activities', [App\Http\Controllers\Central\SuperAdminController::class , 'activities'])->name('superadmin.activities');
    Route::get('/audit-logs', [App\Http\Controllers\Central\SuperAdminController::class , 'activitiesPage'])->name('superadmin.audit');
    Route::post('/impersonate/{tenant}', [App\Http\Controllers\Central\SuperAdminController::class , 'impersonate'])->name('superadmin.impersonate');
    Route::post('/leave-impersonation', [App\Http\Controllers\Central\SuperAdminController::class , 'leaveImpersonation'])->name('superadmin.leave_impersonation');
    Route::post('/impersonate-start', [App\Http\Controllers\Central\SuperAdminController::class , 'impersonateStart'])->name('superadmin.impersonate.start');
    Route::get('/schools-all', [App\Http\Controllers\Central\SuperAdminController::class , 'schoolsAll'])->name('superadmin.schools.all');
    Route::post('/schools/{school}/link-tenant', [App\Http\Controllers\Central\SuperAdminController::class , 'linkTenant'])->name('superadmin.schools.link-tenant');
    Route::post('/schools/{school}/delete', [App\Http\Controllers\Central\SuperAdminController::class , 'deleteSchool'])->name('superadmin.schools.delete');
    Route::get('/tenants-all', [App\Http\Controllers\Central\SuperAdminController::class , 'tenantsAll'])->name('superadmin.tenants.all');

    // Packages Management
    Route::resource('packages', App\Http\Controllers\Central\PackageController::class , ['as' => 'superadmin']);

    // Blog Management
    Route::resource('blogs', App\Http\Controllers\Central\BlogController::class , ['as' => 'superadmin']);
    Route::post('blogs/ai-generate', [App\Http\Controllers\Central\BlogController::class , 'generateWithAI'])->name('superadmin.blogs.ai-generate');
});
// Parents (tenant-scoped)
Route::get('/parents', [App\Http\Controllers\ParentsController::class , 'index'])->name('parents');
Route::get('/getParents', [App\Http\Controllers\ParentsController::class , 'getParents'])->name('getParents');
Route::post('/addParent', [App\Http\Controllers\ParentsController::class , 'addParent'])->name('addParent');
Route::get('/deleteParent/{parentID}', [App\Http\Controllers\ParentsController::class , 'deleteParent'])->name('deleteParent');
Route::post('/updateParent', [App\Http\Controllers\ParentsController::class , 'updateParent'])->name('updateParent');
Route::post('/addParentFromStudent', [App\Http\Controllers\ParentsController::class , 'addParentFromStudent'])->name('addParentFromStudent');
Route::get('/viewParent/{id}', [App\Http\Controllers\ParentsController::class , 'viewParent'])->name('viewParent');
Route::get('/parents/{id}/edit-json', [App\Http\Controllers\ParentsController::class , 'editJson'])->name('parents.edit-json');
// Online Exams (tenant-scoped)
Route::get('/exams', [App\Http\Controllers\ExamsController::class , 'index'])->name('exams.index');
Route::get('/exams/create', [App\Http\Controllers\ExamsController::class , 'create'])->name('exams.create');
Route::post('/exams', [App\Http\Controllers\ExamsController::class , 'store'])->name('exams.store');
Route::get('/exams/{exam}', [App\Http\Controllers\ExamsController::class , 'show'])->name('exams.show');
Route::get('/exams/{exam}/edit', [App\Http\Controllers\ExamsController::class , 'edit'])->name('exams.edit');
Route::put('/exams/{exam}', [App\Http\Controllers\ExamsController::class , 'update'])->name('exams.update');
Route::delete('/exams/{exam}', [App\Http\Controllers\ExamsController::class , 'destroy'])->name('exams.destroy');
Route::post('/exams/{exam}/toggle-status', [App\Http\Controllers\ExamsController::class , 'toggleStatus'])->name('exams.toggle-status');
Route::get('/exams/{exam}/results', [App\Http\Controllers\ExamsController::class , 'results'])->name('exams.results');

// Manual Exams (tenant-scoped)
Route::get('/manual-exams', [App\Http\Controllers\ManualExamsController::class , 'index'])->name('manual-exams.index');
Route::post('/manual-exams', [App\Http\Controllers\ManualExamsController::class , 'store'])->name('manual-exams.store');
// Dependent dropdowns / AJAX
Route::get('/manual-exams/sections/{classId}', [App\Http\Controllers\ManualExamsController::class , 'getSectionsByClassForExams'])->name('manual-exams.sections');
Route::get('/manual-exams/students/{classId}/{section?}', [App\Http\Controllers\ManualExamsController::class , 'getStudentsByClassAndSection'])->name('manual-exams.students');
Route::get('/manual-exams/subjects/{classId}', [App\Http\Controllers\ManualExamsController::class , 'getSubjectsByClassForExams'])->name('manual-exams.subjects');
Route::get('/manual-exams/subjects-by-term', [App\Http\Controllers\ManualExamsController::class , 'getSubjectsByClassAndTerm'])->name('manual-exams.subjects-by-term');
// Search and utilities
Route::get('/manual-exams/grno-search', [App\Http\Controllers\ManualExamsController::class , 'searchByGrno'])->name('manual-exams.grno-search');
// AI Analysis
Route::post('/manual-exams/ai-analyze', [App\Http\Controllers\ManualExamsController::class , 'aiAnalyze'])->name('manual-exams.ai-analyze');
Route::post('/manual-exams/upload-sheet', [App\Http\Controllers\ManualExamsController::class , 'uploadSheet'])->name('manual-exams.upload-sheet');
// CSV Import/Export
Route::get('/manual-exams/export-csv', [App\Http\Controllers\ManualExamsController::class , 'exportCsv'])->name('manual-exams.export-csv');
Route::post('/manual-exams/import-csv', [App\Http\Controllers\ManualExamsController::class , 'importCsv'])->name('manual-exams.import-csv');
// Exam Questions
Route::get('/exams/{exam}/questions/create', [App\Http\Controllers\ExamQuestionsController::class , 'create'])->name('exam-questions.create');
Route::get('/exams/{exam}/questions/{question}/edit', [App\Http\Controllers\ExamQuestionsController::class , 'edit'])->name('exam-questions.edit');
Route::delete('/exams/{exam}/questions/{question}', [App\Http\Controllers\ExamQuestionsController::class , 'destroy'])->name('exam-questions.destroy');
Route::post('/exam-questions', [App\Http\Controllers\ExamQuestionsController::class , 'store'])->name('exam-questions.store');
Route::get('/exam-questions/{question}', [App\Http\Controllers\ExamQuestionsController::class , 'show'])->name('exam-questions.show');
Route::put('/exam-questions/{question}', [App\Http\Controllers\ExamQuestionsController::class , 'update'])->name('exam-questions.update');
Route::delete('/exam-questions/{question}', [App\Http\Controllers\ExamQuestionsController::class , 'destroyById']);
// Print
Route::get('/manual-exams/print-all', [App\Http\Controllers\ManualExamsController::class , 'printAll'])->name('manual-exams.print-all');
Route::get('/manual-exams/print-all/pdf', [App\Http\Controllers\ManualExamsController::class , 'printAllPdf'])->name('manual-exams.print-all.pdf');
Route::get('/manual-exams/print-entry', [App\Http\Controllers\ManualExamsController::class , 'printEntry'])->name('manual-exams.print-entry');
// Behaviour + principal remarks
Route::post('/manual-exams/attributes-save', [App\Http\Controllers\ManualExamsController::class , 'saveAttributesAjax'])->name('manual-exams.attributes-save');
Route::get('/principal-remarks', [App\Http\Controllers\ManualExamsController::class , 'principalRemarks'])->name('principal-remarks');
Route::get('/seed-parents', [App\Http\Controllers\ParentsController::class , 'seedPakistaniParents'])->name('parents.seed');

Route::post('/manual-exams/report-extras', function (\Illuminate\Http\Request $request) {
    $behaviour = [];
    foreach (['Attitude', 'Discipline', 'Neatness & Hygiene', 'Manners & Habits', 'Respect for Elders', 'Care of Books, Dress & Property'] as $area) {
        $key = 'behaviour_' . md5($area);
        if ($request->has($key)) {
            $behaviour[$area] = $request->input($key);
        }
    }
    $studentId = (int)$request->input('student_id');
    $term = $request->input('term');
    $session = $request->input('session');
    $teacherId = (int)$request->input('teacher_id', 0);
    $classId = (int)$request->input('class_id', 0);
    $tenantId = auth()->user()->tenant_id ?? null;
    $row = \Illuminate\Support\Facades\DB::table('manual_exam_entries')
        ->when($tenantId, function ($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        }
        )
            ->where('student_id', $studentId)
            ->where('term', $term)
            ->where('session', $session)
            ->where('teacher_id', $teacherId)
            ->first();
        $data = [];
        if ($row) {
            $data = json_decode($row->data, true) ?: [];
        }
        $data['improvement_required'] = $request->input('improvement_required');
        $data['principal_remarks'] = $request->input('principal_remarks');
        $data['behaviour'] = $behaviour;
        \Illuminate\Support\Facades\DB::table('manual_exam_entries')->updateOrInsert([
            'tenant_id' => $tenantId,
            'student_id' => $studentId,
            'term' => $term,
            'session' => $session,
            'teacher_id' => $teacherId,
        ], [
            'class_id' => $classId,
            'teacher_id' => $teacherId,
            'data' => json_encode($data),
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        return response()->json(['ok' => true]);
    })->name('manual-exams.report-extras');

// ========================================
// CMS MANAGEMENT (School Admin)
// ========================================
Route::middleware(['auth'])->group(function () {
    require base_path('routes/tenant.php');
});

// ========================================
// PUBLIC SCHOOL LANDING PAGES
// ========================================
Route::group([
    'prefix' => '{school_slug}',
    'where' => ['school_slug' => '^((?!.*\.(png|jpg|jpeg|gif|css|js|ico|pdf|txt|xml)).)*$']
], function () {
    Route::get('/', [App\Http\Controllers\SchoolLandingController::class , 'show'])->name('school.landing');
    Route::get('/news', [App\Http\Controllers\SchoolLandingController::class , 'news'])->name('school.news');
    Route::get('/news/{news_slug}', [App\Http\Controllers\SchoolLandingController::class , 'showNews'])->name('school.news.show');

    Route::get('/events', [App\Http\Controllers\SchoolLandingController::class , 'events'])->name('school.events');
    Route::get('/events/{event_slug}', [App\Http\Controllers\SchoolLandingController::class , 'showEvent'])->name('school.event.show');

    Route::get('/blogs', [App\Http\Controllers\SchoolLandingController::class , 'blogs'])->name('school.blogs');
    Route::get('/blogs/{blog_slug}', [App\Http\Controllers\SchoolLandingController::class , 'showBlog'])->name('school.blog.show');

    Route::get('/gallery', [App\Http\Controllers\SchoolLandingController::class , 'gallery'])->name('school.gallery');

    Route::get('/contact', [App\Http\Controllers\SchoolLandingController::class , 'contact'])->name('school.contact');
});
