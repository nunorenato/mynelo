<?php

namespace App\Filament\Resources;

use App\Enums\StatusEnum;
use App\Filament\Resources\BoatRegistrationResource\Pages;
use App\Filament\Resources\BoatRegistrationResource\RelationManagers;
use App\Http\Controllers\BoatRegistrationController;
use App\Models\BoatRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BoatRegistrationResource extends Resource
{
    protected static ?string $model = BoatRegistration::class;

    protected static ?string $navigationIcon = 'tabler-registered';
    protected static ?int $navigationSort = 10;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', StatusEnum::PENDING)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('boat_id')
                    ->relationship('boat', 'external_id')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('seller')
                    ->maxLength(254)
                    ->default(null),
                Forms\Components\Select::make('seat_id')
                    ->relationship('seat', 'name')
                    ->default(null),
                Forms\Components\TextInput::make('seat_position')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('seat_height')
                    ->numeric()
                    ->default(null),
                Forms\Components\Select::make('footrest_id')
                    ->relationship('footrest', 'name')
                    ->default(null),
                Forms\Components\TextInput::make('footrest_position')
                    ->numeric()
                    ->default(null),
                Forms\Components\Select::make('rudder_id')
                    ->relationship('rudder', 'name')
                    ->default(null),
                Forms\Components\TextInput::make('paddle')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('paddle_length')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options(StatusEnum::array()),
                Forms\Components\TextInput::make('hash')
                    ->maxLength(100)
                    ->default(null),
                Forms\Components\TextInput::make('voucher')
                    ->maxLength(50)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        //dump($table);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('boat.external_id')
                    ->label('Boat ID')
                    ->sortable()
                    ->searchable()
                    ->url(function($record){
                        return BoatResource::getUrl('edit', [
                            'record' => $record->boat
                        ]);
                    }),
                Tables\Columns\TextColumn::make('boat.model')
                    ->sortable()
                    ->searchable()
                    ->url(function($record){
                        return ProductResource::getUrl('edit', [
                            'record' => $record->boat->product
                        ]);
                    }),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->url(function($record){
                        return UserResource::getUrl('edit', [
                            'record' => $record->user
                        ]);
                    })
                    ->visible($table->getQueryStringIdentifier() != 'boatsRelationManager'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (StatusEnum $state): string => Str::replace('badge-', '', $state->cssClass()))
                    ->searchable(),
                Tables\Columns\TextColumn::make('voucher')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('status')
                    ->query(fn (Builder $query): Builder => $query->where('status', StatusEnum::PENDING))
                    ->label('Only pending'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('validate')
                        ->action(function (BoatRegistration $registration) {
                            $brc = new BoatRegistrationController();
                            $brc->validateRegistration($registration, $registration->hash);
                        })
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record) => $record->status == StatusEnum::PENDING),
                    Tables\Actions\Action::make('cancel')
                        ->action(function (BoatRegistration $registration) {
                            $brc = new BoatRegistrationController();
                            $brc->cancelRegistration($registration, $registration->hash);
                        } )
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->visible(fn ($record) => $record->status == StatusEnum::PENDING),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->button()
                    ->label('Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ])
            ])
            ->defaultSort('created_at', 'desc')
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent);
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
            'index' => Pages\ListBoatRegistrations::route('/'),
            'create' => Pages\CreateBoatRegistration::route('/create'),
            'edit' => Pages\EditBoatRegistration::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
