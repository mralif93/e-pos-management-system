<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Outlet;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class OutletSelector extends Component
{
    public $selectedOutlet = null;

    public function mount()
    {
        if (Auth::user()->role === 'Admin') {
            $this->selectedOutlet = Session::get('selected_admin_outlet_id', null);
        } else {
            $this->selectedOutlet = Auth::user()->outlet_id;
        }
    }

    public function updatedSelectedOutlet($value)
    {
        if (Auth::user()->role === 'Admin') {
            Session::put('selected_admin_outlet_id', $value);
        }
        $this->dispatch('outletChanged');
    }

    public function render()
    {
        if (Auth::user()->role !== 'Admin') {
            return view('livewire.admin.outlet-selector', [
                'outlets' => collect([]),
            ]);
        }

        return view('livewire.admin.outlet-selector', [
            'outlets' => Outlet::all(),
        ]);
    }
}
