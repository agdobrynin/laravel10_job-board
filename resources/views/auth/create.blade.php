<x-layouts.app pageTitle="Do Sign In">
    <h1 class="mb-4 text-center text-2xl font-medium text-slate-600">
        Sign in to your account
    </h1>

    <x-ui.card>
        <form action="{{ route('auth.store') }}" method="post">
            @csrf
            <div class="mb-4">
                <x-ui.input name="email" value="{{old('email')}}"  label="Email" :clearBtn="false"/>
            </div>
            <div class="mb-4">
                <x-ui.input name="password" type="password" label="Password" :clearBtn="false"/>
            </div>
            <div class="flex justify-between">
                <div>
                    <a class="link" href="#">
                        Forget password?
                    </a>
                </div>
                <div>
                    <x-ui.button type="submit">🔑 Do login</x-ui.button>
                </div>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
