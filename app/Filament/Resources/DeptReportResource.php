<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeptReportResource\Pages;
use App\Filament\Resources\DeptReportResource\RelationManagers;
use App\Models\DeptReport;
use App\Models\Occurence;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeptReportResource extends Resource
{
    protected static ?string $model = Occurence::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'department-report';

    protected static ?string $navigationLabel = 'Dept Reports';

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
                Textarea::make('hod_comment')
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
                TextColumn::make('hod_comment')->label('HOD/Manager Comment')
                    ->sortable()->searchable(),
                TextColumn::make('kits.first_kit_name')->label('First Aid Kit')
                    ->sortable()->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDeptReports::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('hod') && auth()->user()->hasRole('hr')) {
            return parent::getEloquentQuery()
                ->where('status', 'pushed')
                ->where('department', auth()->user()->department)
                ->orWhere('type', 'Medical');
        } elseif (auth()->user()->hasRole('hod')) {
            return parent::getEloquentQuery()
                ->where('status', 'pushed')
                ->where('department', auth()->user()->department);
        } else {
            return parent::getEloquentQuery()
                ->where('pushed_to', auth()->id())
                ->where('status', 'pushed')->orderBy('created_at', 'Desc');
        }
    }
}
