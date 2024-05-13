<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->default(173),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date_of_birth'),
                Forms\Components\TextInput::make('photo')
                    ->maxLength(255)
                    ->default(null),
                /*Forms\Components\FileUpload::make('photo')
                    ->disk('public')
                    ->directory('users')
                    ->avatar(),*/
                Forms\Components\TextInput::make('height')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('weight')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('gender'),
                Forms\Components\Toggle::make('competition')
                    ->required(),
                Forms\Components\Textarea::make('club')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('weekly_trainings')
                    ->numeric()
                    ->default(null),
                Forms\Components\Select::make('discipline_id')
                    ->relationship('discipline', 'name')
                    ->default(null),
                Forms\Components\TextInput::make('time_500')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('time_1000')
                    ->numeric()
                    ->default(null),
                Forms\Components\Toggle::make('alert_fill')
                    ->required(),
                Forms\Components\Select::make('roles')->multiple()->relationship('roles', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->getStateUsing(function(User $user){
                        if(empty($user->photo))
                            return null;
                        else
                            return Str::of($user->photo)->replace('/storage', '');
                    })
                ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country.name')
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
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
               /* Tables\Columns\TextColumn::make('height')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender'),*/
                Tables\Columns\IconColumn::make('competition')
                    ->boolean(),
               /* Tables\Columns\TextColumn::make('weekly_trainings')
                    ->numeric()
                    ->sortable(),*/
                Tables\Columns\TextColumn::make('discipline.name')
                    ->numeric()
                    ->sortable(),
             /*   Tables\Columns\TextColumn::make('time_500')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_1000')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('alert_fill')
                    ->boolean(),*/
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('activities')->url(fn ($record) => UserResource::getUrl('activities', ['record' => $record]))
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'activities' => Pages\ListUserActivities::route('/{record}/activities'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
