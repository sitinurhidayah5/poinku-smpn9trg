<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Roles
        $roles = [
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'Guru'],
            ['id' => 3, 'name' => 'Siswa'],
            ['id' => 4, 'name' => 'Orang Tua'],
            ['id' => 5, 'name' => 'Kepala Sekolah'],
        ];
        DB::table('roles')->insert($roles);

        // 2. Akun Demo (Password default: Password123!)
        DB::table('users')->insert([
            ['role_id' => 1, 'username' => 'admin_bk', 'password' => Hash::make('Password123!'), 'name' => 'Ibu Siti Aminah, S.Pd (BK)'],
            ['role_id' => 2, 'username' => 'guru_wali', 'password' => Hash::make('Password123!'), 'name' => 'Pak Budi Santoso, S.Pd'],
            ['role_id' => 3, 'username' => 'siswa_demo', 'password' => Hash::make('Password123!'), 'name' => 'Ahmad Rizky'],
            ['role_id' => 4, 'username' => 'ortu_demo', 'password' => Hash::make('Password123!'), 'name' => 'Bpk. Hendra (Orang Tua Rizky)'],
            ['role_id' => 5, 'username' => 'kepsek', 'password' => Hash::make('Password123!'), 'name' => 'Drs. H. M. Yamin, M.Pd'],
        ]);

        // 3. Jenis Pelanggaran (Contoh Tata Tertib SMPN 9 Tenggarong)
        DB::table('violation_types')->insert([
            ['code' => 'PLG-001', 'name' => 'Terlambat masuk sekolah (< 15 menit)', 'category' => 'Ringan', 'points' => 5],
            ['code' => 'PLG-002', 'name' => 'Atribut seragam tidak lengkap (Dasi/Baret/Kaos Kaki)', 'category' => 'Ringan', 'points' => 5],
            ['code' => 'PLG-003', 'name' => 'Membolos jam pelajaran tertentu', 'category' => 'Sedang', 'points' => 15],
            ['code' => 'PLG-004', 'name' => 'Membawa/Menggunakan HP tanpa izin saat KBM', 'category' => 'Sedang', 'points' => 20],
            ['code' => 'PLG-005', 'name' => 'Membawa/Merapok/Merokok di area sekolah', 'category' => 'Berat', 'points' => 50],
            ['code' => 'PLG-006', 'name' => 'Tawuran atau berkelahi di dalam/luar sekolah', 'category' => 'Berat', 'points' => 100],
        ]);

        // 4. Jenis Penghargaan
        DB::table('reward_types')->insert([
            ['code' => 'PRE-001', 'name' => 'Petugas Upacara Bendera Terbaik', 'level' => 'Sekolah', 'points' => 10],
            ['code' => 'PRE-002', 'name' => 'Juara Kelas (Peringkat 1-3)', 'level' => 'Sekolah', 'points' => 20],
            ['code' => 'PRE-003', 'name' => 'Juara Lomba Akademik/Non-Akademik Tk. Kabupaten', 'level' => 'Kabupaten', 'points' => 35],
            ['code' => 'PRE-004', 'name' => 'Juara Lomba Tk. Provinsi / Nasional', 'level' => 'Nasional', 'points' => 75],
            ['code' => 'PRE-005', 'name' => 'Siswa Teladan Semester', 'level' => 'Sekolah', 'points' => 50],
        ]);

        // 5. Ambang Batas Sanksi
        DB::table('sanction_thresholds')->insert([
            ['min_points' => 15, 'max_points' => 29, 'action_name' => 'Peringatan Lisan oleh Wali Kelas', 'requires_letter' => false],
            ['min_points' => 30, 'max_points' => 49, 'action_name' => 'Surat Peringatan I (SP 1) & Pembinaan BK', 'requires_letter' => true],
            ['min_points' => 50, 'max_points' => 74, 'action_name' => 'Surat Peringatan II (SP 2) & Pemanggilan Ortu I', 'requires_letter' => true],
            ['min_points' => 75, 'max_points' => 99, 'action_name' => 'Surat Peringatan III (SP 3) & Skorsing 3 Hari', 'requires_letter' => true],
            ['min_points' => 100, 'max_points' => 999, 'action_name' => 'Dikembalikan kepada Orang Tua (Dikeluarkan)', 'requires_letter' => true],
        ]);
    }
}