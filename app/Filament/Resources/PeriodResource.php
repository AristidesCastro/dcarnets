<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeriodResource\Pages;
use App\Filament\Resources\PeriodResource\RelationManagers;
use App\Models\Period;
use App\Models\Institution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;

class PeriodResource extends Resource
{
    protected static ?string $model = Period::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup= 'Configuración';

    protected static ?string $navigationLabel= 'Periodos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                ->schema([
                    Section::make('INFORMACIÓN GENERAL')
                    ->schema([
                        Group::make()
                        ->schema ([
                            TextInput::make('periodo')
                            ->label('Periodo Escolar')
                            ->required()
                            ->columnSpan(1)
                            ->maxLength(30),
                            Select::make('institution_id')
                            ->label('Instituto')
                            ->columnSpan(2)
                            ->required()
                            ->options(institution::pluck('nombre', 'id'))

                        ])
                        ->columns(3),

                        Group::make()
                        ->schema ([
                            DatePicker::make('fecha_inicio')
                            ->label('Inicio del Periodo')
                            ->columnSpan("[1]")
                            ->required(),
                            DatePicker::make('fecha_fin')
                            ->label('Culminación del Periodo')
                            ->columnSpan(1)
                            ->required(),
                        ])->columns(2),

                    ])->columnSpan(4),
                    Section::make('ESTADOS')
                    ->schema([
                        Group::make()
                        ->schema ([
                            Toggle::make('actual')
                            ->label('Período Actual')
                            ->columnSpan("full")
                            ->default(true)
                            ->inline(false)
                            ->required(),
                            Toggle::make('activo')
                            ->label('Activo')
                            ->columnSpan("full")
                            ->default(true)
                            ->inline(false)
                            ->required(),

                        ])
                        ->columnSpan('full')
                        ->columns(2),



                    ])->columnSpan(1)
                ])->columns(5)
                ->columnSpan('full'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('periodo')
                    ->label('Período Académico')
                    ->color(fn ($record): ? string => $record->actual > 0 ? 'success' : '')
                    ->icon(fn ($record): ? string => $record->actual > 0 ? 'heroicon-m-check' : '')
                    ->iconPosition(IconPosition::After)
                    ->iconColor('success')
                    ->searchable(),
                TextColumn::make('fecha_inicio')
                    ->date('m/d/Y')

                    ->label('Inicio'),
                TextColumn::make('fecha_fin')
                    ->date('m/d/Y')
                    ->label('Culminación'),
                TextColumn::make('institution.nombre')
                    ->sortable()
                    ->label('Instituto'),

                TextColumn::make('activo')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state > 0 ? 'ACTIVO' : 'INACTIVO')
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'danger',
                    }),
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
            'index' => Pages\ListPeriods::route('/'),
            'create' => Pages\CreatePeriod::route('/create'),
            'edit' => Pages\EditPeriod::route('/{record}/edit'),
        ];
    }
}
