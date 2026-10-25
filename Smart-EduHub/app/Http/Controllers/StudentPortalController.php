<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\Point;
use App\Models\Course;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Models\AssignmentSubmission;
use App\Models\StudentPermit;
use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentPortalController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('siswa.dashboard');
        }
        return view('siswa.login');
    }

    public function login(Request $request)
    {
        $scanNis = preg_replace('/\D+/', '', (string) $request->input('scan_nis', ''));
        if ($scanNis !== '') {
            $student = Student::whereNotNull('nis_siswa')
                ->get()
                ->first(function (Student $candidate) use ($scanNis) {
                    return preg_replace('/\D+/', '', (string) $candidate->nis_siswa) === $scanNis;
                });

            if ($student) {
                Auth::guard('student')->login($student, true);
                $request->session()->regenerate();
                return redirect()->intended(route('siswa.dashboard'));
            }

            return back()->withErrors([
                'scan_nis' => 'NIS pada ID card tidak ditemukan. Silakan periksa data siswa.',
            ]);
        }

        $credentials = $request->validate([
            'username' => 'required',
            'pin' => 'required',
        ]);

        $username = trim($credentials['username']);
        $student = Student::where('username', $username)
            ->orWhere('nisn', $username)
            ->orWhere('id_siswa', $username)
            ->orWhere('nis_siswa', $username)
            ->first();

        if ($student && (string)$student->pin === (string)$credentials['pin']) {
            Auth::guard('student')->login($student, true);
            $request->session()->regenerate();
            return redirect()->intended(route('siswa.dashboard'));
        }

        return back()->withErrors([
            'username' => 'Kredensial NISN / Username atau PIN tidak sesuai.',
        ])->onlyInput('username');
    }

    public function letters()
    {
        $student = Auth::guard('student')->user();
        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
        $letters = Letter::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->latest()
            ->get();
            
        return view('siswa.surat', compact('student', 'letters'));
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('siswa.login');
    }

    public function dashboard()
    {
        $student = Auth::guard('student')->user();
        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
        $academicYearId = \App\Models\AcademicYear::getIdByName($academicYear);

        $recentAttendances = $student->attendances()
            ->where('academic_year', $academicYear)
            ->latest()->take(5)->get();
            
        $recentPoints = $student->points()
            ->where('academic_year', $academicYear)
            ->latest()->take(5)->get();
        
        $totalViolation = $student->points()
            ->where('type', 'violation')
            ->where('academic_year', $academicYear)
            ->sum('score');
            
        $totalAchievement = $student->points()
            ->where('type', 'achievement')
            ->where('academic_year', $academicYear)
            ->sum('score');
            
        $netPoints = max(0, $totalViolation - $totalAchievement);
        
        $activePermit = StudentPermit::where('student_id', $student->id)
            ->where('status', 'pending')
            ->first();
        
        $violationAlert = null;
        if ($netPoints >= 100) {
            $violationAlert = [
                'type' => 'danger',
                'title' => 'TEGURAN KERAS',
                'message' => 'Poin pelanggaran Anda telah mencapai batas maksimal (100 poin). Segera temui Bagian Kesiswaan.'
            ];
        } elseif ($netPoints >= 70) {
            $violationAlert = [
                'type' => 'warning',
                'title' => 'PANGGILAN ORANG TUA',
                'message' => 'Poin pelanggaran Anda telah mencapai 70 poin. Sekolah akan segera mengirimkan surat panggilan kepada orang tua/wali Anda.'
            ];
        } elseif ($netPoints >= 50) {
            $violationAlert = [
                'type' => 'info',
                'title' => 'PERINGATAN KEDISIPLINAN',
                'message' => 'Poin pelanggaran Anda telah mencapai 50 poin. Harap perhatikan kedisiplinan Anda agar tidak bertambah.'
            ];
        }

        // Calculate attendance rate for the current month
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        
        $attendances = $student->attendances()
            ->where('academic_year', $academicYear)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

        // Calculate total holidays in period
        $period = \Carbon\CarbonPeriod::create($start_date, $end_date);
        $totalHolidays = 0;
        foreach ($period as $date) {
            if (\App\Models\Holiday::checkHoliday($date->format('Y-m-d'))) {
                $totalHolidays++;
            }
        }
        $totalDays = $period->count();
        $workingDays = $totalDays - $totalHolidays;

        $present = $attendances->whereIn('status', ['present', 'late', 'excused'])->count();
        $attendanceRate = $workingDays > 0 ? round(($present / $workingDays) * 100) : 0;

        // Determine attendance status
        if ($attendanceRate >= 95) {
            $attendanceStatus = 'Sangat Baik ✓';
            $statusColor = 'emerald';
        } elseif ($attendanceRate >= 85) {
            $attendanceStatus = 'Baik ✓';
            $statusColor = 'indigo';
        } elseif ($attendanceRate >= 75) {
            $attendanceStatus = 'Cukup !';
            $statusColor = 'amber';
        } else {
            $attendanceStatus = 'Kurang !';
            $statusColor = 'rose';
        }

        // Get teachers for consultation
        $teachers = collect();

        // Get wali kelas from student's angkatan
        if ($student->angkatan_id) {
            $angkatan = \App\Models\Angkatan::with('waliKelas')->find($student->angkatan_id);
            if ($angkatan && $angkatan->waliKelas) {
                $teachers->push($angkatan->waliKelas);
            }
        }

        // Get all BK teachers
        $bkTeachers = \App\Models\User::where('role', 'bk')->get();
        $teachers = $teachers->merge($bkTeachers)->unique('id');

        return view('siswa.dashboard', compact(
            'student', 
            'recentAttendances', 
            'recentPoints', 
            'netPoints', 
            'activePermit', 
            'violationAlert', 
            'teachers',
            'attendanceRate',
            'attendanceStatus',
            'statusColor'
        ));
    }

    public function profile()
    {
        $student = Auth::guard('student')->user();
        return view('siswa.profile', compact('student'));
    }

    public function editProfile()
    {
        $student = Auth::guard('student')->user();
        return view('siswa.edit_profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:students,email,' . $student->id,
            'address' => 'nullable|string',
            'place_of_birth' => 'nullable|string|max:100',
            'dateofbirth' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pin' => 'nullable|string|digits:6|confirmed',
        ]);

        if ($request->filled('pin')) {
            $validated['pin'] = $request->pin;
        } else {
            unset($validated['pin']);
        }

        if ($request->hasFile('foto')) {
            if ($student->foto) {
                Storage::disk('public')->delete($student->foto);
            }
            $path = $request->file('foto')->store('students', 'public');
            $validated['foto'] = $path;
        }

        $student->update($validated);

        return redirect()->route('siswa.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function attendance(Request $request)
    {
        $student = Auth::guard('student')->user();
        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
        
        $start_date = $request->input('start_date', date('Y-m-01'));
        $end_date = $request->input('end_date', date('Y-m-t'));
        $mode = $request->input('mode', 'detail');

        $attendances = $student->attendances()
            ->where('academic_year', $academicYear)
            ->whereBetween('date', [$start_date, $end_date])
            ->latest()
            ->get();

        // Calculate total holidays and attendance stats in period
        $period = \Carbon\CarbonPeriod::create($start_date, $end_date);
        $stats = [
            'present' => 0,
            'late' => 0,
            'absent' => 0,
            'excused' => 0,
        ];
        
        $today = date('Y-m-d');
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $isHoliday = \App\Models\Holiday::checkHoliday($dateStr);
            $attendance = $attendances->where('date', $dateStr)->first();
            $isFutureDate = $dateStr > $today;

            if ($isHoliday) {
                continue;
            }

            if ($attendance) {
                if ($attendance->status === 'present') {
                    $stats['present']++;
                } elseif ($attendance->status === 'late') {
                    $stats['late']++;
                } elseif ($attendance->status === 'excused') {
                    $stats['excused']++;
                } elseif ($attendance->status === 'absent') {
                    $stats['absent']++;
                }
            } elseif (!$isFutureDate) {
                $stats['absent']++;
            }
        }
        
        if ($mode === 'summary') {
            $months = [];
            $angkatan = $student->angkatan?->name ?? null;

            foreach ($period as $date) {
                $monthKey = $date->format('Y-m');
                if (!isset($months[$monthKey])) {
                    $months[$monthKey] = [
                        'name' => $date->format('F Y'),
                        'key' => $monthKey
                    ];
                }
            }

            if ($request->has('print')) {
                $students = collect([$student]);
                $headmaster = \App\Models\Setting::get('headmaster_name', '..........................');
                $headmaster_nip = \App\Models\Setting::get('headmaster_nip', '..........................');

                return view('attendance.print_summary_report', compact('students', 'months', 'start_date', 'end_date', 'headmaster', 'headmaster_nip', 'angkatan'));
            }

            return view('siswa.riwayat_absen_summary', compact('student', 'attendances', 'stats', 'months', 'start_date', 'end_date', 'mode', 'angkatan'));
        }

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        if ($request->has('print')) {
            $students = collect([$student]);
            $headmaster = \App\Models\Setting::get('headmaster_name', '..........................');
            $headmaster_nip = \App\Models\Setting::get('headmaster_nip', '..........................');
            
            return view('attendance.print_report', compact('students', 'dates', 'start_date', 'end_date', 'headmaster', 'headmaster_nip'));
        }

        return view('siswa.riwayat_absen', compact('student', 'attendances', 'stats', 'dates', 'start_date', 'end_date', 'mode'));
    }

    public function points()
    {
        $student = Auth::guard('student')->user();
        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
        $academicYearId = \App\Models\AcademicYear::getIdByName($academicYear);
        
        $points = $student->points()
            ->where('academic_year', $academicYear)
            ->latest()
            ->paginate(10);
        return view('siswa.riwayat_point', compact('student', 'points'));
    }

    public function courses()
    {
        $student = Auth::guard('student')->user();
        $courses = Course::with('modules')->where(function ($query) use ($student) {
            $query->whereNull('angkatan_id')
                  ->orWhere('angkatan_id', $student->angkatan_id);
        })->get();

        $moduleIds = $courses->flatMap(fn ($course) => $course->modules->pluck('id'));
        $progresses = ModuleProgress::where('student_id', $student->id)
            ->whereIn('module_id', $moduleIds)
            ->get()
            ->keyBy('module_id');

        $courses->each(function ($course) use ($progresses) {
            $total = $course->modules->count();
            $course->student_progress = $total
                ? (int) round($course->modules->sum(fn ($module) => $progresses[$module->id]->progress ?? 0) / $total)
                : 0;
        });

        return view('siswa.kursus', compact('student', 'courses'));
    }

    public function modules($course_id = null)
    {
        $student = Auth::guard('student')->user();
        if ($course_id) {
            $course = Course::where(function ($query) use ($student) {
                $query->whereNull('angkatan_id')
                      ->orWhere('angkatan_id', $student->angkatan_id);
            })->findOrFail($course_id);
            $modules = $course->modules()->with(['course', 'questions'])->latest()->get();
        } else {
            $modules = Module::whereHas('course', function ($query) use ($student) {
                $query->whereNull('angkatan_id')
                      ->orWhere('angkatan_id', $student->angkatan_id);
            })->with(['course', 'questions'])->latest()->get();
        }

        $progresses = ModuleProgress::where('student_id', $student->id)
            ->whereIn('module_id', $modules->pluck('id'))
            ->get()
            ->keyBy('module_id');

        $submissions = AssignmentSubmission::where('student_id', $student->id)
            ->whereIn('module_id', $modules->pluck('id'))
            ->get()
            ->keyBy('module_id');

        $modules->each(function ($module) use ($progresses, $submissions) {
            $module->student_progress = $progresses[$module->id]->progress ?? 0;
            $module->is_completed = ($module->student_progress >= 100);

            $sub = $submissions->get($module->id);
            if ($sub && in_array($sub->status, ['submitted', 'graded']) && $module->questions->isNotEmpty()) {
                $ansArr = is_array(json_decode($sub->answer, true)) ? json_decode($sub->answer, true) : [];
                $answeredQIds = array_keys($ansArr);
                $unanswered = $module->questions->reject(function($q) use ($answeredQIds) {
                    return in_array($q->id, $answeredQIds) || in_array((string)$q->id, $answeredQIds);
                });

                if ($unanswered->count() > 0) {
                    $module->has_new_questions = true;
                    $module->unanswered_count = $unanswered->count();
                    $module->is_completed = false;
                    $module->student_progress = max(50, min(90, (int) round(($module->questions->count() - $unanswered->count()) / $module->questions->count() * 100)));
                }
            }
        });

        return view('siswa.modul', compact('student', 'modules'));
    }

    public function showModule($id)
    {
        $student = Auth::guard('student')->user();
        $module = Module::with(['course', 'questions'])->findOrFail($id);
        $questions = $module->questions;
        $progress = ModuleProgress::firstOrCreate(
            ['student_id' => $student->id, 'module_id' => $module->id],
            ['progress' => 50, 'opened_at' => now(), 'last_seen_at' => now()]
        );
        $submission = $student->assignmentSubmissions()->where('module_id', $module->id)->latest('submitted_at')->first();

        return view('siswa.baca_modul', compact('student', 'module', 'progress', 'submission', 'questions'));
    }

    public function showModuleSoal($id)
    {
        $student = Auth::guard('student')->user();
        $module = Module::with(['course', 'questions'])->findOrFail($id);

        if ($module->questions->isEmpty() && !in_array($module->type, ['assignment', 'quiz', 'coding'], true)) {
            return redirect()->route('siswa.module.show', $id);
        }

        $progress = ModuleProgress::firstOrCreate(
            ['student_id' => $student->id, 'module_id' => $module->id],
            ['progress' => 50, 'opened_at' => now(), 'last_seen_at' => now()]
        );

        $submission = $student->assignmentSubmissions()->where('module_id', $module->id)->latest('submitted_at')->first();
        $questions = $module->questions;

        return view('siswa.pengerjaan_soal', compact('student', 'module', 'progress', 'submission', 'questions'));
    }

    public function finishReadingModule($id)
    {
        $student = Auth::guard('student')->user();
        $module = Module::with('questions')->findOrFail($id);

        ModuleProgress::updateOrCreate(
            ['student_id' => $student->id, 'module_id' => $module->id],
            ['progress' => 100, 'opened_at' => now(), 'last_seen_at' => now(), 'completed_at' => now()]
        );

        if ($module->questions->count() > 0) {
            return redirect()->route('siswa.module.soal', $module->id)
                ->with('success', 'Materi telah selesai dibaca. Silakan kerjakan soal di bawah ini.');
        }

        return redirect()->route('siswa.modules')
            ->with('success', 'Selamat! Anda telah menyelesaikan modul: ' . $module->title);
    }

    public function updateModuleProgress(Request $request, $id)
    {
        $student = Auth::guard('student')->user();
        Module::findOrFail($id);
        $progressValue = max(0, min(100, (int) $request->input('progress', 0)));

        $progress = ModuleProgress::firstOrNew(['student_id' => $student->id, 'module_id' => $id]);
        $progress->progress = max($progress->progress ?? 0, $progressValue);
        $progress->opened_at ??= now();
        $progress->last_seen_at = now();
        if ($progress->progress >= 100) {
            $progress->completed_at ??= now();
        }
        $progress->save();

        return response()->json(['progress' => $progress->progress]);
    }

    public function saveDraftAssignment(Request $request, $id)
    {
        $student = Auth::guard('student')->user();
        $module = Module::with('questions')->findOrFail($id);

        $existing = AssignmentSubmission::where('student_id', $student->id)->where('module_id', $module->id)->first();
        $existingAnswers = ($existing && $existing->answer) ? (is_array(json_decode($existing->answer, true)) ? json_decode($existing->answer, true) : []) : [];

        if ($existing && in_array($existing->status, ['submitted', 'graded'])) {
            if ($module->questions->isNotEmpty()) {
                $answeredQIds = array_keys($existingAnswers);
                $unanswered = $module->questions->reject(function($q) use ($answeredQIds) {
                    return in_array($q->id, $answeredQIds) || in_array((string)$q->id, $answeredQIds);
                });
                if ($unanswered->isEmpty()) {
                    return back()->withErrors(['error' => 'Jawaban Anda untuk semua soal di modul ini sudah dikirim dan telah dikunci.']);
                }
            } else {
                return back()->withErrors(['error' => 'Jawaban Anda sudah dikirim dan telah dikunci. Pengiriman ulang tidak diizinkan.']);
            }
        }

        $answers = $request->input('answers', []);
        $answerFiles = $existing->answer_files ?? [];

        if ($module->questions->isNotEmpty()) {
            foreach ($module->questions as $question) {
                if ($request->hasFile("answer_files.{$question->id}")) {
                    $upFile = $request->file("answer_files.{$question->id}");
                    $storedPath = $upFile->store('assignment-submissions', 'public');
                    $answerFiles[$question->id] = $storedPath;
                }
            }
        }

        $mergedAnswers = $existingAnswers;
        if (is_array($answers)) {
            foreach ($answers as $qId => $val) {
                if (!isset($existingAnswers[$qId]) && !isset($existingAnswers[(string)$qId])) {
                    $mergedAnswers[$qId] = $val;
                }
            }
        }

        $data = [
            'student_id' => $student->id,
            'module_id' => $module->id,
            'answer' => $module->questions->isNotEmpty() ? json_encode($mergedAnswers) : ($request->input('answer') ?? null),
            'submitted_at' => now(),
            'status' => 'draft',
            'answer_files' => $answerFiles ?: null,
        ];

        if ($request->hasFile('file')) {
            $upFile = $request->file('file');
            $storedPath = $upFile->store('assignment-submissions', 'public');
            $data['file_path'] = $storedPath;
        }

        AssignmentSubmission::updateOrCreate(
            ['student_id' => $student->id, 'module_id' => $module->id],
            $data
        );

        ModuleProgress::updateOrCreate(
            ['student_id' => $student->id, 'module_id' => $module->id],
            ['progress' => 50, 'opened_at' => now(), 'last_seen_at' => now()]
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Draf berhasil disimpan secara sementara!']);
        }

        return back()->with('success', 'Draf jawaban berhasil disimpan secara sementara.');
    }

    public function submitAssignment(Request $request, $id)
    {
        $student = Auth::guard('student')->user();
        $module = Module::with('questions')->findOrFail($id);

        $existing = AssignmentSubmission::where('student_id', $student->id)->where('module_id', $module->id)->first();
        $existingAnswers = ($existing && $existing->answer) ? (is_array(json_decode($existing->answer, true)) ? json_decode($existing->answer, true) : []) : [];

        if ($existing && in_array($existing->status, ['submitted', 'graded'])) {
            if ($module->questions->isNotEmpty()) {
                $answeredQIds = array_keys($existingAnswers);
                $unanswered = $module->questions->reject(function($q) use ($answeredQIds) {
                    return in_array($q->id, $answeredQIds) || in_array((string)$q->id, $answeredQIds);
                });
                if ($unanswered->isEmpty()) {
                    return back()->withErrors(['error' => 'Jawaban Anda untuk semua soal di modul ini sudah dikirim dan telah dikunci.']);
                }
            } else {
                return back()->withErrors(['error' => 'Jawaban Anda sudah dikirim dan telah dikunci. Pengiriman ulang tidak diizinkan.']);
            }
        }

        $rules = ['answer' => 'nullable|string|max:10000', 'file' => 'nullable|file|mimes:pdf|max:20480'];
        if ($module->questions->isNotEmpty()) {
            $rules['answers'] = 'required|array';
            $rules['answers.*'] = 'nullable|string|max:10000';
            $rules['answer_files'] = 'nullable|array';
            $rules['answer_files.*'] = 'nullable|file|mimes:pdf|max:20480';
        }
        $validated = $request->validate($rules);

        $answerFiles = $existing->answer_files ?? [];
        $score = null;
        $maxScore = null;
        $questionScores = [];

        if ($module->questions->isNotEmpty()) {
            $inputAnswers = $validated['answers'];
            $mergedAnswers = $existingAnswers;
            foreach ($inputAnswers as $qId => $val) {
                if (!isset($existingAnswers[$qId]) && !isset($existingAnswers[(string)$qId])) {
                    $mergedAnswers[$qId] = $val;
                }
            }

            $score = 0;
            $maxScore = $module->questions->sum('score');
            $questionScores = is_array($existing->question_scores ?? null) ? $existing->question_scores : [];

            foreach ($module->questions as $question) {
                if ($question->type === 'multiple_choice') {
                    $ansVal = $mergedAnswers[$question->id] ?? ($mergedAnswers[(string)$question->id] ?? null);
                    $isCorrect = $ansVal === $question->correct_answer;
                    $qScore = $isCorrect ? $question->score : 0;
                    $questionScores[$question->id] = $qScore;
                    $score += $qScore;
                }
                if ($request->hasFile("answer_files.{$question->id}")) {
                    $upFile = $request->file("answer_files.{$question->id}");
                    $storedPath = $upFile->store('assignment-submissions', 'public');
                    $answerFiles[$question->id] = $storedPath;
                }
            }
            $validated['answer'] = json_encode($mergedAnswers);
        }

        if ($module->questions->isEmpty() && blank($validated['answer'] ?? null) && !$request->hasFile('file')) {
            return back()->withErrors(['answer' => 'Isi jawaban atau unggah berkas tugas terlebih dahulu.']);
        }

        $data = [
            'student_id' => $student->id,
            'module_id' => $module->id,
            'answer' => $validated['answer'] ?? null,
            'submitted_at' => now(),
            'status' => 'submitted',
            'score' => $score,
            'max_score' => $maxScore,
            'answer_files' => $answerFiles ?: null,
            'question_scores' => $questionScores ?: null,
        ];

        if ($request->hasFile('file')) {
            $upFile = $request->file('file');
            $storedPath = $upFile->store('assignment-submissions', 'public');
            $data['file_path'] = $storedPath;
        }

        AssignmentSubmission::updateOrCreate(
            ['student_id' => $student->id, 'module_id' => $module->id],
            $data
        );

        ModuleProgress::updateOrCreate(
            ['student_id' => $student->id, 'module_id' => $module->id],
            ['progress' => 100, 'opened_at' => now(), 'last_seen_at' => now(), 'completed_at' => now()]
        );

        return redirect()->route('siswa.modules')->with('success', 'Jawaban/Soal berhasil dikumpulkan. Progres modul telah 100% Selesai.');
    }

    public function grades(Request $request)
    {
        $student = Auth::guard('student')->user();

        $submissions = AssignmentSubmission::where('student_id', $student->id)
            ->with(['module.course', 'module.questions'])
            ->latest('submitted_at')
            ->get();

        $gradedSubmissions = $submissions->whereNotNull('score');
        $totalGraded = $submissions->where('status', 'graded')->count();
        $avgScore = $gradedSubmissions->count() > 0
            ? round($gradedSubmissions->avg(function ($submission) {
                $maxScore = $submission->max_score ?: 100;
                return $maxScore > 0 ? ($submission->score / $maxScore) * 100 : 0;
            }), 1)
            : 0;

        return view('siswa.grades', compact('student', 'submissions', 'totalGraded', 'avgScore'));
    }

    public function printGrades()
    {
        $student = Auth::guard('student')->user();
        $submissions = AssignmentSubmission::where('student_id', $student->id)
            ->with(['module.course', 'module.questions'])
            ->whereNotNull('score')
            ->latest('submitted_at')
            ->get();

        $gradedPercentages = $submissions->map(function ($submission) {
            $maxScore = $submission->max_score ?: 100;
            return $maxScore > 0 ? ($submission->score / $maxScore) * 100 : 0;
        });
        $avgScore = $gradedPercentages->count() ? round($gradedPercentages->average(), 1) : 0;

        return view('siswa.grades_print', compact('student', 'submissions', 'avgScore'));
    }

    public function notifications()
    {
        $student = Auth::guard('student')->user();
        $notifications = $student->notifications()->latest()->paginate(20);
        return view('siswa.notifications', compact('student', 'notifications'));
    }

    public function notificationDetail($id)
    {
        $student = Auth::guard('student')->user();
        $notification = $student->notifications()->findOrFail($id);

        if (!$notification->read) {
            $notification->update(['read' => true]);
        }

        return view('siswa.notification_detail', compact('student', 'notification'));
    }

    public function permit()
    {
        $student = Auth::guard('student')->user();
        $permits = $student->permits()->latest()->get();
        
        // Get contact persons: only admins
        $contacts = \App\Models\User::where('role', 'admin')->get()->filter(function($user) {
            return !empty($user->no_wa);
        });
        
        return view('siswa.pengajuan_ijin', compact('student', 'permits', 'contacts'));
    }

    public function storePermit(Request $request)
    {
        $student = Auth::guard('student')->user();
        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
        
        $validated = $request->validate([
            'type' => 'required|in:sakit,izin,dispen',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'proof_photo' => 'nullable|image|max:2048',
        ]);

        $validated['student_id'] = $student->id;
        $validated['status'] = 'pending';
        $validated['academic_year'] = $academicYear;

        if ($request->hasFile('proof_photo')) {
            $path = $request->file('proof_photo')->store('permits', 'public');
            $validated['proof_photo'] = $path;
        }

        StudentPermit::create($validated);

        return back()->with('success', 'Pengajuan izin berhasil dikirim.');
    }

    public function destroyPermit(StudentPermit $permit)
    {
        $student = Auth::guard('student')->user();

        if (!$student || $permit->student_id !== $student->id) {
            abort(403);
        }

        // Pengajuan yang sudah diproses admin tidak boleh dihapus.
        if (in_array($permit->status, ['approved', 'rejected'], true)) {
            return back()->with('error', 'Pengajuan yang sudah diproses tidak dapat dihapus.');
        }

        if ($permit->proof_photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($permit->proof_photo);
        }

        $permit->delete();

        return back()->with('success', 'Pengajuan izin berhasil dihapus.');
    }

    public function showForgotPin()
    {
        return view('siswa.forgot-pin');
    }

    public function processForgotPin(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string',
            'verification_field' => 'required|in:email,phone,mother_name,dateofbirth',
            'verification_value' => 'required|string',
        ]);

        $identifier = trim($validated['identifier']);
        $student = Student::where('username', $identifier)
            ->orWhere('nisn', $identifier)
            ->orWhere('id_siswa', $identifier)
            ->orWhere('nis_siswa', $identifier)
            ->first();

        if (!$student) {
            return back()->withErrors([
                'identifier' => 'Data siswa dengan identitas tersebut tidak ditemukan.',
            ])->onlyInput('identifier', 'verification_field');
        }

        $field = $validated['verification_field'];
        $inputValue = trim($validated['verification_value']);
        $match = false;

        if ($field === 'email') {
            $match = !empty($student->email) && strcasecmp($student->email, $inputValue) === 0;
        } elseif ($field === 'phone') {
            $cleanStudent = preg_replace('/\D+/', '', (string) $student->phone);
            $cleanInput = preg_replace('/\D+/', '', $inputValue);
            $match = !empty($cleanStudent) && $cleanStudent === $cleanInput
                || (function() use ($student, $cleanInput) {
                    $cleanParent = preg_replace('/\D+/', '', (string) $student->parent_phone);
                    return !empty($cleanParent) && $cleanParent === $cleanInput;
                })();
        } elseif ($field === 'mother_name') {
            $match = !empty($student->mother_name) && strcasecmp(trim($student->mother_name), $inputValue) === 0;
        } elseif ($field === 'dateofbirth') {
            $formatted = date('Y-m-d', strtotime($inputValue));
            $match = !empty($student->dateofbirth) && substr($student->dateofbirth, 0, 10) === $formatted;
        }

        if (!$match) {
            return back()->withErrors([
                'verification_value' => 'Data verifikasi tidak cocok dengan data siswa.',
            ])->onlyInput('identifier', 'verification_field');
        }

        $token = Str::random(64);
        $request->session()->put('reset_pin_token', [
            'token' => $token,
            'student_id' => $student->id,
            'identifier' => $identifier,
            'expires_at' => now()->addMinutes(30),
        ]);

        return redirect()->route('siswa.reset-pin', [
            'token' => $token,
            'identifier' => $identifier,
        ])->with('success', 'Verifikasi berhasil. Silakan masukkan PIN baru Anda.');
    }

    public function showResetPin(Request $request)
    {
        $token = $request->query('token');
        $identifier = $request->query('identifier');
        $sessionToken = $request->session()->get('reset_pin_token');

        if (!$sessionToken
            || $sessionToken['token'] !== $token
            || $sessionToken['identifier'] !== $identifier
            || now()->greaterThan($sessionToken['expires_at'])) {
            return redirect()->route('siswa.forgot-pin')
                ->withErrors(['reset_token' => 'Link reset PIN tidak valid atau sudah kedaluwarsa. Silakan coba lagi.']);
        }

        return view('siswa.reset-pin', compact('token', 'identifier'));
    }

    public function processResetPin(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'identifier' => 'required|string',
            'pin' => 'required|string|digits:6|confirmed',
        ]);

        $sessionToken = $request->session()->get('reset_pin_token');
        if (!$sessionToken
            || $sessionToken['token'] !== $validated['token']
            || $sessionToken['identifier'] !== $validated['identifier']
            || now()->greaterThan($sessionToken['expires_at'])) {
            return redirect()->route('siswa.forgot-pin')
                ->withErrors(['reset_token' => 'Link reset PIN tidak valid atau sudah kedaluwarsa. Silakan coba lagi.']);
        }

        $student = Student::find($sessionToken['student_id']);
        if (!$student) {
            return redirect()->route('siswa.forgot-pin')
                ->withErrors(['reset_token' => 'Data siswa tidak ditemukan.']);
        }

        $student->update(['pin' => $validated['pin']]);
        $request->session()->forget('reset_pin_token');

        return redirect()->route('siswa.login')
            ->with('success', 'PIN Anda berhasil direset. Silakan login dengan PIN baru Anda.');
    }
}
