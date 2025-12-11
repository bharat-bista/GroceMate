@component('mail::message')
# Password Reset Request

Hello {{ $name ?? 'User' }},

Use the following **One Time Password (OTP)** to reset your password. This code will expire in **10 minutes**.

@component('mail::panel')
{{ $otp }}
@endcomponent

If you didn't request a password reset, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
 