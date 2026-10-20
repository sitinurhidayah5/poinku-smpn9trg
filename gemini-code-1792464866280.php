<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Total bulan ini
        $totalViolations = DB::table('violation_records')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('status', 'Valid')->count();

        $totalRewards = DB::table('reward_records')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('status', 'Valid')->count();

        // Top 10 Pelanggar
        $topViolators = DB::table('students')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->leftJoin('violation_records', function($join) {
                $join->on('students.id', '=', 'violation_records.student_id')
                     ->where('violation_records.status', '=', 'Valid');
            })
            ->leftJoin('violation_types', 'violation_records.violation_type_id', '=', 'violation_types.id')
            ->select('students.full_name', 'students.nis', 'classes.grade', 'classes.rombel', DB::raw('COALESCE(SUM(violation_types.points), 0) as total_points'))
            ->groupBy('students.id', 'students.full_name', 'students.nis', 'classes.grade', 'classes.rombel')
            ->orderBy('total_points', 'DESC')
            ->limit(10)->get();

        // Siswa Melewati/Mendekati Ambang Batas Sanksi
        $alertStudents = DB::table('students')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->join('violation_records', 'students.id', '=', 'violation_records.student_id')
            ->join('violation_types', 'violation_records.violation_type_id', '=', 'violation_types.id')
            ->where('violation_records.status', 'Valid')
            ->select('students.full_name', 'students.parent_phone', 'classes.grade', 'classes.rombel', DB::raw('SUM(violation_types.points) as total_points'))
            ->groupBy('students.id', 'students.full_name', 'students.parent_phone', 'classes.grade', 'classes.rombel')
            ->having('total_points', '>=', 30)
            ->get();

        return view('dashboard.index', compact('totalViolations', 'totalRewards', 'topViolators', 'alertStudents'));
    }
}