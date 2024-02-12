<?php

namespace App\Filament\Resources\DeptReportResource\Pages;

use App\Filament\Resources\DeptReportResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDeptReports extends ManageRecords
{
    protected static string $resource = DeptReportResource::class;

    protected function getActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
