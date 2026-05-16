<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Version Update</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; background: #f9fafb; margin: 0; padding: 24px;">
<div style="max-width: 720px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
    <p style="margin: 0 0 12px; color: #6b7280;">{{ $appName }}</p>
    <h1 style="margin: 0 0 12px;">Platform Update v{{ $version->version_number }}</h1>
    @if(!empty($version->alert_title))
        <p style="margin: 0 0 16px; font-weight: 600;">{{ $version->alert_title }}</p>
    @endif
    <p style="margin: 0 0 16px;">Hello {{ $user?->name ?? 'User' }},</p>
    <div style="white-space: pre-wrap; line-height: 1.6;">{{ $version->description }}</div>
    <p style="margin: 20px 0 0;">Access: <a href="{{ $appUrl }}">{{ $appUrl }}</a></p>
</div>
</body>
</html>
