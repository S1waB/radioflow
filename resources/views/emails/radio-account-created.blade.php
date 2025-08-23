@component('mail::message')
# Welcome to RadioFlow, {{ $name }}!

Your account for **{{ $radioName }}** has been created with the role of **{{ $role }}**.

Here are your login details:
- **Email:** {{ $email }}
- **Password:** {{ $password }}

@component('mail::button', ['url' => route('login')])
Login to RadioFlow
@endcomponent

Please change your password after logging in for the first time.

Thanks,<br>
The RadioFlow Team
@endcomponent