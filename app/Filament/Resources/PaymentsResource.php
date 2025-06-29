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
use App\Models\Payments;
use Faker\Provider\ar_EG\Payment;
use Filament\Forms\Components\Hidden;

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
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Pelanggan')->searchable(),
                TextColumn::make('Januari')
                    // ->html()
                    ->action(self::actionPayment(1, self::getTahunFilter()))
                    ->disabledClick(true)
                    ->default(fn($record) => self::infoPayment($record, 1, self::getTahunFilter())),
                TextColumn::make('Februari')
                    ->disabledClick(true)
                    ->action(self::actionPayment(2, self::getTahunFilter()))
                    ->disabledClick(true)
                    ->default(fn($record) => self::infoPayment($record, 2, self::getTahunFilter())),
                TextColumn::make('Maret')
                    ->action(self::actionPayment(3, self::getTahunFilter()))
                    ->disabledClick(true)
                    ->default(fn($record) => self::infoPayment($record, 3, self::getTahunFilter())),
                TextColumn::make('April')
                    ->action(self::actionPayment(4, self::getTahunFilter()))
                    ->disabledClick(true)
                    ->default(fn($record) => self::infoPayment($record, 4, self::getTahunFilter())),
                TextColumn::make('Mei')
                    ->action(self::actionPayment(5, self::getTahunFilter()))
                    ->disabledClick(true)
                    ->default(fn($record) => self::infoPayment($record, 5, self::getTahunFilter())),
                TextColumn::make('Juni')
                    ->action(self::actionPayment(6, self::getTahunFilter()))
                    ->disabledClick(true)
                    ->default(fn($record) => self::infoPayment($record, 6, self::getTahunFilter())),
                TextColumn::make('Juli')
                    ->action(self::actionPayment(7, self::getTahunFilter()))
                    ->disabledClick(true)
                    ->default(fn($record) => self::infoPayment($record, 7, self::getTahunFilter())),
                TextColumn::make('Agustus')
                    ->action(self::actionPayment(8, self::getTahunFilter()))
                    ->disabledClick(true)
                    ->default(fn($record) => self::infoPayment($record, 8, self::getTahunFilter())),
                TextColumn::make('September')
                    ->action(self::actionPayment(9, self::getTahunFilter()))
                    ->disabledClick(true)
                    ->default(fn($record) => self::infoPayment($record, 9, self::getTahunFilter())),
                TextColumn::make('Oktober')
                    ->action(self::actionPayment(10, self::getTahunFilter()))
                    ->disabledClick(true)
                    ->default(fn($record) => self::infoPayment($record, 10, self::getTahunFilter())),
                TextColumn::make('November')
                    ->action(self::actionPayment(11, self::getTahunFilter()))
                    ->disabledClick(true)
                    ->default(fn($record) => self::infoPayment($record, 11, self::getTahunFilter())),
                TextColumn::make('Desember')
                    ->action(self::actionPayment(12, self::getTahunFilter()))
                    ->disabledClick(true)
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
    }

    protected static function getTahunFilter()
    {
        $livewire = Livewire::current();
        return $livewire->tableFilters['tahun']['value'] ?? date('Y');
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

    public static function send_wa($record, $month_name, $year, $month)
    {

        $href = "https://api.whatsapp.com/send/?phone=$record->phone&text=Salam+Bapak%2FIbu%0A%0AKami+informasikan+Invoice+Internet+anda+telah+terbit+dan+dapat+di+bayarkan%2C+berikut+rinciannya+%3A%0A%0ANama+Pelanggan+%3A+{$record->name}%0ATagihan+Bulan+%3A+{$month_name}%0APaket%3A+Internet+{$record->bandwidth}%0ATotal+Tagihan+%3A+{$record->price}%0AJatuh+Tempo+%3A+25+{$month_name}+{$year}%0A%0ABisa+membayar+dengan+transfer+ke+admin+kami+di%0ABank+Mandiri+a%2Fn+Miftakhul+Huda%0A%0ATerimakasih.&type=custom_url&app_absent=0";
        return new HtmlString("<a href='$href' target='_blank' style='padding:5px 10px;background-color:darkseagreen;border-radius:15px;font-size:11px;'>WA</a><br>
        <button wire:click=\"mountTableAction('payment{$month}', $record->id)\" wire:loading.attr=\"disabled\" wire:target=\"mountTableAction('payment{$month}', $record->id)\" style='padding:2px 10px;background-color:orange;border-radius:15px;font-size:11px;margin-top:3px;'>Bayar</button>");
    }

    public static function infoPayment($record, $month, $year)
    {
        if (isset($record->payment)) {
            foreach ($record->payment as $payment):
                if ($payment->month == $month && $payment->year == $year) {
                    $href = route('invoice.index', ['id' => $record->id, 'month' => $month, 'year' => $year]);
                    return new HtmlString("<a href='{$href}' target='_blank' style='padding:5px 10px;background-color:lightskyblue;border-radius:15px;font-size:11px;'>Invoice</a>");
                }
            endforeach;
        }

        $month_name = Carbon::create(null, $month)->translatedFormat('F');
        return self::send_wa($record, $month_name, $year, $month);
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

    public static function actionPayment($month, $year)
    {
        $month_name = Carbon::create(null, $month)->translatedFormat('F');
        return Action::make('payment' . $month)
            ->label("Form Pembayaran $month_name")
            ->modalDescription(fn($record) => $record->name)
            ->visible(true)
            ->hiddenLabel(false)
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
                Textarea::make('note'),
                Hidden::make('month')->default($month),
                Hidden::make('year')->default($year),
            ])->action(function (array $data, $record) {
                //action save
                Payments::create([
                    'customer_id' => $record->id,
                    'type' => $data['type'],
                    'month' => $data['month'],
                    'year' => $data['year'],
                    'note' => $data['note'],
                    'invoice' => self::generateInvoiceNumber(),
                ]);
                Notification::make()
                    ->title("Simpan Data Pembayaran")
                    ->success()
                    ->send();
            })
            ->closeModalByClickingAway(false)
            ->modalFooterActionsAlignment('right')
            ->modalSubmitActionLabel('Simpan');
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-';
        $now = Carbon::now();

        $yearMonth = $now->format('Ym');
        $lastInvoice = Payments::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();

        $sequence = str_pad($lastInvoice + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $yearMonth . $sequence;
    }
}
