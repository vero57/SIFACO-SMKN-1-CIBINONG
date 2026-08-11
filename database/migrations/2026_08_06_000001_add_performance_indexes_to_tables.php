<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan database indexes untuk performa query yang lebih efisien.
 * Semua foreign key mendapatkan index eksplisit untuk JOIN/lookup yang cepat.
 */
return new class extends Migration
{
    public function up(): void
    {
        // -- attendances --
        // Kolom yang sering di-query: student_id (siapa absen), date (hari ini?),
        // status_id (filter status), class_id (filter kelas).
        // Composite (student_id, date) paling sering dipakai untuk cek "sudah absen hari ini".
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['student_id', 'date'], 'idx_attendances_student_date');
            $table->index('status_id', 'idx_attendances_status_id');
            $table->index('class_id', 'idx_attendances_class_id');
            $table->index('date', 'idx_attendances_date');
        });

        // -- permissions (izin/sakit) --
        // Query: cari izin siswa hari ini yang sudah disetujui
        Schema::table('permissions', function (Blueprint $table) {
            $table->index('student_id', 'idx_permissions_student_id');
            $table->index('status', 'idx_permissions_status');
            $table->index(['student_id', 'status'], 'idx_permissions_student_status');
        });

        // -- student_details --
        // Foreign key user_id sering dipakai untuk JOIN ke users
        Schema::table('student_details', function (Blueprint $table) {
            $table->index('user_id', 'idx_student_details_user_id');
        });

        // -- violation_points --
        // Query: total poin pelanggaran per siswa, lookup per attendance
        Schema::table('violation_points', function (Blueprint $table) {
            $table->index('student_id', 'idx_violation_points_student_id');
            $table->index('attendance_id', 'idx_violation_points_attendance_id');
            $table->index('rule_id', 'idx_violation_points_rule_id');
        });

        // -- face_logs --
        // Lookup log per siswa
        Schema::table('face_logs', function (Blueprint $table) {
            $table->index('student_id', 'idx_face_logs_student_id');
        });

        // -- journals --
        // Query: jurnal per siswa
        Schema::table('journals', function (Blueprint $table) {
            $table->index('student_id', 'idx_journals_student_id');
        });

        // -- class_student (pivot) --
        // Unique composite untuk cegah duplikat & percepat lookup
        Schema::table('class_student', function (Blueprint $table) {
            // Cek apakah unique constraint sudah ada, kalau belum tambahkan
            $table->unique(['class_id', 'student_id'], 'uniq_class_student');
        });

        // -- attendance_schedules --
        // Lookup schedule berdasarkan class_id
        Schema::table('attendance_schedules', function (Blueprint $table) {
            $table->index('class_id', 'idx_attendance_schedules_class_id');
        });

        // -- wa_notifications --
        // Lookup notifikasi per sender & receiver
        Schema::table('wa_notifications', function (Blueprint $table) {
            $table->index('sender_id', 'idx_wa_notifications_sender_id');
            $table->index('receiver_id', 'idx_wa_notifications_receiver_id');
        });

        // -- users --
        // Query filter berdasarkan role_id
        Schema::table('users', function (Blueprint $table) {
            $table->index('role_id', 'idx_users_role_id');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_attendances_student_date');
            $table->dropIndex('idx_attendances_status_id');
            $table->dropIndex('idx_attendances_class_id');
            $table->dropIndex('idx_attendances_date');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropIndex('idx_permissions_student_id');
            $table->dropIndex('idx_permissions_status');
            $table->dropIndex('idx_permissions_student_status');
        });

        Schema::table('student_details', function (Blueprint $table) {
            $table->dropIndex('idx_student_details_user_id');
        });

        Schema::table('violation_points', function (Blueprint $table) {
            $table->dropIndex('idx_violation_points_student_id');
            $table->dropIndex('idx_violation_points_attendance_id');
            $table->dropIndex('idx_violation_points_rule_id');
        });

        Schema::table('face_logs', function (Blueprint $table) {
            $table->dropIndex('idx_face_logs_student_id');
        });

        Schema::table('journals', function (Blueprint $table) {
            $table->dropIndex('idx_journals_student_id');
        });

        Schema::table('class_student', function (Blueprint $table) {
            $table->dropUnique('uniq_class_student');
        });

        Schema::table('attendance_schedules', function (Blueprint $table) {
            $table->dropIndex('idx_attendance_schedules_class_id');
        });

        Schema::table('wa_notifications', function (Blueprint $table) {
            $table->dropIndex('idx_wa_notifications_sender_id');
            $table->dropIndex('idx_wa_notifications_receiver_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role_id');
        });
    }
};
