<fieldset class="flex flex-col w-full space-y-1 text-sm space-x-2 pt-2 mt-2 border border-transparent border-t-gray-300">

    <h3 class="text-left font-semibold">
        Pitch Files
        <span class="text-xs font-medium italic">(Selection based on the voice part above.)</span>
    </h3>

    <div class="flex flex-col justify-start items-start space-y-2">
        @foreach($form->pitchFiles AS $pitchFile)
            @if($pitchFile['file_type'] === 'pdf')
                <a
                    href="https://auditionsuite-production.s3.amazonaws.com/{{ $pitchFile['url'] }}"
                    wire:key="pitch-file-{{ $pitchFile['id'] }}"
                    target="_blank"
                >
                    <button
                        type="button"
                        class="bg-green-500 text-white text-xs px-2 border border-green-800 shadow-lg rounded-lg"
                    >
                        {{ $pitchFile['description'] }}
                    </button>
                </a>
            @else
                <div class=" shadow-lg p-2" wire:key="pitchFile-{{ $pitchFile['id'] }}">
                <h4 class="font-semibold">{{ $pitchFile['description']  }}</h4>
                <div>
                    <audio id="audioPlayer-{{ $pitchFile['id'] }}" class="mx-auto" controls
                           style="display: block">
                        <source id="audioSource-{{ $pitchFile['id'] }}"
                                src="https://auditionsuite-production.s3.amazonaws.com/{{ $pitchFile['url'] }}"
                                type="audio/mpeg"
                        >
                        " Your browser does not support the audio element. "
                    </audio>

                </div>
            </div>
            @endif
        @endforeach
    </div>

</fieldset>
