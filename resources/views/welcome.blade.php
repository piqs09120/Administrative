<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Hotel & Restaurant Management System') }}</title>
    <script>
        // Redirect to dashboard
        window.location.href = "{{ route('dashboard') }}";
    </script>
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh; font-family: Arial, sans-serif;">
        <div style="text-align: center;">
            <h1>{{ config('app.name', 'Hotel & Restaurant Management System') }}</h1>
            <p>Redirecting to dashboard...</p>
            <p><a href="{{ route('dashboard') }}">Click here if not redirected automatically</a></p>
        </div>
    </div>
</body>
</html>