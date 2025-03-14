<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $firstName = '';
    public string|null $middleName = '';
    public string|null $lastName = '';
    public string $name = '';
    public string $email = '';
    public bool $missingFullName = true;
    public array $missingFullNameErrorMessage =  [ "The name field must include both first and last names.","Please update the name field to continue..."];
    public int $pronoun_id = 1; //default
    public array $pronouns = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->firstName = Auth::user()->first_name ?? '';
        $this->middleName = Auth::user()->middle_name ?? '';
        $this->lastName = Auth::user()->last_name ?? '';
        $this->email = Auth::user()->email;
        $this->pronoun_id = Auth::user()->pronoun_id;
        $this->pronouns = \App\Models\Pronoun::pluck('descr','id')->toArray();
        $this->missingFullName = $this->getMissingFullNameBool();
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'firstName' => ['required', 'string', 'max:255'],
            'middleName' => ['nullable', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'pronoun_id' => ['required','int','exists:pronouns,id'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->first_name = $this->firstName;
        $user->middle_name = $this->middleName;
        $user->last_name = $this->lastName;

        $user->name = trim($this->firstName) . ' ';
        if(strlen($this->middleName)){
            $user->name .= trim($this->middleName) . ' ';
        }
        $user->name .= trim($this->lastName);

        $user->save();

        $this->missingFullName = $this->getMissingFullNameBool();

        $this->js('window.location.reload()');

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    private function getMissingFullNameBool(): bool
    {
        return (! ($this->firstName && $this->lastName));
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">

        <div>
            <x-input-label for="firstName" :value="__('Name')"/>
            <div class="flex flex-row space-x-2 items-center">
                <x-input-label style="min-width: 3rem;" for="firstName" :value="__('First')"/>
                <x-text-input wire:model="firstName" id="firstName" name="firstName" type="text"
                              class="mt-1 block w-full" required autocomplete="firstName"/>
                <x-input-error class="mt-2" :messages="$errors->get('firstName')"/>
            </div>
            <div class="flex flex-row space-x-2 items-center">
                <x-input-label style="min-width: 3rem;" for="middleName" :value="__('Middle')"/>
                <x-text-input wire:model="middleName" id="middleName" name="middleName" type="text"
                              class="mt-1 block w-full" autocomplete="middleName"/>
                <x-input-error class="mt-2" :messages="$errors->get('middleName')"/>
            </div>
            <div class="flex flex-row space-x-2 items-center">
                <x-input-label style="min-width: 3rem;" for="lastName" :value="__('Last')"/>
                <x-text-input wire:model="lastName" id="lastName" name="lastName" type="text"
                              class="mt-1 block w-full" required autocomplete="lastName"/>
                <x-input-error class="mt-2" :messages="$errors->get('lastName')"/>
            </div>
            @if($missingFullName)
                <x-input-error class="mt-2" :messages="$missingFullNameErrorMessage" />
            @endif
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="pronoun_id" :value="__('Preferred Pronoun')" />
            <select wire:model.live="pronoun_id" >
                @foreach($pronouns AS $id => $pronounDescr)
                    <option value="{{ $id }}">
                        {{ $pronounDescr }}
                    </option>
                @endforeach
            </select>

            <x-input-error class="mt-2" :messages="$errors->get('pronounId')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
