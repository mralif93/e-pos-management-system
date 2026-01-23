<div class="fi-fo-select w-full">
    <div class="fi-input-wrapper flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white focus-within:ring-2 dark:bg-white/5 ring-gray-950/10 dark:ring-white/20 focus-within:ring-primary-600 dark:focus-within:ring-primary-500">
        <select id="outlet-switcher-select"
                wire:model.live="selectedOutlet"
                class="fi-select-input w-full border-none bg-transparent py-1.5 pe-8 ps-3 text-base text-gray-950 outline-none transition duration-75 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6">
            <option value="">Select an Outlet</option>
            @foreach ($outlets as $outlet)
                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
            @endforeach
        </select>
    </div>
</div>

