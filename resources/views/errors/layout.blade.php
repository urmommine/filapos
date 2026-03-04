<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Error')</title>

    {{-- kalau pake Vite --}}
    {{-- @vite(['resources/css/app.css']) --}}

    <style>
        body {
            margin: 0;
            font-family: system-ui, sans-serif;
            background: #0f172a;
            color: #e5e7eb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .card {
            max-width: 420px;
        }

        h1 {
            font-size: 80px;
            margin: 0;
        }

        p {
            opacity: .8;
            margin: 10px 0 30px;
        }

        a {
            padding: 10px 18px;
            background: #22c55e;
            color: #022c22;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            background: #16a34a;
        }
    </style>
</head>
<body>

    <div class="card">
        <h1>@yield('code')</h1>
        <p>@yield('message')</p>

        <a href="{{ url('/') }}">
            Kembali
        </a>
    </div>

</body>
</html>