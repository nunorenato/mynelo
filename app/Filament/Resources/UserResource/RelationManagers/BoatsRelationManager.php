<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\StatusEnum;
use App\Filament\Resources\BoatRegistrationResource;
use App\Http\Controllers\BoatRegistrationController;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BoatsRelationManager extends RelationManager
{
    protected static string $relationship = 'boats';

    public function form(Form $form): Form
    {
        return BoatRegistrationResource::form($form);
    }

    public function table(Table $table): Table
    {
        return BoatRegistrationResource::table($table);
    }
       /* return $table
            ->recordTitleAttribute('boat.external_id')
            ->columns([
                Tables\Columns\TextColumn::make('boat.external_id'),
                TextColumn::make('boat.model')
                    ->label('Model'),
                Tables\Columns\SelectColumn::make('status')
                    ->options(StatusEnum::array())
                    ->beforeStateUpdated(function ($record, $state) {
                        $brc = new BoatRegistrationController();
                        if($state == StatusEnum::VALIDATED)
                            $brc->validateRegistration($record, $record->hash);
                        elseif($state == StatusEnum::CANCELED)
                            $brc->cancelRegistration($record, $record->hash);
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }*/
}
