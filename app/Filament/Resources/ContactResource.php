<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup= 'Configuración';

    protected static ?string $navigationLabel= 'Contactos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                ->schema([
                    Section::make()
                    ->schema([
                        Group::make()
                        ->schema ([
                            TextInput::make('nombre')
                                ->hintColor('primary')
                                ->maxLength(255)
                                ->required(),
                            Select::make('tipo')->label("Tipo")
                                ->required()
                                ->options([
                                    'SocialMedia' => 'Social Media',
                                    'WebSite' => 'Web Site',
                                    'Contact' => 'Contacto',
                                    'Address' => 'Dirección',
                                ])
                                ->default('SocialMedia')
                        ])->columns(2),
                        Group::make()
                        ->schema ([
                            FileUpload::make('icono')
                                ->required()
                                ->columnSpanFull()
                                ->directory('icons')
                                ->image()
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    '1:1',
                                ])
                                ->acceptedFileTypes(['image/jpg','image/png'])
                                ->getUploadedFileNameForStorageUsing(
                                    fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                        ->prepend('contact-'),
                                )
                        ])->columns(1)

                    ])->columns(1)
                ])->columnSpan('full'),

                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    // Columns
                    Split::make([
                        Grid::make(5)
                        ->schema([
                            ImageColumn::make('icono')
                            ->circular()->columnSpan(1),
                            Stack::make([
                                TextColumn::make('nombre')
                                    ->weight(FontWeight::Bold),
                                TextColumn::make('tipo'),
                            ])->columnSpan(4)
                        ])
                    ]),
            ]),



                //
            ])->contentGrid([
                'md' => 2,
                'xl' => 2,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
