<x-layouts.app pageTitle="Confirm your email">
    <h1 class="mb-4 text-center text-2xl font-medium text-slate-600">
        Your email not confirmed
    </h1>

    <x-ui.card class="text-center" x-data>
        <p class="mb-4">Please confirm your email! We mailed confirmation link your email {{ auth()->user()->email }}</p>
        <p>If you not received confirm link,
            <a href="#" class="link" x-on:click="$refs.form.submit()">please try resend again email</a>
        </p>
        <form x-ref="form" action="{{ route('verification.send') }}" method="post">
            @csrf
        </form>
    </x-ui.card>
</x-layouts.app>
