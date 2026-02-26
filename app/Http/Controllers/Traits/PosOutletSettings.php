<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\DB;

trait PosOutletSettings
{
    protected function getOutletSettings($user): array
    {
        return $user && $user->outlet ? $user->outlet->settings : [];
    }

    protected function createPosToken($user, int $minutes = 10): ?string
    {
        if (!$user) {
            return null;
        }

        return $user->createToken(
            'pos-token',
            ['pos:access'],
            now()->addMinutes($minutes)
        )->plainTextToken;
    }
}
