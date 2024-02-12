<?php

namespace App\Filament\Widgets;

use App\Models\Occurence;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class TypeOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Medical Occurence summary last 30 Days',
                Occurence::where('created_at', '>', now()->subDays(30))
                ->where('type','Medical')
                ->count()
            )->color('success'),
            Card::make('General Ocurrence summary last 30 Days',
                Occurence::where('created_at', '>', now()->subDays(30))
                ->where('type','General')
                ->count()
            ),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('op');
    }
}
