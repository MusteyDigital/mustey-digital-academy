<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function download(Course $course)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            abort(403);
        }

        // Must be enrolled
        $enrolled = $user->coursesEnrolled()
            ->where('courses.id', $course->id)
            ->exists();

        if (!$enrolled) {
            abort(403);
        }

        // Require all lessons completed
        $totalLessons = $course->lessons()->count();
        if ($totalLessons > 0) {
            $completedCount = $user->completedLessons()
                ->where('lessons.course_id', $course->id)
                ->count();

            if ($completedCount < $totalLessons) {
                return back()->with('error', 'Complete all lessons before downloading the certificate.');
            }
        }

        // Create or fetch certificate
        $certificate = Certificate::firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            [
                // generate once (first time only)
                'serial' => $this->generateUniqueSerial(),
                'issued_at' => now(),
            ]
        );

        // Verification URL (by serial)
        $verifyUrl = route('certificates.verify', $certificate->serial);

        // QR Code (online generator)
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($verifyUrl);

        $pdf = Pdf::loadView('certificates.course', [
                'studentName' => $user->name,
                'courseTitle' => $course->title,
                'issuedDate'  => ($certificate->issued_at ?? now())->format('F j, Y'),
                'instructor'  => optional($course->instructor)->name,
                'serial'      => $certificate->serial,
                'verifyUrl'   => $verifyUrl,
                'qrUrl'       => $qrUrl,
                'academyLine' => 'Academy in collaboration: Nexdus Academy & Mustey Digital Academy',
            ])
            ->setPaper('a4', 'landscape')
            ->setOptions([
                // ✅ allows QR url image to load
                'isRemoteEnabled' => true,

                // ✅ helps with local file paths like public_path('images/...')
                'isHtml5ParserEnabled' => true,

                // ✅ improves font rendering
                'isFontSubsettingEnabled' => true,
            ]);

        return $pdf->download('certificate-' . $certificate->serial . '.pdf');
    }

    // ✅ Public verification page (VALID + INVALID)
    public function verify(string $serial)
    {
        $certificate = Certificate::where('serial', $serial)
            ->with(['user', 'course', 'course.instructor'])
            ->first();

        // ✅ If not found, show invalid page instead of 404
        if (!$certificate) {
            return view('certificates.invalid', [
                'serial' => $serial,
            ]);
        }

        return view('certificates.verify', compact('certificate'));
    }

    /**
     * Generate serial and ensure uniqueness in DB.
     */
    private function generateUniqueSerial(): string
    {
        do {
            $year = now()->format('Y');
            $rand = str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
            $serial = "NX-MD-{$year}-{$rand}";
        } while (Certificate::where('serial', $serial)->exists());

        return $serial;
    }
}
