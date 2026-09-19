<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DataSensei password reset code</title>
</head>
<body style="margin:0;background:#0d1320;font-family:Inter,Arial,Helvetica,sans-serif;color:#f8fafc;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#0d1320;padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#111c2d;border:1px solid #1e2f47;border-radius:8px;overflow:hidden;">
          <tr>
            <td style="padding:24px 32px;border-bottom:1px solid #1e2f47;">
              <div style="font-size:20px;font-weight:700;color:#f8fafc;">Data<span style="color:#3b82f6;">Sensei</span></div>
              <div style="font-size:13px;color:#8aa0bd;margin-top:4px;">Secure account recovery</div>
            </td>
          </tr>
          <tr>
            <td style="padding:32px;">
              <p style="margin:0 0 16px;color:#f8fafc;font-size:14px;">Hello {{ $recipientName }},</p>
              <p style="margin:0 0 24px;color:#c8d5e8;font-size:14px;line-height:1.6;">Use the following one-time code to reset your DataSensei password:</p>
              <div style="background:#0f1928;border:1px solid #2c4168;border-radius:6px;text-align:center;padding:18px;font-family:'JetBrains Mono',Consolas,monospace;font-size:32px;font-weight:700;letter-spacing:8px;color:#f8fafc;">{{ $otp }}</div>
              <p style="margin:24px 0 0;color:#c8d5e8;font-size:14px;line-height:1.6;">This code expires in {{ $expiresMinutes }} minutes and can be used only once. If you did not request this reset, no action is required.</p>
              <p style="margin:16px 0 0;color:#8aa0bd;font-size:12px;line-height:1.55;">For your protection, never share this code with anyone. DataSensei staff will never ask for it.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
