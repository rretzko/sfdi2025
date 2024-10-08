<fieldset class="flex flex-col w-full space-y-1 text-sm space-x-2 pt-2 pb-4 border border-transparent border-t-gray-300 bg-gray-100">

    <h3 class="text-left font-semibold">Audition Recordings</h3>

    <div class="flex flex-col justify-start items-start space-y-2">
        @foreach($form->fileUploads AS $uploadType)
            <div class=" shadow-lg p-2 bg-white" wire:key="auditionFile-{{ $uploadType }}">
                <h4 class="font-semibold">{{ ucwords($uploadType) }} Recording</h4>
                @if(array_key_exists($uploadType, $form->recordings) && count($form->recordings[$uploadType]))
                    <div>
                        <audio id="audioPlayer-{{ $uploadType }}" class="mx-auto" controls
                               style="display: block">
                            <source id="audioSource-{{ $uploadType }}"
                                    src="https://auditionsuite-production.s3.amazonaws.com/{{ $form->recordings[$uploadType]['url'] }}"
                                    type="audio/mpeg"
                            >
                            " Your browser does not support the audio element. "
                        </audio>

                        <div class="flex flex-row w-full mt-2 space-x-4 justify-center">
                            @if(array_key_exists('approved', $form->recordings[$uploadType]) &&
                                strlen($form->recordings[$uploadType]['approved']))
                                <div class="text-xs text-green-600">
                                    Approved: {{ $form->recordings[$uploadType]['approved'] }}
                                </div>
                            @else
                                <button class="px-2 rounded-full text-sm bg-red-600 text-red-100"
                                        wire:click="recordingReject('{{  $uploadType }}')"
                                        wire:confirm="This will PERMANENTLY delete the uploaded {{ $uploadType }} file.\nClick OK to proceed or Cancel to stop this action."
                                        type="button"
                                >
                                    Remove
                                </button>
                            @endif
                        </div>
                    </div>
                @else
                    <x-forms.elements.livewire.audioFileUpload
                        label=""
                        name="auditionFiles.{{  $uploadType }}"
                    />
                @endif

            </div>
        @endforeach
    </div>

</fieldset>
