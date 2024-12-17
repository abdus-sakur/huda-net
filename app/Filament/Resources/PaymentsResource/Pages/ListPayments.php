<?php

namespace App\Filament\Resources\PaymentsResource\Pages;

use Filament\Actions;
use App\Models\Payments;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PaymentsResource;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Input Pembayaran')
                ->action(function (array $data, $record) {
                    Payments::create($data);

                    Notification::make()
                        ->title('Simpan pembayaran')
                        ->success()
                        ->send();
                }),
        ];
    }
}
