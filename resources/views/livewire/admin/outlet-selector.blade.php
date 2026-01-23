<div>
    @if (auth()->user()->role === 'Admin')
        <x-filament::input.wrapper>
            <x-filament::input.select wire:model.live="selectedOutlet" :options="$outlets->pluck('name', 'id')">
                <option value="">All Outlets</option>
            </x-filament::input.select>
        </x-filament::input.wrapper>
    @endif
</div>
