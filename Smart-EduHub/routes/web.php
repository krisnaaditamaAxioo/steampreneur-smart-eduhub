<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagementUserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentPermitController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\AngkatanController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\PointRuleController;
use App\Http\Controllers\StudentFinanceController;
use App\Http\Controllers\AdminFinanceController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\StudentNotificationController;
use App\Http\Controllers\RaportIndustriController;
use App\Http\Controllers\ParentController;

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    return 'Cache cleared!';
});

Route::get('/show-logs', function () {
    $logFile = storage_path('logs/laravel.log');
    if (!file_exists($logFile)) {
        return 'Log file not found!';
    }
    $lines = file($logFile);
    $lastLines = array_slice($lines, -50);
    return '<pre>' . implode('', $lastLines) . '</pre>';
});

Route::get('/', function () {
    return view('welcome');
});

// Quick Login dari halaman login (tanpa password, hanya untuk user yang sudah pernah login di perangkat ini)
// Data user diambil dari localStorage browser perangkat, bukan dari server.
Route::post('/quick-login/guru/{user}', [AuthController::class, 'quickLoginAsGuru'])->name('quick-login.guru');
Route::post('/quick-login/admin/{user}', [AuthController::class, 'quickLoginAsAdmin'])->name('quick-login.admin');
Route::post('/quick-login/{user}', [AuthController::class, 'quickLogin'])->name('quick-login.user');  // Guru & Admin (model User)
Route::post('/quick-login/siswa/{student}', [AuthController::class, 'quickLoginStudent'])->name('quick-login.student');  // Siswa (model Student)
Route::post('/quick-login/wali/{parent}', [AuthController::class, 'quickLoginParent'])->name('quick-login.parent');  // Orang Tua (model ParentUser)

// Registration Public Routes
Route::get('/daftar', [RegistrationController::class, 'showForm'])->name('registrations.form');
Route::post('/daftar', [RegistrationController::class, 'submitForm'])->name('registrations.submit');
Route::get('/cek-status', [RegistrationController::class, 'showCheckStatus'])->name('registrations.check');
Route::get('/cek-status/proses', [RegistrationController::class, 'trackStatus'])->name('registrations.track');
Route::get('/download-info', [RegistrationController::class, 'downloadInfo'])->name('registrations.download-info');



Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/guru', [DashboardController::class, 'guruDashboard'])->name('guru.dashboard');
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
        Route::get('/students/export-excel', [StudentController::class, 'exportExcel'])->name('students.exportExcel');
        Route::get('/students/export-pdf', [StudentController::class, 'exportPdf'])->name('students.exportPdf');
        Route::get('/students/download-template', [StudentController::class, 'downloadTemplate'])->name('students.downloadTemplate');
        Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::resource('students', StudentController::class)->names([
        'index' => 'students',
    ]);
    Route::post('students/{student}/reset-pin', [StudentController::class, 'resetPin'])->name('students.reset-pin');
    Route::get('/students-print-cards', [StudentController::class, 'printCards'])->name('students.print-cards');
    Route::resource('management-users', ManagementUserController::class);
    Route::post('management-users/{user}/reset-password', [ManagementUserController::class, 'resetPassword'])->name('management-users.reset-password');
    Route::resource('parents', ParentController::class)->except(['show']);
    Route::resource('angkatans', AngkatanController::class)->except(['show']);
    
    Route::prefix('raport-industri')->name('raport.')->group(function () {
        Route::get('/', [RaportIndustriController::class, 'index'])->name('index');
        Route::get('/create', [RaportIndustriController::class, 'create'])->name('create');
        Route::post('/', [RaportIndustriController::class, 'store'])->name('store');
        Route::get('/{raport}/edit', [RaportIndustriController::class, 'edit'])->name('edit');
        Route::put('/{raport}', [RaportIndustriController::class, 'update'])->name('update');
        Route::delete('/{raport}', [RaportIndustriController::class, 'destroy'])->name('destroy');
        Route::get('/{raport}/print', [RaportIndustriController::class, 'print'])->name('print');
        Route::get('/get-students', [RaportIndustriController::class, 'getStudents'])->name('get-students');
        Route::get('/download-template', [RaportIndustriController::class, 'downloadTemplate'])->name('download-template');
        Route::post('/import', [RaportIndustriController::class, 'import'])->name('import');
        Route::get('/print-bulk', [RaportIndustriController::class, 'printBulk'])->name('print-bulk');
    });
    Route::resource('holidays', HolidayController::class)->names([
        'index' => 'holidays',
    ]);
    Route::post('/holidays/range', [HolidayController::class, 'storeRange'])->name('holidays.range.store');
    Route::get('/point-rules', [PointRuleController::class, 'index'])->name('point-rules.index');
    Route::get('/point-rules/create', [PointRuleController::class, 'create'])->name('point-rules.create');
    Route::post('/point-rules', [PointRuleController::class, 'store'])->name('point-rules.store');
    Route::get('/point-rules/{id}/edit', [PointRuleController::class, 'edit'])->name('point-rules.edit');
    Route::put('/point-rules/{id}', [PointRuleController::class, 'update'])->name('point-rules.update');
    Route::delete('/point-rules/{id}', [PointRuleController::class, 'destroy'])->name('point-rules.destroy');
    

    
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/academic-year', [SettingsController::class, 'setAcademicYear'])->name('settings.set-academic-year');
    Route::post('/settings/academic-years/store', [SettingsController::class, 'storeAcademicYear'])->name('settings.academic-years.store');
    Route::post('/settings/academic-years/{academicYear}/delete', [SettingsController::class, 'destroyAcademicYear'])->name('settings.academic-years.destroy');
    Route::post('/settings/academic-years/{academicYear}/set-active', [SettingsController::class, 'activateAcademicYear'])->name('settings.academic-years.activate');
    Route::post('/settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
    Route::post('/settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');
    Route::get('/settings/backup-all', [SettingsController::class, 'backupAll'])->name('settings.backup-all');
    Route::post('/settings/reset-all', [SettingsController::class, 'resetAll'])->name('settings.reset-all');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::get('/attendance/bulk', [AttendanceController::class, 'showBulkForm'])->name('attendance.bulk');
    Route::post('/attendance/bulk', [AttendanceController::class, 'bulkStore'])->name('attendance.bulk.store');
    Route::resource('attendance', AttendanceController::class)->names([
        'index' => 'attendance',
    ]);
    
    Route::get('/points/leaderboard', [PointController::class, 'leaderboard'])->name('points.leaderboard');
    Route::resource('points', PointController::class)->names([
        'index' => 'points',
    ]);

    Route::get('/letters', [LetterController::class, 'index'])->name('letters.index');
    Route::get('/letters/create', [LetterController::class, 'create'])->name('letters.create');
    Route::post('/letters', [LetterController::class, 'store'])->name('letters.store');
    Route::patch('/letters/{letter}/status', [LetterController::class, 'updateStatus'])->name('letters.status');
    Route::delete('/letters/{letter}', [LetterController::class, 'destroy'])->name('letters.destroy');
    Route::get('/letters/print/{student}/{type}', [LetterController::class, 'print'])->name('letters.print');
    Route::get('/registrations', [RegistrationController::class, 'index'])->name('registrations');
    Route::get('/registrations/report/pdf', [RegistrationController::class, 'printReport'])->name('registrations.report.pdf');
    Route::get('/registrations/{registration}', [RegistrationController::class, 'show'])->name('registrations.show');
    Route::patch('/registrations/{registration}/status', [RegistrationController::class, 'updateStatus'])->name('registrations.status');
    Route::delete('/registrations/{registration}', [RegistrationController::class, 'destroy'])->name('registrations.destroy');
    
    // Admin Exam Management
    Route::get('/management/exams', [ExamController::class, 'index'])->name('admin.exams.index');
    Route::post('/management/exams', [ExamController::class, 'storeQuestion'])->name('admin.exams.store');
    Route::post('/management/exams/import', [ExamController::class, 'importQuestions'])->name('admin.exams.import');
    Route::get('/management/exams/questions/export', [ExamController::class, 'exportQuestions'])->name('admin.exams.questions.export');
    Route::get('/management/exams/questions/template', [ExamController::class, 'downloadQuestionsTemplate'])->name('admin.exams.questions.template');
    Route::get('/management/exams/{question}/edit', [ExamController::class, 'editQuestion'])->name('admin.exams.edit');
    Route::put('/management/exams/{question}', [ExamController::class, 'updateQuestion'])->name('admin.exams.update');
    Route::delete('/management/exams/{question}', [ExamController::class, 'deleteQuestion'])->name('admin.exams.delete');
    Route::get('/management/exams/results', [ExamController::class, 'results'])->name('admin.exams.results');
    Route::get('/management/exams/results/top', [ExamController::class, 'topResults'])->name('admin.exams.results.top');
    Route::delete('/management/exams/results/{result}', [ExamController::class, 'deleteResult'])->name('admin.exams.results.delete');
    Route::get('/management/exams/export', [ExamController::class, 'exportResults'])->name('admin.exams.export');
    Route::get('/management/exams/results/export/excel', [ExamController::class, 'exportResultsExcel'])->name('admin.exams.results.export.excel');
    Route::get('/management/exams/results/export/pdf', [ExamController::class, 'exportResultsPdf'])->name('admin.exams.results.export.pdf');

    // Admin Course & Module Management
    Route::get('/courses/rekap-nilai', [CourseController::class, 'allRekapNilai'])->name('admin.courses.all-rekap');
    Route::get('/courses/{course}/rekap-nilai', [CourseController::class, 'rekapNilai'])->name('admin.courses.rekap-nilai');
    Route::get('/courses/{course}/rekap-nilai/cetak', [CourseController::class, 'printRekapNilai'])->name('admin.courses.rekap-nilai.print');
    Route::resource('courses', CourseController::class)->names('admin.courses');
    Route::get('/courses/{course}/progress', [CourseController::class, 'progress'])->name('admin.courses.progress');
    Route::get('/courses/{course}/modules/create', [CourseController::class, 'createModule'])->name('admin.courses.modules.create');
    Route::post('/courses/{course}/modules', [CourseController::class, 'storeModule'])->name('admin.courses.modules.store');
    Route::get('/modules/{module}/preview', [CourseController::class, 'previewModule'])->name('admin.courses.modules.preview');
    Route::get('/modules/{module}/edit', [CourseController::class, 'editModule'])->name('admin.courses.modules.edit');
    Route::put('/modules/{module}', [CourseController::class, 'updateModule'])->name('admin.courses.modules.update');
    Route::delete('/modules/{module}', [CourseController::class, 'destroyModule'])->name('admin.courses.modules.destroy');
    Route::get('/modules/{module}/questions', [CourseController::class, 'questions'])->name('admin.courses.modules.questions');
    Route::post('/modules/{module}/questions', [CourseController::class, 'storeQuestion'])->name('admin.courses.modules.questions.store');
    Route::put('/module-questions/{question}', [CourseController::class, 'updateQuestion'])->name('admin.courses.modules.questions.update');
    Route::delete('/module-questions/{question}', [CourseController::class, 'destroyQuestion'])->name('admin.courses.modules.questions.destroy');
    Route::get('/modules/{module}/submissions', [CourseController::class, 'moduleSubmissions'])->name('admin.courses.modules.submissions');
    Route::post('/submissions/{submission}/grade', [CourseController::class, 'storeGradeSubmission'])->name('admin.courses.modules.grade.store');
    Route::delete('/submissions/{submission}', [CourseController::class, 'destroySubmission'])->name('admin.courses.modules.submissions.destroy');

    // Admin Student Permit Management
    Route::get('/student-permits', [StudentPermitController::class, 'index'])->name('admin.permits.index');
    Route::post('/student-permits', [StudentPermitController::class, 'store'])->name('admin.permits.store');
    Route::put('/student-permits/{permit}', [StudentPermitController::class, 'update'])->name('admin.permits.update');
    Route::patch('/student-permits/{permit}/status', [StudentPermitController::class, 'updateStatus'])->name('admin.permits.status');
    Route::delete('/student-permits/{permit}', [StudentPermitController::class, 'destroy'])->name('admin.permits.destroy');
    
    // Admin Finance Management
    Route::get('/finance', [AdminFinanceController::class, 'index'])->name('admin.finance.index');
    Route::get('/finance/dashboard', [AdminFinanceController::class, 'dashboard'])->name('admin.finance.dashboard');
    Route::get('/finance/fast-payment', [AdminFinanceController::class, 'fastPayment'])->name('admin.finance.fast-payment');
    Route::get('/finance/fast-payment/search', [AdminFinanceController::class, 'fastPaymentSearch'])->name('admin.finance.fast-payment.search');
    Route::post('/finance/fast-payment/process', [AdminFinanceController::class, 'fastPaymentProcess'])->name('admin.finance.fast-payment.process');
    Route::get('/finance/fast-transaction', [AdminFinanceController::class, 'fastTransaction'])->name('admin.finance.fast-transaction');
    Route::get('/finance/fast-transaction/search', [AdminFinanceController::class, 'fastTransactionSearch'])->name('admin.finance.fast-transaction.search');
    Route::get('/finance/rekap', [AdminFinanceController::class, 'rekap'])->name('admin.finance.rekap');
    Route::get('/finance/transaction-search', [AdminFinanceController::class, 'transactionSearch'])->name('admin.finance.transaction-search');
    Route::get('/finance/search-card', [AdminFinanceController::class, 'searchCardByNis'])->name('admin.finance.search-card');
    Route::get('/finance/requests', [AdminFinanceController::class, 'requests'])->name('admin.finance.requests');
    Route::patch('/finance/requests/{cardRequestId}/approve', [AdminFinanceController::class, 'approveRequest'])->name('admin.finance.requests.approve');
    Route::patch('/finance/requests/{cardRequestId}/reject', [AdminFinanceController::class, 'rejectRequest'])->name('admin.finance.requests.reject');
    Route::get('/finance/cards', [AdminFinanceController::class, 'cards'])->name('admin.finance.cards');
    Route::get('/finance/cards/{card}', [AdminFinanceController::class, 'showCard'])->name('admin.finance.cards.show');
    Route::post('/finance/cards/{card}/topup', [AdminFinanceController::class, 'topupCard'])->name('admin.finance.cards.topup');
    Route::post('/finance/cards/{card}/withdraw', [AdminFinanceController::class, 'withdrawCard'])->name('admin.finance.cards.withdraw');
    Route::post('/finance/cards/{card}/payment', [AdminFinanceController::class, 'payment'])->name('admin.finance.cards.payment');
    Route::patch('/finance/cards/{card}/status', [AdminFinanceController::class, 'updateCardStatus'])->name('admin.finance.cards.status');
    Route::get('/finance/transactions', [AdminFinanceController::class, 'transactions'])->name('admin.finance.transactions.index');
    Route::get('/finance/transactions/{transaction}/receipt', [AdminFinanceController::class, 'showReceipt'])->name('admin.finance.transactions.receipt');
    Route::get('/finance/transactions/{transaction}/edit', [AdminFinanceController::class, 'editTransaction'])->name('admin.finance.transactions.edit');
    Route::patch('/finance/transactions/{transaction}', [AdminFinanceController::class, 'updateTransaction'])->name('admin.finance.transactions.update');
    Route::delete('/finance/transactions/{transaction}', [AdminFinanceController::class, 'destroyTransaction'])->name('admin.finance.transactions.destroy');
    Route::get('/finance/payments/history', [AdminFinanceController::class, 'paymentHistory'])->name('admin.finance.payments.history');
    Route::get('/finance/transaction-history', [AdminFinanceController::class, 'transactionHistory'])->name('admin.finance.transaction-history.index');
    Route::get('/finance/transaction-history/{history}', [AdminFinanceController::class, 'showTransactionHistory'])->name('admin.finance.transaction-history.show');
    Route::get('/finance/requests/create', [AdminFinanceController::class, 'createRequest'])->name('admin.finance.requests.create');
    Route::post('/finance/requests', [AdminFinanceController::class, 'storeRequest'])->name('admin.finance.requests.store');

    // Finance Export Routes
    Route::get('/finance/rekap/export/excel', [AdminFinanceController::class, 'exportRekapExcel'])->name('admin.finance.rekap.export.excel');
    Route::get('/finance/rekap/export/pdf', [AdminFinanceController::class, 'exportRekapPdf'])->name('admin.finance.rekap.export.pdf');
    Route::get('/finance/cards/export/pdf', [AdminFinanceController::class, 'exportCardsPdf'])->name('admin.finance.cards.export-pdf');
    Route::get('/finance/cards/{card}/export/excel', [AdminFinanceController::class, 'exportStudentExcel'])->name('admin.finance.cards.export.excel');
    Route::get('/finance/cards/{card}/export/pdf', [AdminFinanceController::class, 'exportStudentPdf'])->name('admin.finance.cards.export.pdf');

    // Admin Complaints Management
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('admin.complaints.index');
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('admin.complaints.show');
    Route::put('/complaints/{complaint}', [ComplaintController::class, 'update'])->name('admin.complaints.update');
    Route::delete('/complaints/{complaint}', [ComplaintController::class, 'destroy'])->name('admin.complaints.destroy');

    // Admin Notifications Management (Student Notifications)
    Route::resource('notifications', StudentNotificationController::class)->names([
        'index' => 'admin.notifications.index',
        'create' => 'admin.notifications.create',
        'store' => 'admin.notifications.store',
        'edit' => 'admin.notifications.edit',
        'update' => 'admin.notifications.update',
        'destroy' => 'admin.notifications.destroy',
    ]);

    // Admin Notifications API & Actions (Admin's own notifications)
    Route::prefix('admin-notifications')->name('admin.notifications.')->group(function () {
        Route::get('/unread-count', function () {
            return response()->json(['count' => auth()->user()->unreadAdminNotifications()->count()]);
        })->name('unread-count');
        
        Route::get('/new', [AdminNotificationController::class, 'getNewNotifications'])->name('new');
        
        Route::post('/{id}/read', [AdminNotificationController::class, 'markAsRead'])->name('read');
        
        Route::post('/read-all', function () {
            auth()->user()->adminNotifications()->update(['read_at' => now()]);
            return back();
        })->name('read-all');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Guru Impersonate (hanya admin)
    Route::post('/impersonate/guru/{user}', [AuthController::class, 'impersonateGuru'])->name('impersonate.guru');
    Route::post('/impersonate/guru/stop', [AuthController::class, 'stopImpersonateGuru'])->name('impersonate.guru.stop');
});

// Public Registration Routes
Route::get('/join', [RegistrationController::class, 'showForm'])->name('registrations.form');
Route::post('/join', [RegistrationController::class, 'submitForm'])->name('registrations.submit');
Route::get('/join/check', [RegistrationController::class, 'showCheckStatus'])->name('registrations.check');
Route::get('/join/status', [RegistrationController::class, 'trackStatus'])->name('registrations.track');
Route::get('/join/download', [RegistrationController::class, 'downloadInfo'])->name('registrations.download');

// Exam Routes (Public/Student)
Route::get('/exam/start/{id}', [ExamController::class, 'start'])->name('exams.start');
Route::post('/exam/submit/{id}', [ExamController::class, 'submit'])->name('exams.submit');

// Attendance Scan Routes
Route::get('/attendance-scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
Route::post('/attendance-scan', [AttendanceController::class, 'processScan'])->name('attendance.process-scan');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/login/admin', [AuthController::class, 'showAdminLogin'])->name('login.admin');
    Route::post('/login/admin', [AuthController::class, 'loginAdmin'])->name('login.admin.submit');
    Route::get('/login/guru', [AuthController::class, 'showGuruLogin'])->name('login.guru');
    Route::post('/login/guru', [AuthController::class, 'loginGuru'])->name('login.guru.submit');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
});

// Student Portal Routes
Route::prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/login', [StudentPortalController::class, 'showLogin'])->name('login');
    Route::post('/login', [StudentPortalController::class, 'login']);
    Route::get('/forgot-pin', [StudentPortalController::class, 'showForgotPin'])->name('forgot-pin');
    Route::post('/forgot-pin', [StudentPortalController::class, 'processForgotPin'])->name('forgot-pin.process');
    Route::get('/reset-pin', [StudentPortalController::class, 'showResetPin'])->name('reset-pin');
    Route::post('/reset-pin', [StudentPortalController::class, 'processResetPin'])->name('reset-pin.update');
    Route::get('/share/transactions/{transaction}/receipt', [StudentFinanceController::class, 'sharedReceipt'])
        ->middleware('signed')
        ->name('finance.transactions.share');
    
    Route::middleware('auth:student')->group(function () {
        Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [StudentPortalController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [StudentPortalController::class, 'updateProfile'])->name('profile.update');
        Route::get('/attendance', [StudentPortalController::class, 'attendance'])->name('attendance');
        Route::get('/points', [StudentPortalController::class, 'points'])->name('points');
        Route::get('/courses', [StudentPortalController::class, 'courses'])->name('courses');
        Route::get('/modules/{course_id?}', [StudentPortalController::class, 'modules'])->name('modules');
        Route::get('/module/read/{id}', [StudentPortalController::class, 'showModule'])->name('module.show');
        Route::get('/module/{id}/pdf', [StudentPortalController::class, 'downloadModulePdf'])->name('module.pdf');
        Route::get('/module/soal/{id}', [StudentPortalController::class, 'showModuleSoal'])->name('module.soal');
        Route::post('/module/{id}/finish-read', [StudentPortalController::class, 'finishReadingModule'])->name('module.finish-read');
        Route::post('/module/{id}/progress', [StudentPortalController::class, 'updateModuleProgress'])->name('module.progress');
        Route::post('/module/{id}/assignment/draft', [StudentPortalController::class, 'saveDraftAssignment'])->name('module.assignment.draft');
        Route::post('/module/{id}/assignment', [StudentPortalController::class, 'submitAssignment'])->name('module.assignment.submit');
        Route::get('/permit', [StudentPortalController::class, 'permit'])->name('permit');
        Route::post('/permit', [StudentPortalController::class, 'storePermit'])->name('permit.store');
        Route::delete('/permit/{permit}', [StudentPortalController::class, 'destroyPermit'])->name('permit.destroy');
        Route::get('/letters', [StudentPortalController::class, 'letters'])->name('letters');
        Route::get('/letters/view/{student}/{type}', [LetterController::class, 'print'])->name('letters.view');
        
        // Notifications
        Route::get('/notifications', [StudentPortalController::class, 'notifications'])->name('notifications');
        Route::get('/notifications/{id}', [StudentPortalController::class, 'notificationDetail'])->name('notifications.show');
        Route::get('/api/notifications/unread-count', function() {
            return response()->json([
                'count' => auth('student')->user()->unreadNotifications()->count()
            ]);
        })->name('api.notifications.unread-count');
        Route::get('/api/notifications/latest', function() {
            // Ambil notifikasi terbaru yang belum dibaca dan belum ditampilkan
            $lastShownId = request('last_shown_id', 0);
            $notifications = auth('student')->user()
                ->notifications()
                ->where('read', false)
                ->where('id', '>', $lastShownId)
                ->latest()
                ->get();
            return response()->json([
                'notifications' => $notifications
            ]);
        })->name('api.notifications.latest');
        Route::post('/notifications/{id}/read', function($id) {
            $notification = \App\Models\StudentNotification::findOrFail($id);
            $notification->update(['read' => true]);
            return back();
        })->name('notifications.read');
        Route::post('/notifications/read-all', function() {
            auth('student')->user()->notifications()->update(['read' => true]);
            return back();
        })->name('notifications.read-all');
        
        // Finance Routes
        Route::get('/finance', [StudentFinanceController::class, 'index'])->name('finance');
        Route::get('/finance/history', [StudentFinanceController::class, 'history'])->name('finance.history');
        Route::get('/finance/request-card', [StudentFinanceController::class, 'showRequestCard'])->name('finance.request-card');
        Route::post('/finance/request-card', [StudentFinanceController::class, 'storeRequestCard'])->name('finance.request-card.store');
        Route::get('/finance/transfer', [StudentFinanceController::class, 'showTransferForm'])->name('finance.transfer');
        Route::post('/finance/transfer/confirm', [StudentFinanceController::class, 'confirmTransfer'])->name('finance.transfer.confirm');
        Route::get('/finance/transfer/pin', [StudentFinanceController::class, 'showPinForm'])->name('finance.transfer.pin');
        Route::post('/finance/transfer/process', [StudentFinanceController::class, 'processTransfer'])->name('finance.transfer.process');
        Route::get('/finance/transactions/{transaction}', [StudentFinanceController::class, 'showTransaction'])->name('finance.transactions.show');
        Route::get('/finance/transactions/{transaction}/receipt', [StudentFinanceController::class, 'receipt'])->name('finance.transactions.receipt');
        
        // Student Complaints
        Route::get('/complaints', [ComplaintController::class, 'myComplaints'])->name('complaints.index');
        Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
        Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
        Route::get('/complaints/{complaint}', [ComplaintController::class, 'myShow'])->name('complaints.show');
        Route::delete('/complaints/{complaint}', [ComplaintController::class, 'myDestroy'])->name('complaints.destroy');
        
        // Grades
        Route::get('/grades', [StudentPortalController::class, 'grades'])->name('grades');
        Route::get('/grades/cetak', [StudentPortalController::class, 'printGrades'])->name('grades.print');
        
        Route::post('/logout', [StudentPortalController::class, 'logout'])->name('logout');
    });
});

// Parent / Wali Murid Portal Routes
use App\Http\Controllers\ParentAuthController;
use App\Http\Controllers\ParentPortalController;

Route::prefix('wali')->name('parent.')->group(function () {
    Route::get('/login', [ParentAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ParentAuthController::class, 'login']);
    Route::get('/register', [ParentAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [ParentAuthController::class, 'register']);
    Route::get('/forgot-password', [ParentAuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [ParentAuthController::class, 'processForgotPassword'])->name('forgot-password.process');
    Route::get('/reset-password', [ParentAuthController::class, 'showResetPassword'])->name('reset-password');
    Route::post('/reset-password', [ParentAuthController::class, 'processResetPassword'])->name('reset-password.update');

    Route::middleware('auth:parent')->group(function () {
        Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/attendance', [ParentPortalController::class, 'attendance'])->name('attendance');
        Route::get('/finance', [ParentPortalController::class, 'finance'])->name('finance');
        Route::get('/points', [ParentPortalController::class, 'points'])->name('points');
        Route::get('/permit', [ParentPortalController::class, 'permit'])->name('permit');
        Route::post('/permit', [ParentPortalController::class, 'storePermit'])->name('permit.store');
        Route::delete('/permit/{permit}', [ParentPortalController::class, 'destroyPermit'])->name('permit.destroy');
        Route::get('/letters', [ParentPortalController::class, 'letters'])->name('letters');
        Route::get('/notifications', [ParentPortalController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{notification}/read', [ParentPortalController::class, 'markNotificationRead'])->name('notifications.read');
        Route::get('/complaints', [ParentPortalController::class, 'complaints'])->name('complaints');
        Route::post('/complaints', [ParentPortalController::class, 'storeComplaint'])->name('complaints.store');
        Route::get('/complaints/{complaint}', [ParentPortalController::class, 'complaintShow'])->name('complaints.show');
        Route::delete('/complaints/{complaint}', [ParentPortalController::class, 'destroyComplaint'])->name('complaints.destroy');
        Route::get('/profile', [ParentPortalController::class, 'profile'])->name('profile');
        Route::put('/profile', [ParentPortalController::class, 'updateProfile'])->name('profile.update');
        Route::post('/logout', [ParentAuthController::class, 'logout'])->name('logout');
    });
});

use App\Http\Controllers\ConfigController;

Route::get('/ab_acp/fetch_settings.php', [ConfigController::class, 'fetchSettings']);
Route::get('/ab_acp/fetch_students.php', [ConfigController::class, 'fetchStudents']);
// Mengizinkan GET dan POST sementara untuk debugging
Route::match(['get', 'post'], '/ab_acp/save_attendance.php', [ConfigController::class, 'saveAttendance']);