<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Inter, Arial, sans-serif; background: #f4f6fb; margin: 0; padding: 0; }
        .wrap { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: #0066FF; padding: 32px 40px; color: #fff; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: .8; }
        .body { padding: 32px 40px; color: #1a1f36; }
        .body p { font-size: 15px; line-height: 1.6; color: #4a5568; }
        .highlight { background: #fff7ed; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .highlight p { margin: 0; font-size: 14px; color: #92400e; }
        .footer { background: #f9fafb; padding: 20px 40px; text-align: center; }
        .footer p { font-size: 12px; color: #a0aec0; margin: 0; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>Attendance Alert</h1>
        <p>Gaurily Internal Notification</p>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $employee->name }}</strong>,</p>
        <p>We noticed that your attendance was not recorded for <strong>{{ $date }}</strong>.</p>

        <div class="highlight">
            <p><strong>Date:</strong> {{ $date }}</p>
            <p style="margin-top:8px;"><strong>Status:</strong> Absent / No clock-in recorded</p>
        </div>

        <p>If this is a mistake or you were on leave, please contact your HR or team lead to update your attendance record.</p>
        <p>If you were working remotely or had an issue clocking in, please reach out to the HR team at <a href="mailto:care@gaurily.com" style="color:#0066FF;">care@gaurily.com</a> as soon as possible.</p>
        <p style="margin-top:24px;">Regards,<br><strong>Gaurily HR Team</strong></p>
    </div>
    <div class="footer">
        <p>This is an automated message from Gaurily Attendance System. Do not reply to this email.</p>
    </div>
</div>
</body>
</html>
