<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Error')</title>

    @vite(['resources/css/app.css'])

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
            background: #84cc16;
            /* Lime 500 */
            color: #1a2e05;
            /* Lime 950 for text contrast */
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        }

        a:hover {
            background: #65a30d;
            /* Lime 600 */
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