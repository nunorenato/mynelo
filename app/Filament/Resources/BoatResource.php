<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BoatResource\Pages;
use App\Filament\Resources\BoatResource\RelationManagers;
use App\Models\Boat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BoatResource extends Resource
{
    protected static ?string $model = Boat::class;

    protected static ?string $navigationIcon = 'tabler-kayak';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('model')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('finished_at'),
                Forms\Components\TextInput::make('finished_weight')
                    ->numeric()
                    ->default(null),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->default(null),
                Forms\Components\TextInput::make('ideal_weight')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('external_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\Select::make('painter_id')
                    ->relationship('painter', 'name')
                    ->default(null),
                Forms\Components\Select::make('layuper_id')
                    ->relationship('layuper', 'name')
                    ->default(null),
                Forms\Components\Select::make('montador_id')
                    ->relationship('assembler', 'name')
                    ->default(null),
                Forms\Components\Select::make('evaluator_id')
                    ->relationship('evaluator', 'name')
                    ->default(null),
                Forms\Components\TextInput::make('co2')
                    ->numeric()
                    ->default(null),
                Forms\Components\Toggle::make('voucher_used')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('external_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('finished_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('finished_weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('voucher_used')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
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
            'index' => Pages\ListBoats::route('/'),
            'create' => Pages\CreateBoat::route('/create'),
            'edit' => Pages\EditBoat::route('/{record}/edit'),
        ];
    }
}
