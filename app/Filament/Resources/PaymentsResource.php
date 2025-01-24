<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Livewire\Livewire;
use Filament\Forms\Form;
use App\Models\Customers;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
// use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\View\Components\Modal;
use App\Filament\Resources\PaymentsResource\Pages;

class PaymentsResource extends Resource
{
    protected static ?string $model = Customers::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Menu';
    protected static ?string $navigationLabel = 'Data Pembayaran';
    protected static ?string $label = 'Data Pembayaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Select::make('customer_id')
                //     ->label('Nama Pelanggan')
                //     ->required()
                //     ->options(fn() => Customers::get()->pluck('name', 'id'))
                //     ->searchable(),
                // Select::make('type')
                //     ->label('Jenis Pembayaran')
                //     ->required()
                //     ->options([
                //         'Transfer' => 'Transfer',
                //         'Tunai' => 'Tunai/Cash',
                //     ]),
                // TextInput::make('month')
                //     ->required()
                //     ->type('month')
                //     ->label('Bulan Pembayaran'),
                // TextInput::make('amount')
                //     ->required()
                //     ->numeric()
                //     ->label('Nominal'),
                // Textarea::make('note')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->query(Customers::filterYear(self::getTahunFilter()))
            ->columns([
                TextColumn::make('name')->label('Nama Pelanggan'),
                TextColumn::make('Januari')
                    // ->html()
                    // ->formatStateUsing(function ($state, $record) {
                    //     return view('components.btn-modal', [
                    //         'recordId' => $record->id
                    //     ]);
                    // })
                    ->action(self::actionPayment())
                    ->default(fn($record) => self::infoPayment($record, 1, self::getTahunFilter())),
                TextColumn::make('Februari')
                    ->action(self::actionPayment())
                    ->default(fn($record) => self::infoPayment($record, 2, self::getTahunFilter())),
                TextColumn::make('Maret')
                    ->default(fn($record) => self::infoPayment($record, 3, self::getTahunFilter())),
                TextColumn::make('April')
                    ->default(fn($record) => self::infoPayment($record, 4, self::getTahunFilter())),
                TextColumn::make('Mei')
                    ->default(fn($record) => self::infoPayment($record, 5, self::getTahunFilter())),
                TextColumn::make('Juni')
                    ->default(fn($record) => self::infoPayment($record, 6, self::getTahunFilter())),
                TextColumn::make('Juli')
                    ->default(fn($record) => self::infoPayment($record, 7, self::getTahunFilter())),
                TextColumn::make('Agustus')
                    ->default(fn($record) => self::infoPayment($record, 8, self::getTahunFilter())),
                TextColumn::make('September')
                    ->default(fn($record) => self::infoPayment($record, 9, self::getTahunFilter())),
                TextColumn::make('Oktober')
                    ->default(fn($record) => self::infoPayment($record, 10, self::getTahunFilter())),
                TextColumn::make('November')
                    ->default(fn($record) => self::infoPayment($record, 11, self::getTahunFilter())),
                TextColumn::make('Desember')
                    ->default(fn($record) => self::infoPayment($record, 12, self::getTahunFilter())),
            ])
            ->filters([
                SelectFilter::make('tahun')
                    ->label('Tahun')
                    // ->relationship('payment', 'year')
                    // ->default(2024)
                    ->options([
                        '2021' => 2021,
                        '2022' => 2022,
                        '2023' => 2023,
                        '2024' => 2024,
                        '2025' => 2025,
                        '2026' => 2026,
                        '2027' => 2027,
                        '2028' => 2028,
                        '2029' => 2029,
                        '2030' => 2030,
                    ])
                    ->query(fn($query, $data) => $query)
            ], layout: FiltersLayout::AboveContent);
        // ->actions([
        //     Action::make('view')
        //         ->label('View Details')
        //         ->modalContent(function ($record) {
        //             return view('components.modal', [
        //                 'record' => $record
        //             ]);
        //         })
        //         ->modalSubmitAction(false) // Removes the submit button
        //         ->modalCancelAction(false) // Optional: removes the cancel button
        //         ->modalWidth('md')
        // ]);
    }

    protected static function getTahunFilter()
    {
        $livewire = Livewire::current();
        return $livewire->tableFilters['tahun']['value'] ?? '2024';
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
        ];
    }

    public static function send_wa($record, $month, $year)
    {
        $href = "https://api.whatsapp.com/send/?phone=$record->phone&text=Salam+Bapak%2FIbu%0A%0AKami+informasikan+Invoice+Internet+anda+telah+terbit+dan+dapat+di+bayarkan%2C+berikut+rinciannya+%3A%0A%0ANama+Pelanggan+%3A+{$record->name}%0ATagihan+Bulan+%3A+{$month}%0APaket%3A+Internet+{$record->bandwidth}%0ATotal+Tagihan+%3A+{$record->price}%0AJatuh+Tempo+%3A+25+{$month}+{$year}%0A%0ABisa+membayar+dengan+transfer+ke+admin+kami+di%0ABank+Mandiri+a%2Fn+Miftakhul+Huda%0A%0ATerimakasih.&type=custom_url&app_absent=0";
        return new HtmlString("<a href='$href' target='_blank' style='padding:5px 10px;background-color:darkseagreen;border-radius:15px;font-size:11px;'>Bayar</a>");
    }

    public static function infoPayment($record, $month, $year)
    {
        $db_month = 1;
        $db_year = 9999;
        if (isset($record->payment[0]->month) && $record->payment[0]->month) {
            $date = explode('-', $record->payment[0]->month);
            $db_year = $date[0];
            $db_month = intval($date[1]);
        }
        if (isset($record->payment)) {
            foreach ($record->payment as $payment):
                $date = explode('-', $payment->month);
                $db_year = $date[0];
                $db_month = intval($date[1]);
                if ($db_month == $month && $db_year == $year) {
                    return new HtmlString("<span style='padding:5px 10px;background-color:lightskyblue;border-radius:15px;font-size:11px;' disabled>LUNAS</span>");
                }
            endforeach;
        }

        $month_name = Carbon::create(null, $month)->translatedFormat('F');
        return self::send_wa($record, $month_name, $year);
    }

    public static function checkPayment($record, $month, $year)
    {
        $payment = false;
        foreach ($record->payment as $payment):
            $date = explode('-', $payment->month);
            $db_year = $date[0];
            $db_month = intval($date[1]);
            if ($db_month == $month && $db_year == $year) {
                return true;
            }
        endforeach;

        return $payment;
    }

    public static function actionPayment()
    {
        return Action::make('payment')
            ->visible(true)
            ->modalWidth('sm')
            ->color('primary')
            ->form([
                Select::make('type')
                    ->label('Jenis Pembayaran')
                    ->required()
                    ->options([
                        'Transfer' => 'Transfer',
                        'Tunai' => 'Tunai/Cash',
                    ]),
                Textarea::make('note')
            ])->action(function (array $data, $record) {
                //action save
                Notification::make()
                    ->title($data['jenis_kenaikan'])
                    ->success()
                    ->send();
            })
            ->closeModalByClickingAway(false)
            ->modalFooterActionsAlignment('right')
            ->modalSubmitActionLabel('Simpan');
    }
}
