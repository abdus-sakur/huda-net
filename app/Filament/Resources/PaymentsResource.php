<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Payments;
use Filament\Forms\Form;
use App\Models\Customers;
use Filament\Tables\Table;
use Livewire\Attributes\Layout;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PaymentsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PaymentsResource\RelationManagers;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Livewire\Livewire;

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
                Select::make('customer_id')
                    ->label('Nama Pelanggan')
                    ->required()
                    ->options(fn() => Customers::get()->pluck('name', 'id'))
                    ->searchable(),
                Select::make('type')
                    ->label('Jenis Pembayaran')
                    ->required()
                    ->options([
                        'Transfer' => 'Transfer',
                        'Tunai' => 'Tunai/Cash',
                    ]),
                TextInput::make('month')
                    ->required()
                    ->type('month')
                    ->label('Bulan Pembayaran'),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->label('Nominal'),
                Textarea::make('note')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->query(Customers::filterYear(self::getTahunFilter()))
            ->columns([
                TextColumn::make('name')->label('Nama Pelanggan'),
                TextColumn::make('Januari')
                    ->default(fn($record) => self::actionPayment($record, 1, self::getTahunFilter())),
                TextColumn::make('Februari')
                    ->default(fn($record) => self::actionPayment($record, 2, self::getTahunFilter())),
                TextColumn::make('Maret')
                    ->default(fn($record) => self::actionPayment($record, 3, self::getTahunFilter())),
                TextColumn::make('April')
                    ->default(fn($record) => self::actionPayment($record, 4, self::getTahunFilter())),
                TextColumn::make('Mei')
                    ->default(fn($record) => self::actionPayment($record, 5, self::getTahunFilter())),
                TextColumn::make('Juni')
                    ->default(fn($record) => self::actionPayment($record, 6, self::getTahunFilter())),
                TextColumn::make('Juli')
                    ->default(fn($record) => self::actionPayment($record, 7, self::getTahunFilter())),
                TextColumn::make('Agustus')
                    ->default(fn($record) => self::actionPayment($record, 8, self::getTahunFilter())),
                TextColumn::make('September')
                    ->default(fn($record) => self::actionPayment($record, 9, self::getTahunFilter())),
                TextColumn::make('Oktober')
                    ->default(fn($record) => self::actionPayment($record, 10, self::getTahunFilter())),
                TextColumn::make('November')
                    ->default(fn($record) => self::actionPayment($record, 11, self::getTahunFilter())),
                TextColumn::make('Desember')
                    ->default(fn($record) => self::actionPayment($record, 12, self::getTahunFilter())),
            ])
            ->filters([
                SelectFilter::make('tahun')
                    ->label('Tahun')
                    // ->relationship('payment', 'year')
                    // ->default(2024)
                    ->options([
                        '2015' => 2015,
                        '2016' => 2016,
                        '2017' => 2017,
                        '2018' => 2018,
                        '2019' => 2019,
                        '2020' => 2020,
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
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                // Tables\Actions\EditAction::make(),
            ]);
    }

    protected static function getTahunFilter()
    {
        $livewire = Livewire::current();
        // return request()->query('tableFilters')['tahun']['value'] ?? '2024';
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
            // 'create' => Pages\CreatePayments::route('/create'),
            // 'edit' => Pages\EditPayments::route('/{record}/edit'),
        ];
    }

    public static function send_wa($record, $month, $year)
    {
        $href = "https://api.whatsapp.com/send/?phone=$record->phone&text=Salam+Bapak%2FIbu%0A%0AKami+informasikan+Invoice+Internet+anda+telah+terbit+dan+dapat+di+bayarkan%2C+berikut+rinciannya+%3A%0A%0ANama+Pelanggan+%3A+{$record->name}%0ATagihan+Bulan+%3A+{$month}%0APaket%3A+Internet+{$record->bandwidth}%0ATotal+Tagihan+%3A+{$record->price}%0AJatuh+Tempo+%3A+25+{$month}+{$year}%0A%0ABisa+membayar+dengan+transfer+ke+admin+kami+di%0ABank+Mandiri+a%2Fn+Miftakhul+Huda%0A%0ATerimakasih.&type=custom_url&app_absent=0";
        return new HtmlString("<a href='$href' target='_blank' style='padding:5px 10px;background-color:darkseagreen;border-radius:15px;font-size:11px;'>WA</a>");
    }

    public static function actionPayment($record, $month, $year)
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
                    return new HtmlString("<span style='padding:5px 10px;background-color:lightskyblue;border-radius:15px;font-size:11px;'>LUNAS</span>");
                }
            endforeach;
        }
        switch ($month) {
            case 1:
                $month_name = 'Januari';
                break;
            case 2:
                $month_name = 'Februari';
                break;
            case 3:
                $month_name = 'Maret';
                break;
            case 4:
                $month_name = 'April';
                break;
            case 5:
                $month_name = 'Mei';
                break;
            case 6:
                $month_name = 'Juni';
                break;
            case 7:
                $month_name = 'Juli';
                break;
            case 8:
                $month_name = 'Agustus';
                break;
            case 9:
                $month_name = 'September';
                break;
            case 10:
                $month_name = 'Oktober';
                break;
            case 11:
                $month_name = 'November';
                break;
            case 12:
                $month_name = 'Desember';
                break;
        }
        // dd($record);
        if ($db_month == $month && $db_year == $year) {
            return new HtmlString("<span style='padding:5px 10px;background-color:lightskyblue;border-radius:15px;font-size:11px;'>LUNAS</span>");
        }
        return self::send_wa($record, $month_name, $year);
    }
}
