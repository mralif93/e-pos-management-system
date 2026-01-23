@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\Outlet;

    $user = Auth::user();
    $userOutletName = 'No Outlet Assigned';

    if ($user) {
        if ($user->role === 'Super Admin') {
            // Livewire switcher will be rendered
        } else {
            // This section will display the non-admin user's outlet info
            if ($user->outlet_id) {
                $outlet = Outlet::find($user->outlet_id);
                $userOutletName = $outlet ? $outlet->name : 'N/A';
            }
        }
    }
@endphp

<div class="flex items-center gap-x-4">
    @if ($user && $user->role === 'Super Admin')
        <div class="fi-dropdown">
            @livewire('admin.outlet-switcher')
        </div>
    @else
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
            <span class="text-gray-950 dark:text-white font-semibold">{{ $userOutletName }}</span>
        </div>
    @endif
</div>
