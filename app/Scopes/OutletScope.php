<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OutletScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        // Apply scope only if user is Super Admin and an outlet is selected in session
        if ($user && $user->role === 'Super Admin') {
            $selectedOutletId = Session::get('selected_super_admin_outlet_id');
            if ($selectedOutletId) {
                $builder->where('outlet_id', $selectedOutletId);
            }
        }
        // For non-super admins, apply a direct outlet filter if they have one
        // This is a safety measure to ensure non-super admins only see their own outlet's data.
        // It might be redundant if the resource already handles this, but provides an extra layer.
        else if ($user && $user->outlet_id) {
            $builder->where('outlet_id', $user->outlet_id);
        }
    }
}
