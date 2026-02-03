@php
    // ✅ Base64 logos (DomPDF-safe)
    $nexdusPath = public_path('images/nexdus.png');
    $musteyPath = public_path('images/mustey.png');

    $nexdusLogo = file_exists($nexdusPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($nexdusPath))
        : null;

    $musteyLogo = file_exists($musteyPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($musteyPath))
        : null;
@endphp

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate</title>

    <style>
        @page { margin: 18px; } /* smaller margin helps avoid page 2 */

        * { box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111;
            margin: 0;
            padding: 0;
        }

        /* IMPORTANT: avoid height:100% in dompdf (causes overflow -> page 2) */
        .wrap {
            border: 6px solid #111;
            padding: 16px;
        }

        .inner {
            border: 2px solid #111;
            padding: 16px 20px;
            text-align: center;
        }

        /* Logos row */
        .logos-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .logos-table td {
            width: 33.33%;
            vertical-align: middle;
        }
        .logo-left { text-align: left; }
        .logo-center { text-align: center; font-size: 12px; font-weight: 700; color: #222; }
        .logo-right { text-align: right; }

        /* Bigger logos */
        .logos-table img {
            height: 120px;   /* ✅ increase size here */
            width: auto;
            display: inline-block;
        }

        .small {
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #333;
            margin-top: 6px;
        }

        .title {
            font-size: 40px;
            font-weight: 700;
            margin: 10px 0 6px;
        }

        .name {
            font-size: 36px;
            font-weight: 700;
            margin: 12px 0;
        }

        .course {
            font-size: 18px;
            margin: 8px 0;
            line-height: 1.4;
        }

        .meta {
            margin-top: 10px;
            font-size: 13px;
            color: #333;
        }

        .academy {
            margin-top: 6px;
            font-size: 12px;
            color: #111;
            font-weight: 600;
        }

        .serial {
            margin-top: 8px;
            font-size: 12px;
            color: #111;
        }

        /* Bottom section */
        .bottom-table {
            width: 100%;
            margin-top: 12px;
            border-collapse: collapse;
        }

        .bottom-table td {
            width: 33.33%;
            vertical-align: bottom;
            text-align: center;
            padding: 6px;
        }

        .sig-line {
            border-top: 1px solid #111;
            padding-top: 6px;
            font-size: 11px;
            margin-top: 22px;
        }

        .qr img {
            width: 110px;
            height: 110px;
            display: block;
            margin: 0 auto;
        }

        .verify {
            margin-top: 6px;
            font-size: 9.5px;
            color: #222;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="inner">

            <table class="logos-table">
                <tr>
                    <td class="logo-left">
                        @if($nexdusLogo)
                            <img src="{{ $nexdusLogo }}" alt="Nexdus Logo">
                        @else
                            <span style="font-size:12px;color:#666;">Nexdus Logo</span>
                        @endif
                    </td>

                    <td class="logo-center">
                        Nexdus Academy × Mustey Digital Academy
                    </td>

                    <td class="logo-right">
                        @if($musteyLogo)
                            <img src="{{ $musteyLogo }}" alt="Mustey Logo">
                        @else
                            <span style="font-size:12px;color:#666;">Mustey Logo</span>
                        @endif
                    </td>
                </tr>
            </table>

            <div class="small">Certificate of Completion</div>
            <div class="title">This certifies that</div>

            <div class="name">{{ $studentName }}</div>

            <div class="course">
                has successfully completed the course <br>
                <strong>{{ $courseTitle }}</strong>
            </div>

            <div class="meta">Issued on {{ $issuedDate }}</div>
            <div class="academy">{{ $academyLine }}</div>

            <div class="serial">
                <strong>Serial Code:</strong> {{ $serial }}
            </div>

            <table class="bottom-table">
                <tr>
                    <td>
                        <div class="sig-line">
                            Instructor: {{ $instructor ?? '—' }}
                        </div>
                    </td>

                    <td>
                        <div class="qr">
                            <img src="{{ $qrUrl }}" alt="QR Code">
                        </div>
                        <div class="verify">
                            Verify: {{ $verifyUrl }}
                        </div>
                    </td>

                    <td>
                        <div class="sig-line">
                            Nexdus Academy
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</body>
</html>
