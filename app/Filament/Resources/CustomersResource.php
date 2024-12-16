<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomersResource\Pages;
use App\Filament\Resources\CustomersResource\RelationManagers;
use App\Models\Customers;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomersResource extends Resource
{
    protected static ?string $model = Customers::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Menu';
    protected static ?string $navigationLabel = 'Data Pelanggan';
    protected static ?string $label = 'Data Pelanggan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Nama Pelanggan'),
                TextInput::make('phone')
                    ->required()
                    ->tel()
                    ->label('No Telepon/WA'),
                Select::make('bandwidth')
                    ->options([
                        '5 Mbps' => '5 Mbps',
                        '7 Mbps' => '7 Mbps',
                        '10 Mbps' => '10 Mbps',
                        '14 Mbps' => '14 Mbps',
                        '15 Mbps' => '15 Mbps',
                        '20 Mbps' => '20 Mbps',
                        '150 Mbps' => '150 Mbps',
                    ])
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->label('Biaya'),
                TextInput::make('ip')
                    ->ipv4()
                    ->required()
                    ->label('IP'),
                TextInput::make('subscribe')
                    ->type('month')
                    ->required()
                    ->label('Mulai Berlangganan'),
                TextInput::make('sub_district')
                    ->required()
                    ->label('Kecamatan'),
                TextInput::make('urban_village')
                    ->required()
                    ->label('Kelurahan')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Pelanggan'),
                TextColumn::make('phone')->label('No. HP/WA'),
                TextColumn::make('bandwidth')->label('Bandwidth'),
                TextColumn::make('price')->money('IDR', locale: 'id')->label('Biaya'),
                TextColumn::make('ip')->label('IP'),
                TextColumn::make('subscribe')
                    ->date('F Y')->label('Mulai Berlangganan'),
                TextColumn::make('sub_district')->label('Kecamatan'),
                TextColumn::make('urban_village')->label('Kelurahan'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->hiddenLabel(),
                Tables\Actions\DeleteAction::make()->hiddenLabel(),
            ], ActionsPosition::BeforeCells)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListCustomers::route('/'),
            // 'create' => Pages\CreateCustomers::route('/create'),
            // 'edit' => Pages\EditCustomers::route('/{record}/edit'),
        ];
    }
}
