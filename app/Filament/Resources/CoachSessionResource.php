<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoachSessionResource\Pages;
use App\Filament\Resources\CoachSessionResource\RelationManagers;
use App\Models\Coach\Session;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoachSessionResource extends Resource
{
    protected static ?string $model = Session::class;

    protected static ?string $navigationIcon = 'heroicon-o-play';
    protected static ?int $navigationSort = 25;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('createdon')
                ->dateTime()
                ->label('Date')
                ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Update stats')
                    ->action(fn (Session $record) => $record->updateStats())
                    ->color('info')
                    ->icon('heroicon-o-cpu-chip'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('createdon', 'desc');
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
            'index' => Pages\ListCoachSessions::route('/'),
            'create' => Pages\CreateCoachSession::route('/create'),
            'edit' => Pages\EditCoachSession::route('/{record}/edit'),
        ];
    }
}
