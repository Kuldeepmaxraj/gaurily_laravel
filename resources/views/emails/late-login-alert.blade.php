<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Inter, Arial, sans-serif; background: #f4f6fb; margin: 0; padding: 0; }
        .wrap { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: #f59e0b; padding: 32px 40px; color: #fff; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: .8; }
        .body { padding: 32px 40px; color: #1a1f36; }
        .body p { font-size: 15px; line-height: 1.6; color: #4a5568; }
        .highlight { background: #fff7ed; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .highlight p { margin: 0 0 6px; font-size: 14px; color: #92400e; }
        .highlight p:last-child { margin-bottom: 0; }
        .footer { background: #f9fafb; padding: 20px 40px; text-align: center; }
        .footer p { font-size: 12px; color: #a0aec0; margin: 0; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>Late Check-In Alert</h1>
        <p>Gaurily Internal Notification</p>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $employee->name }}</strong>,</p>
        <p>Your check-in today was recorded as <strong>late</strong>. Please review the details below.</p>

        <div class="highlight">
            <p><strong>Your Check-In Time:</strong> {{ $loginTime }}</p>
            <p><strong>Shift Start Time:</strong> {{ $shiftStart }}</p>
            <p><strong>Late By:</strong> {{ $minutesLate }} minute{{ $minutesLate === 1 ? '' : 's' }}</p>
        </div>

        <p>Repeated late arrivals may affect your attendance record. If you had a valid reason, please inform your team lead or HR at <a href="mailto:care@gaurily.com" style="color:#0066FF;">care@gaurily.com</a>.</p>
        <p style="margin-top:24px;">Regards,<br><strong>Gaurily HR Team</strong></p>
    </div>
    <div class="footer">
        <p>This is an automated message from Gaurily Attendance System. Do not reply to this email.</p>
    </div>
</div>
</body>
</html>
