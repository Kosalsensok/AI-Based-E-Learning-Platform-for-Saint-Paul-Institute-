<!DOCTYPE html>
<html lang="km" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <title>លេខកូដសម្ងាត់ OTP</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

  <style>
    body, table, td, p, a, h1, h2, span {
      font-family: 'Kantumruy Pro', 'Siemreap', 'Khmer OS Siemreap', 'Noto Sans Khmer', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
      -webkit-font-smoothing: antialiased;
    }
    .otp-box {
      font-family: 'Plus Jakarta Sans', 'SF Pro Display', -apple-system, monospace !important;
    }
    @media only screen and (max-width: 540px) {
      .card-body { padding: 24px 16px !important; }
      .otp-digit {
        width: 38px !important;
        height: 46px !important;
        line-height: 46px !important;
        font-size: 22px !important;
        margin: 0 2px !important;
      }
      .heading-title { font-size: 17px !important; }
    }
  </style>
</head>

<body style="background-color: #0b1120; margin: 0; padding: 32px 12px; font-family: 'Kantumruy Pro', 'Siemreap', 'Khmer OS Siemreap', 'Noto Sans Khmer', sans-serif;">

  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td align="center">
        
        <!-- Main Card Wrapper -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 45px rgba(0,0,0,0.3); border: 1px solid #1e293b;">
          
          <!-- Modern Brand Header -->
          <tr>
            <td align="center" style="background: linear-gradient(145deg, #1d4ed8 0%, #2563eb 60%, #3b82f6 100%); padding: 32px 20px; text-align: center;">
              
              <!-- E-LMS Logo Badge -->
              <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin-bottom: 12px;">
                <tr>
                  <td align="center" style="background-color: #ffffff; width: 50px; height: 50px; border-radius: 14px; box-shadow: 0 8px 16px rgba(0,0,0,0.12);">
                    <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 800; color: #2563eb; letter-spacing: -0.5px;">E-LMS</span>
                  </td>
                </tr>
              </table>

              <h1 style="margin: 0; color: #ffffff; font-size: 18px; font-weight: 700; letter-spacing: 0.2px; font-family: 'Kantumruy Pro', 'Khmer OS Siemreap', sans-serif;">
                វិទ្យាស្ថាន សន្តប៉ូល (SPI)
              </h1>
              <p style="margin: 4px 0 0 0; color: #bfdbfe; font-size: 12px; font-weight: 500; font-family: 'Plus Jakarta Sans', sans-serif;">
                AI-Powered E-Learning Management System
              </p>
            </td>
          </tr>

          <!-- Card Content Body -->
          <tr>
            <td class="card-body" style="padding: 32px 28px; background-color: #ffffff; text-align: center;">
              
              <!-- Verification Badge -->
              <div style="display: inline-block; background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 20px; padding: 4px 14px; margin-bottom: 16px;">
                <span style="font-size: 12px; font-weight: 600; color: #1d4ed8; font-family: 'Kantumruy Pro', 'Khmer OS Siemreap', sans-serif;">
                  🔐 សុវត្ថិភាពផ្ទៀងផ្ទាត់គណនី
                </span>
              </div>

              <!-- Main Heading -->
              <h2 class="heading-title" style="margin: 0 0 10px 0; font-size: 19px; font-weight: 700; color: #0f172a; font-family: 'Kantumruy Pro', 'Khmer OS Siemreap', sans-serif; line-height: 1.4;">
                លេខកូដសម្ងាត់ OTP សម្រាប់ចូលប្រព័ន្ធ
              </h2>

              <!-- Description -->
              <p style="margin: 0 0 24px 0; font-size: 13.5px; color: #64748b; line-height: 1.7; font-family: 'Kantumruy Pro', 'Khmer OS Siemreap', sans-serif;">
                សួស្តី <strong style="color: #0f172a;">{{ $user->name ?? $user->name_kh ?? 'អ្នកប្រើប្រាស់' }}</strong>! សូមប្រើប្រាស់លេខកូដ ៦ ខ្ទង់ខាងក្រោម ដើម្បីបន្តដំណើរការចូលប្រើប្រាស់គណនីរបស់អ្នក៖
              </p>

              <!-- OTP Digits Container -->
              <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto 20px auto;">
                <tr>
                  <td align="center" style="background-color: #f8fafc; border: 1.5px dashed #93c5fd; border-radius: 18px; padding: 16px 20px;">
                    <div style="text-align: center; white-space: nowrap;">
                      @php
                        $otpStr = (string)$otp;
                        $digits = str_split($otpStr);
                      @endphp
                      @foreach($digits as $digit)
                        <span class="otp-digit otp-box" style="display: inline-block; width: 44px; height: 50px; line-height: 50px; margin: 0 3px; background-color: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 12px; font-size: 24px; font-weight: 800; color: #1d4ed8; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.04); vertical-align: middle;">
                          {{ $digit }}
                        </span>
                      @endforeach
                    </div>
                  </td>
                </tr>
              </table>

              <!-- Expiry Alert Capsule -->
              <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin-bottom: 24px;">
                <tr>
                  <td style="background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 20px; padding: 6px 16px;">
                    <span style="font-size: 12px; color: #e11d48; font-weight: 600; font-family: 'Kantumruy Pro', 'Khmer OS Siemreap', sans-serif;">
                      ⏱️ លេខកូដនេះមានសុពលភាពត្រឹមតែ <strong style="color: #be123c;">៥ នាទី</strong> ប៉ុណ្ណោះ
                    </span>
                  </td>
                </tr>
              </table>

              <!-- Warning Box -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid #f59e0b; border-radius: 10px;">
                <tr>
                  <td style="padding: 12px 14px; text-align: left;">
                    <p style="margin: 0; font-size: 12px; color: #92400e; line-height: 1.6; font-family: 'Kantumruy Pro', 'Khmer OS Siemreap', sans-serif;">
                      <strong>⚠️ ការរំលឹកសុវត្ថិភាព៖</strong> សូមកុំផ្ដល់លេខកូដ OTP នេះទៅកាន់អ្នកដទៃជាដាច់ខាត។ ក្រុមការងារបច្ចេកវិទ្យានឹងមិនទាមទារលេខកូដសម្ងាត់នេះពីអ្នកឡើយ។
                    </p>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- Clean Footer (គ្មាន Address) -->
          <tr>
            <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 24px; text-align: center;">
              <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 600; color: #475569; font-family: 'Kantumruy Pro', 'Khmer OS Siemreap', sans-serif;">
                Saint Paul Institute (E-LMS)
              </p>
              <p style="margin: 0; font-size: 11px; color: #94a3b8; font-family: 'Plus Jakarta Sans', sans-serif;">
                © 2026 E-LMS. All rights reserved. • <a href="https://spilms.tech" style="color: #2563eb; text-decoration: none; font-weight: 600;">spilms.tech</a>
              </p>
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>
</html>
