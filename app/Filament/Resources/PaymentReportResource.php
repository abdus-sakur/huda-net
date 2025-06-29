<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Tables;
use Livewire\Livewire;
use App\Models\Payments;
use Filament\Tables\Table;
use App\Models\ReportPayments;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\PaymentReportResource\Pages;
use App\Models\Customers;

class PaymentReportResource extends Resource
{
    protected static ?string $model = ReportPayments::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Menu';
    protected static ?string $navigationLabel = 'Report Pembayaran';
    protected static ?string $label = 'Report Pembayaran';
    protected static ?int $navigationSort = 99;

    public $selectedYear;

    public function mount(): void
    {
        $this->selectedYear = request()->get('year', date('Y'));
    }

    public static function table(Table $table): Table
    {
        // $tahun = static::getTahunFilter();
        // $query = ReportPayments::getPaymentSummary($tahun);
        return $table
            ->query(function () {
                $tahun = static::getTahunFilter();

                // Jalankan generate summary jika diperlukan
                ReportPayments::getPaymentSummary($tahun);
                // dd($tahun);
                // Ambil data dari tabel hasil summary
                return ReportPayments::query();
            })
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Bulan')
                    ->formatStateUsing(function ($state) {
                        $months = [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember'
                        ];
                        return $months[$state] ?? $state;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_customers')
                    ->label('Total Pelanggan')
                    ->numeric()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('paid_customers')
                    ->label('Pelanggan Bayar')
                    ->action(self::actionDetail('paid'))
                    ->numeric()
                    ->alignCenter()
                    ->color('success'),

                Tables\Columns\TextColumn::make('unpaid_customers')
                    ->label('Pelanggan Belum Bayar')
                    ->action(self::actionDetail('unpaid'))
                    ->numeric()
                    ->alignCenter()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('total_paid')
                    ->label('Total Dibayar')
                    ->money('IDR')
                    ->alignEnd()
                    ->color('success'),

                Tables\Columns\TextColumn::make('total_unpaid')
                    ->label('Total Belum Dibayar')
                    ->money('IDR')
                    ->alignEnd()
                    ->color('danger'),
            ])->filters([
                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        $years = [];
                        $currentYear = date('Y');
                        for ($year = 2022; $year <= $currentYear; $year++) {
                            $years[$year] = (string) $year;
                        }
                        return $years;
                    })
                    ->query(fn($query, $data) => $query),
            ], layout: FiltersLayout::AboveContent)
            ->paginated(false);
    }

    protected static function getTahunFilter()
    {
        $livewire = Livewire::current();
        return $livewire->tableFilters['tahun']['value'] ?? date('Y');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentReports::route('/'),
            // 'create' => Pages\CreatePaymentReport::route('/create'),
            // 'edit' => Pages\EditPaymentReport::route('/{record}/edit'),
        ];
    }

    public static function actionDetail($type)
    {
        return Action::make('payment' . $type)
            ->label("Pelanggan belum bayar")
            ->visible(true)
            ->hiddenLabel(false)
            ->modalWidth('2xl')
            ->modalContent(function ($record) use ($type) {
                $month = $record->month;
                $month_name = Carbon::create(null, $month)->translatedFormat('F');
                $year = static::getTahunFilter();
                $month_year = strlen($month) == 1 ? $year . '-0' . $month : $year . '-' . $month;
                $data = Customers::select('customers.*')
                    ->leftJoin('payments', function ($join) use ($month, $year) {
                        $join->on('payments.customer_id', '=', 'customers.id')
                            ->where('payments.month', $month)
                            ->where('payments.year', $year);
                    })
                    ->where('customers.subscribe', '<=', $month_year);
                if ($type === 'paid') {
                    $data = $data->whereNotNull('payments.id');
                } else {
                    $data = $data->whereNull('payments.id');
                }
                $data = $data->get();
                return view('components.modal-report-payment', [
                    'customers' => $data,
                    'month_name' => $month_name,
                    'year' => $year,
                    'type' => $type,

                ]);
            })
            ->modalSubmitAction(false)
            ->closeModalByClickingAway(false);
    }
}
