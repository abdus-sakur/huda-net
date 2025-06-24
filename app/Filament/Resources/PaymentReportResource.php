<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Livewire\Livewire;
use App\Models\Payments;
use Filament\Forms\Form;
use App\Models\Customers;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PaymentReportResource\Pages;
use App\Filament\Resources\PaymentReportResource\RelationManagers;

class PaymentReportResource extends Resource
{
    protected static ?string $model = Payments::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Menu';
    protected static ?string $navigationLabel = 'Report Pembayaran';
    protected static ?string $label = 'Report Pembayaran';
    protected static ?int $navigationSort = 99;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('month'),
                TextColumn::make('year'),
                TextColumn::make('total_customers')
                    ->label('Total Pelanggan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('paid_customers')
                    ->label('Pelanggan Bayar')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('unpaid_customers')
                    ->label('Pelanggan Belum Bayar')
                    ->sortable(),
                TextColumn::make('total_paid')
                    ->label('Total Bayar')
                    ->sortable()
                    ->searchable()
                    ->money('idr'),
                TextColumn::make('total_unpaid')
                    ->label('Total Belum Bayar')
                    ->sortable()
                    ->searchable()
                    ->money('idr')
            ])->filters([
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
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->filters([
                "WITH RECURSIVE months AS (
  SELECT 1 AS month, '2024' AS year
  UNION ALL
  SELECT month + 1, '2024' FROM months WHERE month < 12
),
customer_months AS (
  SELECT 
    c.id AS customer_id,
    c.price,
    m.month,
    m.year,
    c.subscribe
  FROM customers c
  CROSS JOIN months m
  WHERE c.subscribe <= CONCAT(m.year, '-', LPAD(m.month, 2, '0')) 
),
payments_check AS (
  SELECT
    cm.customer_id,
    cm.month,
    cm.year,
    CAST(cm.price AS DECIMAL(12, 2)) AS price,
    CASE 
      WHEN p.id IS NULL THEN 0 ELSE 1
    END AS has_paid
  FROM customer_months cm
  LEFT JOIN payments p
    ON p.customer_id = cm.customer_id
    AND p.month = cm.month
    AND p.year = cm.year
)
SELECT 
  month,
  year,
  COUNT(*) AS total_customers,
  SUM(has_paid) AS paid_customers,
  COUNT(*) - SUM(has_paid) AS unpaid_customers,
  SUM(CASE WHEN has_paid = 1 THEN price ELSE 0 END) AS total_paid,
  SUM(CASE WHEN has_paid = 0 THEN price ELSE 0 END) AS total_unpaid
FROM payments_check
GROUP BY year, month
ORDER BY year, month;
"
            ])
            ->paginated(false)
            ->recordAction(null);
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
            'index' => Pages\ListPaymentReports::route('/'),
            // 'create' => Pages\CreatePaymentReport::route('/create'),
            // 'edit' => Pages\EditPaymentReport::route('/{record}/edit'),
        ];
    }
}
