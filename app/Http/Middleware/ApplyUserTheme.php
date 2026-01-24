<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyUserTheme
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        \Illuminate\Support\Facades\Log::info('ApplyUserTheme Middleware Running', [
            'user_id' => $user?->id,
            'theme_color' => $user->theme_color ?? 'null'
        ]);

        if ($user && !empty($user->theme_color)) {
            $colorName = $user->theme_color;

            // Map simple color names to Filament Color objects
            $colors = [
                'indigo' => Color::Indigo,
                'blue' => Color::Blue,
                'red' => Color::Red,
                'green' => Color::Green,
                'teal' => Color::Teal,
                'orange' => Color::Orange,
                'purple' => Color::Purple,
                'pink' => Color::Pink,
                'slate' => Color::Slate,
                'amber' => Color::Amber,
            ];

            $primaryColor = $colors[$colorName] ?? Color::Indigo;

            FilamentColor::register([
                'primary' => $primaryColor,
            ]);
        }

        return $next($request);
    }
}
