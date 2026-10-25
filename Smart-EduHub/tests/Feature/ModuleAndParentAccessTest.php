<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleAndParentAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_only_sees_modules_created_by_himself(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        $otherTeacher = User::factory()->create(['role' => 'guru']);

        $course = Course::create(['title' => 'Course A']);
        $otherCourse = Course::create(['title' => 'Course B']);

        Module::create([
            'course_id' => $course->id,
            'title' => 'Modul Saya',
            'type' => 'text',
            'created_by' => $teacher->id,
        ]);

        Module::create([
            'course_id' => $otherCourse->id,
            'title' => 'Modul Guru Lain',
            'type' => 'text',
            'created_by' => $otherTeacher->id,
        ]);

        $this->actingAs($teacher)
            ->get(route('admin.courses.index'))
            ->assertOk()
            ->assertSee('Modul Saya')
            ->assertDontSee('Modul Guru Lain');
    }

    public function test_parent_management_page_is_accessible(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('parents.index'))
            ->assertOk();
    }

    public function test_student_can_download_module_content_as_pdf(): void
    {
        $student = Student::factory()->create(['status' => 0]);
        $course = Course::create(['title' => 'Kursus PDF']);
        $module = Module::create([
            'course_id' => $course->id,
            'title' => 'Materi PDF',
            'type' => 'text',
            'content' => '<p>Isi materi untuk PDF.</p>',
        ]);

        $this->actingAs($student, 'student')
            ->get(route('siswa.module.pdf', $module->id))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=materi-pdf.pdf');
    }

    public function test_guru_dashboard_shows_own_courses_and_modules(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);

        $course = Course::create(['title' => 'Kursus Guru']);

        Module::create([
            'course_id' => $course->id,
            'title' => 'Modul Saya',
            'type' => 'text',
            'created_by' => $teacher->id,
        ]);

        $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertRedirect(route('guru.dashboard'));

        $this->actingAs($teacher)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Guru')
            ->assertSee('Kursus Guru')
            ->assertSee('Modul Saya');
    }
}
