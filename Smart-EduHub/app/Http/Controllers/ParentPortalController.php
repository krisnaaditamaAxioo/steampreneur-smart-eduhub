<?php

namespace App\Http\Controllers;

use App\Models\StudentPermit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentPortalController extends Controller
{
    private function getParentStudent()
    {
        $parent = Auth::guard('parent')->user();
        return $parent->student;
    }

    public function dashboard()
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;

        if (!$student) {
            return view('parent.dashboard', [
                'parent' => $parent,
                'student' => null,
                'recentAttendances' => collect(),
                'recentPoints' => collect(),
                'netPoints' => 0,
                'activePermit' => null,
                'violationAlert' => null,
                'attendanceRate' => 0,
                'attendanceStatus' => 'Tidak Ada Data',
                'statusColor' => 'slate',
            ]);
        }

        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));

        // Presensi 5 Terakhir
        $recentAttendances = $student->attendances()
            ->where('academic_year', $academicYear)
            ->latest('date')
            ->take(5)
            ->get();

        // Point kedisiplinan
        $recentPoints = $student->points()
            ->where('academic_year', $academicYear)
            ->latest('date')
            ->take(5)
            ->get();

        $totalViolationPoints = $student->points()
            ->where('academic_year', $academicYear)
            ->where('type', 'violation')
            ->sum('score');

        $totalRewardPoints = $student->points()
            ->where('academic_year', $academicYear)
            ->where('type', 'achievement')
            ->sum('score');

        $initialPoints = 100;
        $netPoints = $initialPoints - $totalViolationPoints + $totalRewardPoints;

        // Permohonan izin aktif
        $activePermit = $student->permits()
            ->where('academic_year', $academicYear)
            ->where('status', 'pending')
            ->latest()
            ->first();

        // Peringatan Poin
        $violationAlert = null;
        if ($netPoints <= 50) {
            $violationAlert = [
                'level' => 'danger',
                'title' => 'Peringatan Kedisiplinan Kritis!',
                'message' => "Sisa poin kedisiplinan anak Anda saat ini tinggal {$netPoints} poin. Harap melakukan pembinaan dan bimbingan.",
            ];
        } elseif ($netPoints <= 75) {
            $violationAlert = [
                'level' => 'warning',
                'title' => 'Peringatan Kedisiplinan (Poin Rendah)',
                'message' => "Sisa poin kedisiplinan anak Anda adalah {$netPoints} poin.",
            ];
        }

        // Statistik Presensi
        $totalDays = $student->attendances()->where('academic_year', $academicYear)->count();
        $presentDays = $student->attendances()
            ->where('academic_year', $academicYear)
            ->whereIn('status', ['present', 'late'])
            ->count();

        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 100;

        if ($attendanceRate >= 90) {
            $attendanceStatus = 'Sangat Baik';
            $statusColor = 'emerald';
        } elseif ($attendanceRate >= 75) {
            $attendanceStatus = 'Cukup Baik';
            $statusColor = 'amber';
        } else {
            $attendanceStatus = 'Perlu Perhatian';
            $statusColor = 'rose';
        }

        return view('parent.dashboard', compact(
            'parent',
            'student',
            'recentAttendances',
            'recentPoints',
            'netPoints',
            'activePermit',
            'violationAlert',
            'attendanceRate',
            'attendanceStatus',
            'statusColor'
        ));
    }

    public function attendance(Request $request)
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;
        if (!$student) return back()->with('error', 'Siswa tidak ditemukan.');

        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));

        $start_date = $request->input('start_date', date('Y-m-01'));
        $end_date = $request->input('end_date', date('Y-m-t'));

        $attendances = $student->attendances()
            ->where('academic_year', $academicYear)
            ->whereBetween('date', [$start_date, $end_date])
            ->latest()
            ->get();

        // Hitung statistik per hari (sama seperti portal siswa):
        // - hari libur diabaikan, - hari tanpa catatan yang sudah lewat dihitung Alpa.
        $stats = [
            'present' => 0,
            'late' => 0,
            'absent' => 0,
            'excused' => 0,
        ];

        $period = \Carbon\CarbonPeriod::create($start_date, $end_date);
        $dates = [];
        $today = date('Y-m-d');

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dates[] = $dateStr;

            if (\App\Models\Holiday::checkHoliday($dateStr)) {
                continue;
            }

            $att = $attendances->firstWhere('date', $dateStr);

            if ($att) {
                if (in_array($att->status, ['present', 'late', 'excused', 'absent'], true)) {
                    $stats[$att->status]++;
                }
            } elseif ($dateStr <= $today) {
                $stats['absent']++;
            }
        }

        if ($request->has('print')) {
            $headmaster = \App\Models\Setting::get('headmaster_name', '..........................');
            $headmaster_nip = \App\Models\Setting::get('headmaster_nip', '..........................');

            return view('parent.print_attendance', compact(
                'parent',
                'student',
                'attendances',
                'dates',
                'start_date',
                'end_date',
                'headmaster',
                'headmaster_nip'
            ));
        }

        return view('parent.attendance', compact('parent', 'student', 'attendances', 'stats', 'dates', 'start_date', 'end_date'));
    }

    public function finance()
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;
        if (!$student) return back()->with('error', 'Siswa tidak ditemukan.');

        $cards = $student->cards;
        $requests = $student->cardRequests()->latest()->get();

        $cardIds = $cards->pluck('id')->toArray();
        $transactions = \App\Models\StudentCardTransaction::whereIn('card_id', $cardIds)
            ->latest()
            ->paginate(15);

        return view('parent.finance', compact('parent', 'student', 'cards', 'requests', 'transactions'));
    }

    public function points()
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;
        if (!$student) return back()->with('error', 'Siswa tidak ditemukan.');

        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));

        $points = $student->points()
            ->where('academic_year', $academicYear)
            ->latest()
            ->paginate(10);

        // Ringkasan poin seluruh tahun ajaran (bukan hanya halaman yang tampil).
        $violationCount    = $student->points()->where('academic_year', $academicYear)->where('type', 'violation')->count();
        $achievementCount  = $student->points()->where('academic_year', $academicYear)->where('type', 'achievement')->count();
        $violationPoints   = $student->points()->where('academic_year', $academicYear)->where('type', 'violation')->sum('score');
        $achievementPoints = $student->points()->where('academic_year', $academicYear)->where('type', 'achievement')->sum('score');
        $totalPoints       = $violationPoints + $achievementPoints;

        return view('parent.points', compact(
            'parent', 'student', 'points',
            'violationCount', 'achievementCount', 'violationPoints', 'achievementPoints', 'totalPoints'
        ));
    }

    public function permit()
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;
        if (!$student) return back()->with('error', 'Siswa tidak ditemukan.');

        $permits = $student->permits()->latest()->get();

        // Kontak admin yang bisa dihubungi lewat WhatsApp (khusus pengajuan menunggu).
        $contacts = \App\Models\User::where('role', 'admin')->get()->filter(fn ($u) => !empty($u->no_wa));

        return view('parent.permit', compact('parent', 'student', 'permits', 'contacts'));
    }

    public function storePermit(Request $request)
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;
        if (!$student) return back()->with('error', 'Siswa tidak ditemukan.');

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

        return back()->with('success', 'Pengajuan izin sakit/keperluan oleh Wali Murid berhasil dikirim.');
    }

    public function destroyPermit(StudentPermit $permit)
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;

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

    public function letters()
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;
        if (!$student) return back()->with('error', 'Siswa tidak ditemukan.');

        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));

        $letters = \App\Models\Letter::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->latest()
            ->get();

        return view('parent.letters', compact('parent', 'student', 'letters'));
    }

    public function notifications()
    {
        $parent = Auth::guard('parent')->user();
        $notifications = $parent->notifications()->latest()->paginate(20);

        return view('parent.notifications', compact('parent', 'notifications'));
    }

    public function markNotificationRead(\App\Models\ParentNotification $notification)
    {
        $parent = Auth::guard('parent')->user();
        abort_unless($notification->parent_id === $parent->id, 403);

        $notification->update(['read' => true]);

        return redirect()->route('parent.letters');
    }

    public function complaints()
    {
        $parent = Auth::guard('parent')->user();
        $complaints = $parent->complaints()->latest()->get();

        return view('parent.complaints', compact('parent', 'complaints'));
    }

    public function storeComplaint(Request $request)
    {
        $parent = Auth::guard('parent')->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'proof_photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('proof_photo')) {
            $path = $request->file('proof_photo')->store('complaints', 'public');
            $validated['proof_photo'] = $path;
        }

        $parent->complaints()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'proof_photo' => $validated['proof_photo'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengaduan berhasil dikirim ke Pihak Sekolah/Admin.');
    }

    public function complaintShow(\App\Models\Complaint $complaint)
    {
        $parent = Auth::guard('parent')->user();

        if ($complaint->complainant_id != $parent->id || $complaint->complainant_type != $parent->getMorphClass()) {
            abort(403);
        }

        return view('parent.complaint_detail', compact('parent', 'complaint'));
    }

    public function destroyComplaint(\App\Models\Complaint $complaint)
    {
        $parent = Auth::guard('parent')->user();

        if ($complaint->complainant_id != $parent->id || $complaint->complainant_type != $parent->getMorphClass()) {
            abort(403);
        }

        if ($complaint->proof_photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($complaint->proof_photo);
        }

        $complaint->delete();

        return redirect()->route('parent.complaints')->with('success', 'Pengaduan berhasil dihapus.');
    }

    public function profile()
    {
        $parent = Auth::guard('parent')->user();

        return view('parent.profile', compact('parent'));
    }

    public function updateProfile(Request $request)
    {
        $parent = Auth::guard('parent')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:parents,email,' . $parent->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:L,P',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->filled('password')) {
            // Kolom password sudah memakai cast 'hashed', jadi simpan nilai aslinya saja
            $validated['password'] = $request->password;
        } else {
            unset($validated['password']);
        }

        $parent->update($validated);

        return redirect()->route('parent.profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
