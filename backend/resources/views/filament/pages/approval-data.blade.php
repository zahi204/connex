<div class="p-4 space-y-2">
    @if(is_array($data))
        @foreach($data as $key => $value)
            <div class="flex gap-2">
                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ str($key)->headline() }}:</span>
                <span class="text-gray-600 dark:text-gray-400">
                    @if(is_array($value))
                        {{ implode(', ', $value) }}
                    @else
                        {{ $value }}
                    @endif
                </span>
            </div>
        @endforeach
    @else
        <p class="text-gray-500 dark:text-gray-400">No data submitted.</p>
    @endif
</div>
