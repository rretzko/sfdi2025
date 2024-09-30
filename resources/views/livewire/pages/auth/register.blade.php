<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    //Honeypot field
    public string $poohBear = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if($this->poohBear != ''){
            logger('**** SPAMMER DETECTED using name: ' . $validated['name'] . ' ****');
            $this->reset();
            return;
        }

        event(new Registered($user = User::create($validated)));

        $this->updateNames($user);

        $this->makeStudent($user);

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    /**
     * @param  User  $user
     * @return void
     */
    private function makeStudent(User $user):void
    {
        $service = new \App\Services\CalcSeniorYearService();
        $seniorYear = $service->getSeniorYear();

        \App\Models\Student::create(
            [
                'id' => $user->id, //synchronize id and user_id
                'user_id' => $user->id,
                'voice_part_id' => 63, //default soprano i
                'class_of' => $seniorYear,
                'birthday' => date('Y-m-d'),
            ]
        );
    }

    private function updateNames(User $user): void
    {
        $service = new \App\Services\SplitNameIntoNamePartsService($user->name);

        $parts = $service->getNameParts();

        $user->prefix_name = $parts['prefix_name'];
        $user->first_name = $parts['first_name'];
        $user->middle_name = $parts['middle_name'];
        $user->last_name = $parts['last_name'];
        $user->suffix_name = $parts['suffix_name'];

        $user->save();
    }
}; ?>

<div>
    <form wire:submit="register">

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <input type="text" aria-hidden="true" class="hidden" wire:model="poohBear" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
