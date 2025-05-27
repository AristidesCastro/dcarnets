<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstitutionResource\Pages;
use App\Filament\Resources\InstitutionResource\RelationManagers;
use App\Models\Institution;
use App\Models\User;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\Repeater;
use Filament\Support\Enums\Alignment;

class InstitutionResource extends Resource
{
    protected static ?string $model = Institution::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Institutos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('INFORMACIÓN')
                            ->icon('heroicon-m-shopping-bag')
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre')
                                    ->hintColor('primary')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('user_id')
                                    ->label('Usuario')
                                    ->required()
                                    ->options(User::all()->pluck('name', 'id'))
                                    ->searchable(),
                                FileUpload::make('logo')
                                    ->label('Logo')
                                    ->required()
                                    ->columnSpanFull()
                                    ->directory('institution')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['1:1'])
                                    ->acceptedFileTypes(['image/jpg', 'image/png'])
                                    ->getUploadedFileNameForStorageUsing(
                                        fn(TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                            ->prepend('logo-'),
                                    )
                            ]),

                    ])->columnSpan(1),
                Group::make()
                    ->schema([
                        Section::make('CONTACTOS')
                            ->icon('heroicon-m-shopping-bag')
                            ->schema([
                                Repeater::make('institutionsContacts')
                                    ->hiddenLabel()
                                    ->addActionLabel('Nuevo Contacto')
                                    ->addActionAlignment(Alignment::End)
                                    ->collapsible()
                                    ->relationship()
                                    ->schema([
                                        Select::make('contact_id')
                                            ->label('Tipo')
                                            ->required()
                                            ->options(Contact::pluck('nombre', 'id'))
                                            ->columnSpan(1),
                                        TextInput::make('informacion')
                                            ->label('Información')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                    ])->columns(3)->grid(1)
                                    ->itemLabel(fn(array $state): ?string => $state['contact_id'] ?? null),

                            ])->columns(1)
                    ])->columnSpan(2),
                //
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('logo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
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
            'index' => Pages\ListInstitutions::route('/'),
            'create' => Pages\CreateInstitution::route('/create'),
            'edit' => Pages\EditInstitution::route('/{record}/edit'),
        ];
    }
    
  
}
