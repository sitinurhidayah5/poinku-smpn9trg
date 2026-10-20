<?php

namespace App\Http\Controllers;

use App\Models\ViolationRecord;
use App\Models\Student;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ViolationController extends Controller
{
    // Input Tunggal maupun Massal (Maksimal 3 Langkah)
    public function store(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'violation_type_id' => 'required|exists:violation_types,id',
            'date' => 'required|date',
            'time' => 'required',
            'description' => 'nullable|string',
            'proof_photo' => 'nullable|image|max:2048'
        ]);

        $activeYear = DB::table('academic_years')->where('is_active', true)->first();
        $photoPath = null;

        if ($request->hasFile('proof_photo')) {
            $photoPath = $request->file('proof_photo')->store('proofs/violations', 'public');
        }

        DB::transaction(function () use ($request, $activeYear, $photoPath) {
            foreach ($request->student_ids as $studentId) {
                $record = ViolationRecord::create([
                    'student_id' => $studentId,
                    'violation_type_id' => $request->violation_type_id,
                    'academic_year_id' => $activeYear->id,
                    'recorded_by' => Auth::id(),
                    'date' => $request->date,
                    'time' => $request->time,
                    'description' => $request->description,
                    'proof_photo' => $photoPath,
                    'status' => 'Valid'
                ]);

                // Record Audit Log
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'CREATE',
                    'module' => 'Pelanggaran',
                    'description' => 'Mencatat pelanggaran untuk Siswa ID: ' . $studentId,
                    'ip_address' => $request->ip()
                ]);
            }
        });

        return redirect()->back()->with('success', 'Catatan pelanggaran berhasil disimpan!');
    }

    // Pembatalan Transaksi oleh Admin (Audit Logged)
    public function cancel(Request $request, $id)
    {
        $request->validate(['cancel_reason' => 'required|string']);

        $record = ViolationRecord::findOrFail($id);
        $record->update([
            'status' => 'Dibatalkan',
            'cancel_reason' => $request->cancel_reason,
            'cancelled_by' => Auth::id()
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'CANCEL',
            'module' => 'Pelanggaran',
            'description' => "Membatalkan pelanggaran ID #{$id}. Alasan: " . $request->cancel_reason,
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Transaksi pelanggaran telah dibatalkan.');
    }
}