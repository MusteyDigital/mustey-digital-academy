<?php

namespace App\Http\Controllers;

use App\Mail\CertificateIssued;
use App\Models\Certificate;
use App\Models\Course;
use App\Notifications\CertificateReady; // ✅ ADD THIS
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

        // ✅ Create or fetch certificate (serial + token)
        $certificate = Certificate::firstOrCreate(
            [
                'user_id'   => $user->id,
                'course_id' => $course->id,
            ],
            [
                'serial'       => $this->generateUniqueSerial(),
                'verify_token' => $this->generateUniqueToken(), // 64 chars
                'issued_at'    => now(),
            ]
        );

        // ✅ Backfill old records safely (serial/token may be null from earlier schema)
        $wasUpdated = false;

        if (!$certificate->serial) {
            $certificate->serial = $this->generateUniqueSerial();
            $wasUpdated = true;
        }

        if (!$certificate->verify_token) {
            $certificate->verify_token = $this->generateUniqueToken();
            $wasUpdated = true;
        }

        if ($wasUpdated) {
            $certificate->save();
        }

        // ✅ URLs (for QR + email)
        $verifyUrl   = route('certificates.verify', $certificate->verify_token);
        $downloadUrl = route('certificates.download', $course->id);

        /**
         * ✅ IMPORTANT:
         * Only do email + notification the FIRST time certificate is created
         */
        if ($certificate->wasRecentlyCreated) {

            // ✅ 1) Send Email (queued)
            Mail::to($user->email)->queue(
                new CertificateIssued($user, $course, $certificate, $verifyUrl, $downloadUrl)
            );

            // ✅ 2) Create In-App Notification (saved in DB)
            $user->notify(new CertificateReady($course->title));
        }

        // QR Code (online generator)
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($verifyUrl);
        
        // PDF
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
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);

        return $pdf->download('certificate-' . $certificate->serial . '.pdf');
        
    }
        
    // ✅ Public verification by token
    public function verify(string $token)
    {
        $certificate = Certificate::where('verify_token', $token)
            ->with(['user', 'course', 'course.instructor'])
            ->first();

        if (!$certificate) {
            return view('certificates.invalid', [
                'serial' => $token,
            ]);
        }

        return view('certificates.verify', compact('certificate'));
    }

    private function generateUniqueSerial(): string
    {
        do {
            $year = now()->format('Y');
            $rand = str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
            $serial = "NX-MD-{$year}-{$rand}";
        } while (Certificate::where('serial', $serial)->exists());

        return $serial;
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = bin2hex(random_bytes(32)); // 64 chars
        } while (Certificate::where('verify_token', $token)->exists());

        return $token;
    }
}
