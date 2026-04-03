@php
    // ===== LOGOS =====
    $nexdusPath = public_path('images/nexdus.png');
    $musteyPath = public_path('images/mustey.png');
    $watermarkPath = public_path('images/watermark.png');

    $nexdusLogo = file_exists($nexdusPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($nexdusPath))
        : null;

    $musteyLogo = file_exists($musteyPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($musteyPath))
        : null;

    $watermarkLogo = file_exists($watermarkPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($watermarkPath))
        : null;

    // Navy Brand Color
    $logoColor = '#0B2E6D';
@endphp

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Certificate</title>

<style>
@page { margin: 18px; }

body {
    font-family: DejaVu Sans, sans-serif;
    margin: 0;
    padding: 0;
    color: #111;
}

.wrap {
    border: 6px solid #111;
    padding: 16px;
}

.inner {
    border: 2px solid #111;
    padding: 20px;
    text-align: center;
    position: relative;
}

/* ===== WATERMARK ===== */
.watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 420px;
    opacity: 0.096;   /* Perfect for navy */
    z-index: 0;
}

.content-layer {
    position: relative;
    z-index: 2;
}

/* ===== LOGOS ===== */
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
.logo-center { text-align: center; font-size: 12px; font-weight: 700; }
.logo-right { text-align: right; }

.logos-table img {
    height: 110px;
}

/* ===== TEXT ===== */
.small {
    font-size: 12px;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-top: 6px;
}

.title {
    font-size: 40px;
    font-weight: 800;
    margin: 14px 0 6px;
    color: {{ $logoColor }};
}

.name {
    font-size: 36px;
    font-weight: 800;
    margin: 12px 0;
    color: {{ $logoColor }};
}

.course {
    font-size: 18px;
    margin: 10px 0;
    line-height: 1.5;
}

.meta {
    margin-top: 10px;
    font-size: 13px;
}

.academy {
    margin-top: 6px;
    font-size: 12px;
    font-weight: 600;
}

.serial {
    margin-top: 8px;
    font-size: 12px;
}

/* ===== BOTTOM SECTION ===== */
.bottom-table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    table-layout: fixed;
}

.bottom-table td {
    width: 33.33%;
    vertical-align: bottom;
    padding: 6px;
}

.sig-wrap {
    width: 260px;
    margin: 0 auto;
}

.sig-line {
    border-top: 1px solid #111;
    padding-top: 6px;
    font-size: 11px;
    margin-top: 22px;
}

.qr img {
    width: 115px;
    height: 115px;
    display: block;
    margin: 0 auto;
}

.verify {
    margin-top: 6px;
    font-size: 9.5px;
    word-break: break-all;
    text-align: center;
}
</style>
</head>

<body>
<div class="wrap">
<div class="inner">

@if($watermarkLogo)
    <img src="{{ $watermarkLogo }}" class="watermark">
@endif

<div class="content-layer">

<table class="logos-table">
<tr>
<td class="logo-left">
@if($nexdusLogo)
<img src="{{ $nexdusLogo }}">
@endif
</td>

<td class="logo-center">
Nexdus Academy × Mustey Digital Academy
</td>

<td class="logo-right">
@if($musteyLogo)
<img src="{{ $musteyLogo }}">
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

<td style="text-align:left;">
<div class="sig-wrap">
<div class="sig-line">
Instructor: {{ $instructor ?? '—' }}
</div>
</div>
</td>

<td style="text-align:center;">
<div class="qr">
<img src="{{ $qrUrl }}">
</div>
<div class="verify">
Verify: {{ $verifyUrl }}
</div>
</td>

<td style="text-align:right;">
<div class="sig-wrap">
<div class="sig-line">
Nexdus Academy
</div>
</div>
</td>

</tr>
</table>

</div>
</div>
</div>
</body>
</html>
