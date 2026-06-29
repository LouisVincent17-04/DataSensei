<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DataSensei password reset code</title>
</head>
<body style="margin:0;background:#080c14;font-family:Arial,Helvetica,sans-serif;color:#f0f4ff;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#080c14;padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#111827;border:1px solid #27344a;border-radius:14px;overflow:hidden;">
          <tr>
            <td style="padding:28px 32px;border-bottom:1px solid #27344a;">
              <div style="font-size:22px;font-weight:700;">Data<span style="color:#63b3ed;">Sensei</span></div>
              <div style="font-size:12px;color:#8a99b3;margin-top:4px;">Secure account recovery</div>
            </td>
          </tr>
          <tr>
            <td style="padding:32px;">
              <p style="margin:0 0 16px;color:#f0f4ff;">Hello {{ $recipientName }},</p>
              <p style="margin:0 0 22px;color:#8a99b3;line-height:1.65;">Use the following one-time code to reset your DataSensei password:</p>
              <div style="background:#0d1320;border:1px solid #334764;border-radius:10px;text-align:center;padding:20px;font-size:36px;font-weight:700;letter-spacing:10px;color:#63b3ed;">{{ $otp }}</div>
              <p style="margin:22px 0 0;color:#8a99b3;line-height:1.65;">This code expires in {{ $expiresMinutes }} minutes and can be used only once. If you did not request this reset, no action is required.</p>
              <p style="margin:18px 0 0;color:#53627b;font-size:12px;line-height:1.55;">For your protection, never share this code with anyone. DataSensei staff will never ask for it.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
