<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OccurenceResource\Pages;
use App\Filament\Resources\OccurenceResource\RelationManagers;
use App\Models\Compartment;
use App\Models\FirstAid;
use App\Models\Occurence;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Closure;
use Filament\Forms\Components\MultiSelect;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;


class OccurenceResource extends Resource
{
    protected static ?string $model = Occurence::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-list';

    protected static ?string $navigationLabel = 'My Occurences';





    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->label('Occurence type')
                    ->options([
                        'Security' => 'Security',
                        'General' => 'General',
                        'Medical' => 'Medical',
                    ])
                    ->required()
                    ->reactive(),

                Select::make('security_id')
                    ->label('Reported By')
                    ->options(User::where('site', auth()->user()->site)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('compartment_id')
                    ->label('Compartment')
                    ->options(Compartment::where(['site' => auth()->user()->site])->pluck('compartment_name', 'id'))
                    ->searchable()
                    ->required(fn (Closure $get) => $get('type') == 'Security')
                    ->hidden(fn (Closure $get) => $get('type') !== 'Security'),

                Select::make('occurence_scene')
                    ->label('Scene of occurrence')
                    ->options([
                        'At Work' => 'At work',
                        'On the way to work' => 'On the way to work',
                        'At Home' => 'At Home',
                    ])
                    ->required(fn (Closure $get) => $get('type') == 'Security')
                    ->hidden(fn (Closure $get) => $get('type') == 'Security'),

                MultiSelect::make('first_aid_item')
                    ->label('First Aid Item Used')
                    ->relationship('kits', 'first_kit_name')
                    ->searchable()
                    ->required(fn (Closure $get) => $get('type') == 'Medical')
                    ->hidden(fn (Closure $get) => $get('type') !== 'Medical'),

                Textarea::make('occurence')
                    ->required(),

                FileUpload::make('evidence')->multiple()
                    ->maxFiles(5)
                    ->label('Evidence')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()->searchable()->sortable(),
                BadgeColumn::make('type')
                    ->sortable()->label('Type')
                    ->colors([
                        'warning' => 'General',
                        'success' => 'Security',
                        'danger' => 'Medical',
                    ]),
                TextColumn::make('reporter.name')
                    ->sortable()->searchable()->label('Reported By'),
                TextColumn::make('reporter.job_title')
                    ->sortable()->searchable()->label('Title'),
                TextColumn::make('occurence')
                    ->sortable()->searchable()->wrap(),
                TextColumn::make('compartment.compartment_name')
                    ->sortable()->label('Compartment'),

                TextColumn::make('occurence_scene')
                    ->sortable()->searchable(),
                TextColumn::make('kits.first_kit_name')->label('First Aid Kit'),
                TextColumn::make('om_comment'),
                TextColumn::make('hod_comment'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                //EditAction::make()->visible(fn (Occurence $record): bool => $record->status == 'pending'),
                //DeleteAction::make()->visible(fn (Occurence $record): bool => $record->status == 'pending'),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOccurences::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereBelongsTo(auth()->user())->orderBy('created_at', 'Desc');
    }
}
