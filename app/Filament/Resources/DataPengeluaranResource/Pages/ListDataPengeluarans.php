<?php

namespace App\Filament\Resources\DataPengeluaranResource\Pages;

use App\Filament\Resources\DataPengeluaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataPengeluarans extends ListRecords
{
    protected static string $resource = DataPengeluaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
