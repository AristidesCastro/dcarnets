<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeopletypeResource\Pages;
use App\Filament\Resources\PeopletypeResource\RelationManagers;
use App\Models\Peopletype;
use App\Models\Institution;
use App\Models\Group as GroupModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class PeopletypeResource extends Resource
{
    protected static ?string $model = Peopletype::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup= 'Configuración';

    protected static ?string $navigationLabel= 'Tipo Personas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('INFORMACIÓN')
                    ->schema([
                        Forms\Components\Group::make()
                        ->schema ([
                            TextInput::make('nombre')
                                ->label('Nombre')
                                ->required()
                                ->columnSpan(2)
                                ->maxLength(50),
                            Select::make('group_id')
                                ->label('Grupo')
                                ->columnSpan(1)
                                ->required()
                                ->options(GroupModel::pluck('nombre', 'id')),
                            Select::make('institution_id')
                                ->label('Instituto')
                                ->columnSpan('full')
                                ->required()
                                ->options(institution::pluck('nombre', 'id'))

                        ])
                        ->columns(3),

                    ])->columns(1)
                ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->label('Nombre'),
                TextColumn::make('Group.nombre')
                    ->sortable()
                    ->label('Grupo'),
                TextColumn::make('institution.nombre')
                    ->sortable()
                    ->label('Instituto'),
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
            'index' => Pages\ListPeopletypes::route('/'),
            'create' => Pages\CreatePeopletype::route('/create'),
            'edit' => Pages\EditPeopletype::route('/{record}/edit'),
        ];
    }
}
