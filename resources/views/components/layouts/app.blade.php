<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Job bord App @isset($pageTitle)| {{ $pageTitle }} @endisset</title>
        @vite('resources/css/app.css')
    </head>
    <body class="mx-auto mt-10 max-w-2xl text-slate-700 bg-gradient-to-r from-indigo-100 from-10% via-sky-300 via-30% to-emerald-100 to-90%">
        {{ $slot }}
    </body>
</html>
