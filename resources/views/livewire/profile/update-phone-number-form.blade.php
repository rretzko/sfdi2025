<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->phoneMobile = \App\Models\PhoneNumber::where('user_id', auth()->id())
        ->where('phone_type','mobile')
        ->first()
        ->phone_number ?? '';

        $this->phoneHome = \App\Models\PhoneNumber::where('user_id', auth()->id())
            ->where('phone_type','home')
            ->first()
            ->phone_number ?? '';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updatePhoneNumber(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'phoneHome' => ['nullable', 'string', 'max:24'],
            'phoneMobile' => ['nullable', 'string', 'max:24'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('phone-number-updated', name: $user->name);
    }

}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Phone Number(s)') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your home and cell phone numbers.") }}
        </p>
    </header>

    <form wire:submit="updatePhoneNumbers" class="mt-6 space-y-6">
        <div>
            <x-input-label for="phoneMobile" :value="__('Cell Phone')" />
            <x-text-input wire:model="phoneMobile" id="phoneMobile" name="phoneMobile" type="text" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('phoneMobile')" />
        </div>

        <div>
            <x-input-label for="phoneHome" :value="__('Home Phone')" />
            <x-text-input wire:model="phoneHome" id="phoneHome" name="phoneHome" type="text" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('phoneHome')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="phone-number-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
