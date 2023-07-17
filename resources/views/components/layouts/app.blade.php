<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Job bord App @isset($pageTitle)| {{ $pageTitle }} @endisset</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="mx-auto mt-4 max-w-2xl text-slate-700 bg-gradient-to-r from-indigo-100 from-10% via-sky-300 via-30% to-emerald-100 to-90% pb-10">
        <nav class="mb-4 flex justify-between text-lg font-medium">
            <ul class="flex space-x-2">
                <li>
                    <a class="link" href="{{ route('vacancies.index') }}">Home</a>
                </li>
            </ul>

            <ul class="flex space-x-4">
                @auth
                    <li>
                        <a class="link" href="{{ route('my-vacancy-applications.index') }}">
                            {{ auth()->user()->name ?? 'Guest' }}
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('auth.destroy') }}" method="POST">
                            @csrf
                            @method('delete')
                            <button class="link">Logout</button>
                        </form>
                    </li>
                @else
                    <li>
                        <a class="link" href="{{ route('auth.create') }}">Sign in</a>
                    </li>
                    <li>
                        <a class="link" href="{{ route('reg.create') }}">Sign up</a>
                    </li>
                @endauth
            </ul>
        </nav>

        @if (session('error'))
            <x-ui.alert-error class="shadow-md hover:shadow-lg">
                <x-slot:header>Error action!</x-slot:header>
                <p>{{ session('error') }}</p>
            </x-ui.alert-error>
        @endif

        @if (session('success'))
            <x-ui.alert-success class="shadow-md hover:shadow-lg">
                <x-slot:header>Success action!</x-slot:header>
                <p>{{ session('success') }}</p>
            </x-ui.alert-success>
        @endif

        {{ $slot }}
    </body>
</html>
