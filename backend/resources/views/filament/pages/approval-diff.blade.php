<div class="space-y-4 p-2">
    {{-- Header --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $record->submittedBy?->phone ?? 'לא ידוע' }}
                    מבקש לעדכן
                    <span class="text-primary-600 dark:text-primary-400">{{ count($submitted) }} שדה/ות</span>
                    @if($entityName)
                        עבור <strong>{{ $entityName }}</strong>
                    @endif
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    פעולה: <strong>{{ $record->action_type?->value ?? $record->action_type }}</strong>
                    &middot; הוגש {{ $record->created_at->diffForHumans() }}
                </p>
            </div>
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                {{ $record->status?->value === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                {{ strtoupper($record->status?->value ?? 'unknown') }}
            </span>
        </div>
    </div>

    {{-- Diff Table --}}
    @if(count($submitted) > 0)
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300 w-1/4">שדה</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-500 dark:text-gray-400 w-3/8">ערך נוכחי</th>
                        <th class="px-4 py-3 text-right font-semibold text-green-600 dark:text-green-400 w-3/8">ערך מוצע</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @foreach($submitted as $field => $newValue)
                        @php
                            $oldValue = $current[$field] ?? null;
                            $changed = $oldValue !== $newValue;
                        @endphp
                        <tr class="{{ $changed ? 'bg-yellow-50/30 dark:bg-yellow-900/10' : '' }}">
                            <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300 capitalize">
                                {{ str_replace('_', ' ', $field) }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                @if($oldValue === null)
                                    <em class="text-gray-400">—</em>
                                @elseif(is_array($oldValue))
                                    {{ implode(', ', $oldValue) }}
                                @elseif(is_bool($oldValue))
                                    {{ $oldValue ? 'כן' : 'לא' }}
                                @else
                                    {{ $oldValue }}
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs {{ $changed ? 'text-green-700 dark:text-green-300 font-semibold' : 'text-gray-500 dark:text-gray-400' }}">
                                @if($newValue === null)
                                    <em class="text-gray-400">—</em>
                                @elseif(is_array($newValue))
                                    {{ implode(', ', $newValue) }}
                                @elseif(is_bool($newValue))
                                    {{ $newValue ? 'כן' : 'לא' }}
                                @else
                                    {{ $newValue }}
                                @endif
                                @if($changed)
                                    <span class="ms-1 text-yellow-600 dark:text-yellow-400">✎</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400 italic">לא הוגשו נתונים ברמת שדה.</p>
    @endif
</div>
