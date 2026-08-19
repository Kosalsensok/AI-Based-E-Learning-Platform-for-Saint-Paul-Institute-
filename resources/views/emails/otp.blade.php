<!DOCTYPE html>
<html lang="km" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <title>លេខកូដសម្ងាត់ OTP</title>
  
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap');

    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }

    body {
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      min-width: 100%;
      background-color: #0b0f19;
      font-family: 'Kantumruy Pro', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #334155;
    }

    .email-container {
      max-width: 520px !important;
      margin: 0 auto !important;
      width: 100%;
    }

    .otp-digit {
      display: inline-block;
      width: 42px;
      height: 48px;
      line-height: 48px;
      margin: 0 4px;
      background: #ffffff;
      border: 1.5px solid #cbd5e1;
      border-radius: 10px;
      font-family: 'Plus Jakarta Sans', monospace;
      font-size: 26px;
      font-weight: 800;
      color: #1d4ed8;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    }

    @media only screen and (max-width: 540px) {
      .mobile-padding { padding: 24px 16px !important; }
      .card-body { padding: 24px 18px !important; }
      .otp-digit {
        width: 36px !important;
        height: 44px !important;
        line-height: 44px !important;
        font-size: 22px !important;
        margin: 0 2px !important;
      }
      .brand-title { font-size: 16px !important; }
    }
  </style>
</head>

<body style="background-color: #080c16; margin: 0; padding: 30px 12px;">

  <!-- Outer Canvas -->
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td align="center">
        
        <!-- Main Card Wrapper -->
        <table class="email-container" border="0" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.4); border: 1px solid rgba(255, 255, 255, 0.08);">
          
          <!-- Top Header / Banner -->
          <tr>
            <td style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%); padding: 32px 24px; text-align: center;">
              
              <!-- Logo Container -->
              <table border="0" cellpadding="0" cellspacing="0" align="center">
                <tr>
                  <td align="center" style="background-color: #ffffff; width: 54px; height: 54px; border-radius: 16px; box-shadow: 0 8px 16px rgba(0,0,0,0.15);">
                    <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 900; color: #1d4ed8; letter-spacing: -0.5px;">E-LMS</span>
                  </td>
                </tr>
              </table>

              <h1 class="brand-title" style="margin: 14px 0 2px 0; color: #ffffff; font-size: 19px; font-weight: 700; letter-spacing: 0.2px;">
                វិទ្យាស្ថាន សន្តប៉ូល (SPI)
              </h1>
              <p style="margin: 0; color: #bfdbfe; font-size: 12px; font-weight: 500; letter-spacing: 0.4px;">
                AI-Powered E-Learning Management System
              </p>
            </td>
          </tr>

          <!-- Card Body Content -->
          <tr>
            <td class="card-body" style="padding: 36px 32px; background-color: #ffffff; text-align: center;">
              
              <div style="display: inline-block; background-color: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 20px; margin-bottom: 16px;">
                🔐 សុវត្ថិភាពផ្ទៀងផ្ទាត់គណនី
              </div>

              <h2 style="margin: 0 0 10px 0; font-size: 18px; font-weight: 700; color: #0f172a;">
                លេខកូដសម្ងាត់ OTP សម្រាប់ចូលប្រព័ន្ធ
              </h2>

              <p style="margin: 0 0 24px 0; font-size: 13.5px; color: #64748b; line-height: 1.6;">
                សួស្តី <strong>{{ $user->name ?? $user->name_kh ?? 'អ្នកប្រើប្រាស់' }}</strong>! សូមប្រើប្រាស់លេខកូដ ៦ ខ្ទង់ខាងក្រោម ដើម្បីបន្តដំណើរការចូលប្រើប្រាស់គណនីរបស់អ្នក៖
              </p>

              <!-- OTP Code Display Grid -->
              <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto 24px auto;">
                <tr>
                  <td align="center" style="background: #f8fafc; border: 1.5px dashed #93c5fd; border-radius: 16px; padding: 18px 20px;">
                    <div style="letter-spacing: 0;">
                      @php
                        $otpStr = (string)$otp;
                        $digits = str_split($otpStr);
                      @endphp
                      @foreach($digits as $digit)
                        <span class="otp-digit">{{ $digit }}</span>
                      @endforeach
                    </div>
                  </td>
                </tr>
              </table>

              <!-- Expiry Alert -->
              <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin-bottom: 24px;">
                <tr>
                  <td style="background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 24px; padding: 6px 14px; font-size: 12px; color: #e11d48; font-weight: 600;">
                    ⏱️ លេខកូដនេះមានសុពលភាពត្រឹមតែ <span style="font-weight: 700;">៥ នាទី</span> ប៉ុណ្ណោះ
                  </td>
                </tr>
              </table>

              <!-- Security Caution Note -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border-radius: 12px; border-left: 4px solid #f59e0b;">
                <tr>
                  <td style="padding: 12px 16px; text-align: left;">
                    <p style="margin: 0; font-size: 12px; color: #b45309; line-height: 1.5;">
                      <strong>⚠️ ការរំលឹកសុវត្ថិភាព៖</strong> សូមកុំផ្ដល់លេខកូដ OTP នេះទៅកាន់អ្នកដទៃជាដាច់ខាត។ ក្រុមការងារបច្ចេកវិទ្យានឹងមិនទាមទារលេខកូដសម្ងាត់នេះពីអ្នកឡើយ។
                    </p>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- Footer Section (គ្មាន Address) -->
          <tr>
            <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 22px 24px; text-align: center;">
              <p style="margin: 0 0 6px 0; font-size: 12px; font-weight: 600; color: #475569;">
                Saint Paul Institute (E-LMS)
              </p>
              <p style="margin: 0; font-size: 11px; color: #94a3b8;">
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
