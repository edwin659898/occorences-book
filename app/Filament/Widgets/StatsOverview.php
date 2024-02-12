<?php

namespace App\Filament\Widgets;

use App\Models\Occurence;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Kiambere Occurence last 24 Hours',
                Occurence::where('created_at', '>', Carbon::now()->subHours(24))
                ->WhereHas('user', function ($query) {
                    $query->where('site', 'Kiambere');
                })
                ->count()
            ),
            Card::make('Nyongoro Occurence last 24 Hours',
                Occurence::where('created_at', '>', Carbon::now()->subHours(24))
                ->WhereHas('user', function ($query) {
                    $query->where('site', 'Nyongoro');
                })
                ->count()
            ),
            Card::make('7 Forks Occurence last 24 Hours',
                Occurence::where('created_at', '>', Carbon::now()->subHours(24))
                ->WhereHas('user', function ($query) {
                    $query->where('site', '7 Forks');
                })
                ->count()
            ),
            Card::make('Dokolo Occurence last 24 Hours',
            Occurence::where('created_at', '>', Carbon::now()->subHours(24))
            ->WhereHas('user', function ($query) {
                $query->where('site', 'Dokolo');
            })
            ->count()
        ),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('op');
    }
}
