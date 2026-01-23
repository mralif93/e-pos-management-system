<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Outlet;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class OutletSwitcher extends Component
{
    public $selectedOutlet = null;

    public function mount()
    {
        if (Auth::user()->role === 'Super Admin') {
            $this->selectedOutlet = Session::get('selected_super_admin_outlet_id', null);
        }
    }

    public function updatedSelectedOutlet($value)
    {
        if (Auth::user()->role === 'Super Admin') {
            Session::put('selected_super_admin_outlet_id', $value);
        }
        $this->dispatch('outletChanged'); // Dispatch event for other components to react
    }

    public function render()
    {
        if (Auth::user()->role !== 'Super Admin') {
            return view('livewire.admin.outlet-switcher', [
                'outlets' => collect([]),
            ]);
        }

        return view('livewire.admin.outlet-switcher', [
            'outlets' => Outlet::all(),
        ]);
    }
}