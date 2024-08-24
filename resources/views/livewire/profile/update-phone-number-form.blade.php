<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $phoneHome = '';
    public string $phoneMobile = '';

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
    public function updatePhoneNumbers(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'phoneHome' => ['nullable', 'string', 'max:24'],
            'phoneMobile' => ['nullable', 'string', 'max:24'],
        ]);

        $service = new \App\Services\FormatPhoneService();
        $updatedHome = $this->updatePhone($service->getPhoneNumber($this->phoneHome), 'home');
        $updatedMobile = $this->updatePhone($service->getPhoneNumber($this->phoneMobile), 'mobile');

        if($updatedHome || $updatedMobile){
            $this->resetPhones();
            $this->dispatch('phone-number-updated', name: $user->name);
        }

    }

    public function updatePhone(string $phoneNumber, string $phoneType): bool
    {
        return (bool)\App\Models\PhoneNumber::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'phone_type' => $phoneType
                ],
                [
                    'phone_number' => $phoneNumber,
                ]
            );
    }

    private function resetPhones()
    {
        $this->phoneHome = \App\Models\PhoneNumber::query()
            ->where('user_id', auth()->id())
            ->where('phone_type', 'home')
            ->first()
            ->phone_number;

        $this->phoneMobile = \App\Models\PhoneNumber::query()
            ->where('user_id', auth()->id())
            ->where('phone_type', 'mobile')
            ->first()
            ->phone_number;
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
