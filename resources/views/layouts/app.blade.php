<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Photobooth')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
    <main>
        @if(session('error'))
        <div style="color:red;">{{ session('error') }}</div>
        @endif

        @if(session('success'))
        <div style="color:green;">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>

</body>

</html>