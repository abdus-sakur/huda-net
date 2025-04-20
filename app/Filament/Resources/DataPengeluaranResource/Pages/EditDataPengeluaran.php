<?php

namespace App\Filament\Resources\DataPengeluaranResource\Pages;

use App\Filament\Resources\DataPengeluaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataPengeluaran extends EditRecord
{
    protected static string $resource = DataPengeluaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
