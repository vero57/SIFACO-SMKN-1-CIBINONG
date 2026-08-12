<?php

namespace Tests\Feature;

use App\Http\Controllers\dashboard\dash_feature\KelasController;
use App\Models\ClassModel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class KelasScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagi_schedule_type_updates_class_schedule(): void
    {
        $role = Role::create(['name' => 'Guru']);
        $teacher = User::create([
            'name' => 'Guru A',
            'email' => 'guru@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $class = ClassModel::create([
            'name' => 'Kelas 10',
            'walas_id' => $teacher->id,
        ]);

        $response = (new KelasController())->updateSchedule(new Request([
            'schedule_type' => 'pagi',
        ]), $class->id);

        $this->assertEquals(302, $response->getStatusCode());

        $schedule = $class->fresh()->attendanceSchedule;
        $this->assertNotNull($schedule);
        $this->assertSame('05:00:00', $schedule->start_time_in);
        $this->assertSame('07:00:00', $schedule->end_time_in);
        $this->assertSame('15:00:00', $schedule->start_time_out);
        $this->assertSame('18:00:00', $schedule->end_time_out);
    }
}
