<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignmentResource\Pages;
use App\Filament\Resources\AssignmentResource\RelationManagers;
use App\Models\Assignment;
use App\Models\Peopletype;
use App\Models\Institution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Stack;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup= 'Configuración';

    protected static ?string $navigationLabel= 'Asignaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('INFORMACIÓN')
                ->schema([
                    Group::make()
                    ->schema ([
                        Select::make('institution_id')
                            ->label('Instituto')
                            ->options(Institution::pluck('nombre', 'id'))
                            ->required()
                            ->columnSpan(2)
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('peopletype_id', null); // Deselecciona el campo peopletype_id
                            }),
                        Select::make('peopletype_id')
                            ->label('Tipo de Persona')
                            ->disabled(fn (Get $get) : bool => ! filled($get('institution_id')))
                            ->options(fn(Get $get) => Peopletype::where('institution_id', (int) $get('institution_id'))->pluck('nombre', 'id'))
                            ->columnSpan(2)
                            ->reactive()
                            ->required(),
                        TextInput::make('categoria')
                            ->label(fn (Get $get) : string => match (Peopletype::find($get('peopletype_id'))?->nombre) {
                                'Docente' => 'Especialidad',
                                'Estudiante' => 'Especialidad, Grado, Año o Periodo y Sección',
                                default => 'Cargo',
                            })
                            ->disabled(fn (Get $get) : bool => ! filled($get('peopletype_id')))
                            ->required()
                            ->columnSpan(2)
                            ->maxLength(100),
                        TextInput::make('dependencia')
                            ->label('Otro Campo')
                            ->disabled(fn (Get $get) : bool => ! filled($get('peopletype_id')))
                            ->columnSpan(2)
                            ->maxLength(100),
                    ])
                    ->columns(4),

                ])->columns(1)
            ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('peopletype.nombre')
                    ->label('Tipo de Pesona')
                    ->sortable(),
                TextColumn::make('categoria')
                    ->description(fn ($record): ?string => $record->dependencia ? (string) $record->dependencia : null),
                TextColumn::make('institution.nombre')
                    ->label('Instituto')
                    ->sortable(),
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
            'index' => Pages\ListAssignments::route('/'),
            'create' => Pages\CreateAssignment::route('/create'),
            'edit' => Pages\EditAssignment::route('/{record}/edit'),
        ];
    }
}
