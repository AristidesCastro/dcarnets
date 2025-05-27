<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeopleResource\Pages;
use App\Filament\Resources\PeopleResource\RelationManagers;
use App\Models\Institution;
use App\Models\People;
use App\Models\Peopletype;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;


class PeopleResource extends Resource
{
    protected static ?string $model = People::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $user = Auth::user(); // Obtener el usuario autenticado
        $isSuperAdmin = $user->hasRole('super_admin'); // Verificar si es super_admin

        // Obtener el ID de la institución del usuario si NO es super_admin
        // Asegúrate que tu modelo User tenga una columna 'institution_id'
        // o una relación 'institution' para obtener el id.
        // Ajusta 'institution_id' si el campo se llama diferente en tu modelo User.
        // $userInstitutionId = !$isSuperAdmin ? $user->institution_id : null;
        $userInstitutionId = !$isSuperAdmin ? Institution::where('user_id', $user->id)->first()?->id : null;

        return $form
            ->schema([
                Section::make('INFORMACIÓN')
                ->schema([
                    Group::make()
                    ->schema ([

                        TextInput::make('cedula')
                            ->required()
                            ->maxLength(30)
                            ->columnSpan(2),
                        DatePicker::make('fecha_nacimiento')
                            ->label('Fecha de Nacimiento')
                            ->maxDate(now())
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('nombres')
                            ->required()
                            ->maxLength(150)
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('apellidos')
                            ->required()
                            ->maxLength(150)
                            ->columnSpan(2),
                        Select::make('institution_id')
                            ->label('Instituto')
                            // Cargar opciones solo si es super_admin
                            ->options(fn (): array => $isSuperAdmin ? Institution::pluck('nombre', 'id')->toArray() : [])
                            // Requerido solo si es super_admin (para el rol Instituto se asigna automáticamente)
                            ->required($isSuperAdmin)
                            ->columnSpan(2)
                            // Reactivo solo si es super_admin, para que actualice peopletype_id
                            ->reactive($isSuperAdmin)
                            // Ocultar si NO es super_admin
                            ->hidden(!$isSuperAdmin)
                            // Deshabilitar si NO es super_admin (aunque esté oculto, es buena práctica)
                            ->disabled(!$isSuperAdmin)
                            // Limpiar peopletype_id solo si es super_admin y cambia la institución
                            ->afterStateUpdated(function (Set $set, $state) use ($isSuperAdmin) {
                                if ($isSuperAdmin) {
                                     $set('peopletype_id', null);
                                }
                            }),
                            // Establecer valor por defecto si es rol Instituto y está creando
                            // Usaremos mutateFormDataBeforeCreate para más seguridad
                            // ->default($userInstitutionId) // Opcional: puede ayudar en algunos casos

                            // NOTA: Si usas ->hidden(), el valor podría no enviarse.
                            // Es más seguro usar mutateFormDataBeforeCreate/Save abajo.

                        // --- Campo People Type ---
                        Select::make('peopletype_id')
                        ->label(function (Get $get) use ($isSuperAdmin, $userInstitutionId): string {
                            $baseLabel = 'Tipo de Personas';

                            // Determinar el ID de la institución relevante (misma lógica que en options/disabled)
                            $institutionIdToFilter = $isSuperAdmin
                                ? $get('institution_id') // Obtener del campo si es super_admin
                                : $userInstitutionId;     // Usar el del usuario si es rol Instituto

                            // Si tenemos un ID, intentamos obtener el nombre de la institución
                            if ($institutionIdToFilter) {
                                // Buscamos la institución en la base de datos
                                // NOTA: Esto añade una consulta extra al renderizar el formulario
                                $institution = Institution::find($institutionIdToFilter);

                                if ($institution) {
                                    // Si encontramos la institución, añadimos su nombre al label
                                    return $baseLabel . ' para: ' . $institution->nombre;
                                } else {
                                    // Si no la encontramos (poco probable pero posible), mostramos el ID como fallback
                                    return $baseLabel . ' (Institución ID: ' . $institutionIdToFilter . ')';
                                }
                            }

                            // Si no hay institutionIdToFilter (ej. super_admin aún no selecciona), mostramos el label base
                            return $baseLabel ;
                        })
                         // Deshabilitar si la institution_id relevante no está definida.
                             // Para super_admin, depende del campo 'institution_id'.
                             // Para Instituto, su $userInstitutionId siempre está definido, así que nunca se deshabilita.
                            ->disabled(function (Get $get) use ($isSuperAdmin, $userInstitutionId): bool {
                                if (!$isSuperAdmin) {
                                    return false; // El rol Instituto siempre tiene institución, nunca deshabilitar.
                                }
                                // Lógica original para super_admin
                                return !filled($get('institution_id'));
                            })
                            ->options(function (Get $get) use ($isSuperAdmin, $userInstitutionId): array {
                                // Determinar qué ID de institución usar para la consulta
                                $institutionIdToFilter = $isSuperAdmin
                                    ? $get('institution_id') // Obtener del campo si es super_admin
                                    : $userInstitutionId;     // Usar el del usuario si es rol Instituto

                                // Si no hay un ID de institución (ej. super_admin no ha seleccionado aún), retornar vacío
                                if (!$institutionIdToFilter) {
                                    return [];
                                }

                                // Consultar los tipos de persona filtrando por el ID de institución determinado
                                return Peopletype::where('institution_id', (int) $institutionIdToFilter)
                                                 ->pluck('nombre', 'id')
                                                 ->toArray();
                            })
                            ->columnSpan(2)
                            // Reactivo: Necesario para super_admin, no hace daño para Instituto.
                            ->reactive()
                            ->required(),
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
                Tables\Columns\TextColumn::make('nombres')
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellidos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cedula')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_nacimiento')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('institution_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('peopletype_id')
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
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePeople::route('/create'),
            'edit' => Pages\EditPeople::route('/{record}/edit'),
        ];
    }
}
