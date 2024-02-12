<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Occurence;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Webbingbrasil\FilamentDateFilter\DateFilter;

class ReportResource extends Resource
{
    protected static ?string $model = Occurence::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $slug = 'general-report';

    protected static ?string $navigationLabel = 'Site Reports';

    protected static ?string $navigationGroup = 'Reports';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('occurence')
                    ->rows(2)
                    ->disabled(),
                TextInput::make('department')
                    ->disabled()
                    ->reactive(),
                Select::make('pushed_to')
                    ->label('Select user to receive to')
                    ->options(User::role('manager')->pluck('name', 'id'))
                    ->required(),
                Textarea::make('om_comment')
                    ->label('Comment')
                    ->rows(2),
                FileUpload::make('evidence')->multiple()
                    ->maxFiles(5)
                    ->label('Evidence')
                    ->enableDownload()
                    ->disabled()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()->searchable()->sortable(),
                TextColumn::make('user.site')
                    ->sortable()->searchable()->label('Site'),
                TextColumn::make('department')
                    ->sortable()->searchable()->label('Dept'),
                TextColumn::make('user.name')
                    ->sortable()->label('Recorded By'),
                BadgeColumn::make('type')
                    ->sortable()->label('Occ Type')
                    ->colors([
                        'warning' => 'General',
                        'success' => 'Security',
                        'danger' => 'Medical',
                    ]),
                TextColumn::make('reporter.name')
                    ->sortable()->searchable()->label('Reporter'),
                TextColumn::make('reporter.job_title')
                    ->sortable()->searchable()->label('Title'),
                TextColumn::make('occurence')
                    ->sortable()->searchable()->wrap(),
                TextColumn::make('compartment.compartment_name')
                    ->sortable()->label('Compartment'),
                TextColumn::make('occurence_scene')
                    ->sortable()->searchable(),
                TextColumn::make('kits.first_kit_name')->label('First Aid Kit')
                    ->sortable()->searchable(),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'General' => 'General',
                        'Security' => 'Security',
                        'Medical' => 'Medical',
                    ]),
                DateFilter::make('created_at')
                    ->label(__('Created At'))
                    ->minDate(Carbon::today()->subMonth(1))
                    ->maxDate(Carbon::today()->addMonth(1))
                    ->timeZone('Africa/Nairobi')
                    ->range()
                    ->fromLabel(__('From'))
                    ->untilLabel(__('Until'))
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('push')
                    ->visible(auth()->user()->hasRole('sm'))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['status'] = 'pushed';
                        return $data;
                    })
                    ->successNotificationMessage('Occurence Pushed'),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
                BulkAction::make('mark as read')
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'closed']))
                    ->deselectRecordsAfterCompletion()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReports::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('op')) {
            return parent::getEloquentQuery()->where('status', 'pending');
        }
        if (auth()->user()->hasRole('sm')) {
            return parent::getEloquentQuery()
                ->where('status', 'pending')
                ->where('type', '!=', 'Medical')
                ->WhereHas('user', function ($query) {
                    $query->where('site', auth()->user()->site);
                });
        } else {
            return parent::getEloquentQuery()
                ->where('status', 'pending')
                ->where('user_id', auth()->id())->orderBy('created_at', 'Desc');
        }
    }
}
