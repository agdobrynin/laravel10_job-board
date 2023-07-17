<x-layouts.app pageTitle="Sign up">
    <h1 class="mb-4 text-center text-2xl font-medium text-slate-600">
        Sign up
    </h1>

    <x-ui.card>
        <form action="{{ route('reg.store') }}" method="post">
            @csrf
            <div class="mb-4">
                <x-ui.input name="email" value="{{old('email')}}" label="Email" :clearBtn="false"/>
            </div>
            <div class="mb-4">
                <x-ui.input name="name" value="{{old('name')}}" label="Your name" :clearBtn="false"/>
            </div>
            <div class="mb-4">
                <x-ui.input name="password" type="password" label="Password" :clearBtn="false"/>
            </div>
            <div class="mb-4">
                <x-ui.input name="password_confirmation" type="password" label="Confirmation password" :clearBtn="false"/>
            </div>
            <div
                x-data="{
                    is_employer: false,
                    changeEmployer(el) {
                        this.is_employer = el.checked;

                        if (!this.is_employer) {
                            el.form.employer_name.value = '';
                        }
                    }
                }"
                x-init="is_employer = Boolean({{ old('is_employer') ? '1' : '0' }})">
                <div class="mb-4 ps-2">
                    <x-ui.checkbox
                        x-on:change="changeEmployer($event.target)"
                        name="is_employer"
                        type="checkbox"
                        value="1"
                        :showError="true"
                        :checked="old('is_employer', false)"
                        label="I am employer"/>
                </div>
                <div class="mb-4" x-show="is_employer">
                    <x-ui.input name="employer_name" value="{{ old('employer_name') }}" label="Employer name" :clearBtn="false"/>
                </div>
            </div>
            <x-ui.button class="w-full" type="submit">💼 Register</x-ui.button>
        </form>
    </x-ui.card>
</x-layouts.app>
