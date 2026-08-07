<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Completion</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
        }
        .certificate-container {
            width: 1050px;
            height: 740px;
            padding: 20px;
            margin: 20px auto;
            position: relative;
            background: #ffffff;
            border: 15px solid #1e40af;
            box-sizing: border-box;
            text-align: center;
        }
        .inner-border {
            border: 2px solid #3b82f6;
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
            position: relative;
        }
        .title {
            font-size: 50px;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-top: 40px;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 24px;
            color: #6b7280;
            margin-bottom: 50px;
        }
        .presented-to {
            font-size: 20px;
            color: #374151;
            margin-bottom: 15px;
        }
        .student-name {
            font-size: 48px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 30px;
            font-style: italic;
            border-bottom: 2px solid #e5e7eb;
            display: inline-block;
            padding-bottom: 5px;
            min-width: 600px;
        }
        .description {
            font-size: 18px;
            color: #4b5563;
            margin-bottom: 20px;
        }
        .course-name {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 60px;
        }
        .footer {
            position: absolute;
            bottom: 60px;
            width: 100%;
            display: table;
        }
        .signature-block {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            border-top: 1px solid #9ca3af;
            width: 200px;
            margin: 0 auto;
            padding-top: 10px;
            font-size: 16px;
            color: #4b5563;
        }
        .cert-number {
            font-size: 14px;
            color: #9ca3af;
            position: absolute;
            bottom: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="inner-border">
            <h1 class="title">Certificate of Completion</h1>
            <div class="subtitle">This certificate is proudly presented to</div>
            
            <div class="student-name">{{ $student_name }}</div>
            
            <div class="description">For successfully completing the requirements of the course:</div>
            
            <div class="course-name">{{ $course_name }}</div>
            
            <div class="footer">
                <div class="signature-block">
                    <div class="signature-line">Date Issued: <br>{{ $issued_at }}</div>
                </div>
                <div class="signature-block">
                    <!-- Logo placeholder -->
                    <h2 style="color: #1e40af; margin: 0;">E.LMS</h2>
                </div>
                <div class="signature-block">
                    <div class="signature-line">Authorized Signature</div>
                </div>
            </div>
            
            <div class="cert-number">Certificate No: {{ $cert_number }}</div>
        </div>
    </div>
</body>
</html>
