<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error')</title>

    @vite(['resources/css/app.css'])
</head>

<body class="bg-[#0f172a] text-[#e5e7eb] flex items-center justify-center min-h-screen text-center font-sans antialiased">

    <div class="max-w-[420px] p-6">
        <h1 class="text-[80px] font-bold leading-none m-0">@yield('code')</h1>
        <p class="opacity-80 mt-[10px] mb-[30px]">@yield('message')</p>

        <a href="{{ url('/') }}" class="inline-block px-[18px] py-[10px] bg-[#84cc16] text-[#1a2e05] rounded-lg font-semibold no-underline transition-colors duration-200 hover:bg-[#65a30d]">
            Kembali
        </a>
    </div>

</body>

</html>
