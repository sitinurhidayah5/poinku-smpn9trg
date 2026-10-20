<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class LetterController extends Controller
{
    public function generateSanctionLetter($studentId, $thresholdId)
    {
        $student = Student::with(['class'])->findOrFail($studentId);
        $violations = DB::table('violation_records')
            ->join('violation_types', 'violation_records.violation_type_id', '=', 'violation_types.id')
            ->where('student_id', $studentId)
            ->where('status', 'Valid')
            ->get();

        $totalPoints = $violations->sum('points');

        $data = [
            'student' => $student,
            'violations' => $violations,
            'totalPoints' => $totalPoints,
            'date' => date('d F Y')
        ];

        $pdf = Pdf::loadView('letters.sanction_sp', $data);
        return $pdf->stream("Surat_Panggilan_{$student->nis}.pdf");
    }
}