<?php

namespace App\Filament\Resources\OccurenceResource\Pages;

use App\Filament\Resources\OccurenceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Tables\Actions\CreateAction;

class ManageOccurences extends ManageRecords
{
    protected static string $resource = OccurenceResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = auth()->id();
                    $data['department'] = auth()->user()->department;

                    return $data;
                })
        ];
    }
}
