<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Manage Users';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name'),
                TextInput::make('job_title')
                    ->label('Job Title'),
                TextInput::make('email')
                    ->label('Email')->disabled(),
                Select::make('site')
                    ->label('Site')
                    ->options([
                        'Head Office' => 'Head Office',
                        'Nyongoro' => 'Nyongoro',
                        'Kiambere' => 'Kiambere',
                        'Dokolo' => 'Dokolo',
                        '7 Forks' => '7 Forks'
                    ])
                    ->required(),
                Select::make('department')
                    ->label('Department')
                    ->options([
                        'Forestry' => 'Forestry',
                        'Operations' => 'Operations',
                        'IT' => 'IT',
                        'Communication' => 'Communication',
                        'Human Resources' => 'Human Resources',
                        'Finance and Accounts' => 'Finance and Accounts',
                        'Top Management' => 'Top Management',
                    ])
                    ->required(),
                MultiSelect::make('role.name')
                    ->relationship('roles','name')->label('Has Roles'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()->label('Name'),
                TextColumn::make('email')
                    ->sortable()->label('Email'),
                TextColumn::make('site')
                    ->sortable()->label('Site'),
                TextColumn::make('department')
                    ->sortable()->label('Department'),
                SelectColumn::make('status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),
                TextColumn::make('roles.name')
                    ->sortable()->label('Roles'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

}
