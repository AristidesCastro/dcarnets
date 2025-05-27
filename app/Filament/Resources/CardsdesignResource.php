<?php

namespace App\Filament\Resources;

// --- Core Filament ---
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder; // <-- Importar Builder

// --- Form Components ---

use Filament\Forms\Components\Actions as SectionActions;
// use Filament\Forms\Components\Actions\Action as SectionAction; // No longer used directly in a way that needs this alias here
use Filament\Forms\Components\Actions\Action as FormAction; // Alias para acciones de Formulario/Repeater
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Actions as FormComponentActions; // <-- Importar Actions component
use Filament\Forms\Components\Grid; // <-- Importar Grid
use Filament\Forms\Components\Group; // Ya estaba implícito, pero lo hacemos explícito para claridad
use Filament\Forms\Components\FileUpload;
// use Filament\Forms\Components\Group as FormGroup; // Redundant alias, Group is fine
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio; // <- Se mantiene para selección de imagen
use Filament\Forms\Components\Repeater; // <-- Importar Repeater
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle; // <-- Importar Toggle

// --- Table Components ---
use Filament\Tables;
// use Filament\Tables\Actions\ActionGroup as TableActionGroup; // Alias para Table Actions - Not used directly
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction; // Explicitly used
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction; // Explicitly used
use Filament\Tables\Columns\TextColumn;

// --- Models ---
use App\Models\Cardsdesign;
use App\Models\Group as GroupModel; // Alias para evitar conflicto con Filament\Forms\Components\Group
use App\Models\Institution;
use App\Models\Peopletype;
use App\Models\Cardsasset; // Necesario para la galería/subida de imágenes
use App\Models\Cardselement; // <-- Importar el nuevo modelo

// --- Resource Pages ---
use App\Filament\Resources\CardsdesignResource\Pages;
// use Filament\Forms\Components\RichEditor; // <-- Mantenemos por si se usa en otro lado, aunque no en el modal - Not used here
use Filament\Notifications\Notification;
// --- Laravel & PHP ---
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Collection; // <-- Importar Collection


/**
 * Recurso para gestionar Diseños de Carnets.
 */
class CardsdesignResource extends Resource
{
    protected static ?string $model = Cardsdesign::class;
    protected static ?string $modelLabel = 'Diseño de Carnet';
    protected static ?string $pluralModelLabel = 'Diseños de Carnets';
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?int $navigationSort = 2;

    private const VIEWPORT_FIXED_RENDER_WIDTH = 400; // px
    private const VIEWPORT_FIXED_RENDER_HEIGHT = 400; // px
    private const FIT_SCALE_REDUCTION_FACTOR = 0.99; // Reducir escala de ajuste en 1%
    private const MIN_SCALE_FACTOR = 0.01; // Escala mínima del 1%

    /**
     * Define la estructura principal del formulario.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Diseño')
                    ->schema(self::getDesignInfoSchema())
                    ->columns(2)
                    ->columnSpanFull(),
                Tabs::make('Faces')
                    ->label('Diseño del Carnet')
                    ->tabs([
                        Tab::make('Anverso')
                            ->icon('heroicon-o-document')
                            ->schema(self::getFaceSchema(1)), // Schema para Cara 1
                        Tab::make('Reverso')
                            ->icon('heroicon-o-document-duplicate')
                            ->schema(self::getFaceSchema(2)) // Schema para Cara 2
                            ->visible(fn (Get $get): bool => $get('caras') === '02 Caras'),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(), // Mantiene la pestaña activa entre recargas
            ])
            ->columns(1);
    }

    /**
     * Devuelve el schema para una pestaña de cara (Preview + Repeater).
     */
    protected static function getFaceSchema(int $faceNumber): array
    {
        // Definir los nombres de los campos de depuración que los botones de zoom manipularán.
        // Estos nombres deben coincidir con cómo se definen en getFacePreviewPlaceholder.
        $var_Zoom_Scale_Identifier = "info_Zoom_Scale_{$faceNumber}";
        $debugOrientacionOverrideName_Identifier = "debug_orientacion_override_face_{$faceNumber}";
        $debugAnchoWorkspaceName_Identifier = "debug_ancho_workspace_face_{$faceNumber}";
        $debugAltoWorkspaceName_Identifier = "debug_alto_workspace_face_{$faceNumber}";
        $debugPosicionXOverrideName_Identifier = "debug_posicion_x_override_face_{$faceNumber}";
        $debugPosicionYOverrideName_Identifier = "debug_posicion_y_override_face_{$faceNumber}";


        return [
            Grid::make(5) // Crear una rejilla de 5 columnas
                ->schema([
                    Section::make('SectionCarnet_'.$faceNumber)
                        ->heading('Vista Previa del Carnet ')
                        ->headerActions([
                            FormAction::make("ZoomMenos_{$faceNumber}")
                                ->extraAttributes([
                                    'id' => 'Boton_ZoomMenos_'.$faceNumber,
                                ])
                                ->label(false)
                                ->tooltip('Alejar')
                                ->icon('heroicon-o-magnifying-glass-minus')
                                ->iconButton(true)
                                ->action(function (Get $get, Set $set) use (
                                    $var_Zoom_Scale_Identifier,
                                    $debugOrientacionOverrideName_Identifier,
                                    $debugAltoWorkspaceName_Identifier,
                                    $debugPosicionXOverrideName_Identifier,
                                    $debugPosicionYOverrideName_Identifier,
                                    $debugAnchoWorkspaceName_Identifier
                                ) {
                                    $currentScale = $get($var_Zoom_Scale_Identifier) !== null ? (float) $get($var_Zoom_Scale_Identifier) : 100.0;
                                    $decrementStep = 1; // Decrement by 1%
                                    $newScalePercentage = $currentScale - $decrementStep;
                                    $minScalePercentage = self::MIN_SCALE_FACTOR * 100;
                                    $newScalePercentage = max($newScalePercentage, $minScalePercentage);
                                    $set($var_Zoom_Scale_Identifier, (string)round($newScalePercentage));

                                    // Centrar después de cambiar la escala
                                    $orientacionOverrideValue = $get($debugOrientacionOverrideName_Identifier);
                                    $mainOrientacionValue = $get('../../orientacion') ?? 'Vertical';
                                    $currentPreviewOrientacion = !empty($orientacionOverrideValue) ? $orientacionOverrideValue : $mainOrientacionValue;
                                    $defaultHeightPx = ($currentPreviewOrientacion === 'Vertical' ? 1004 : 650);
                                    $altoWorkspaceValue = $get($debugAltoWorkspaceName_Identifier);
                                    $workspaceNativeHeight = (float)(!empty($altoWorkspaceValue) ? $altoWorkspaceValue : $defaultHeightPx);
                                    if ($workspaceNativeHeight <= 0) $workspaceNativeHeight = $defaultHeightPx;

                                    $newScaleFactor = round($newScalePercentage) / 100;
                                    if ($newScaleFactor <= 0 || $newScaleFactor > 1) $newScaleFactor = self::MIN_SCALE_FACTOR;

                                    $centeredPanY = (self::VIEWPORT_FIXED_RENDER_HEIGHT - ($workspaceNativeHeight * $newScaleFactor)) / 2;
                                    $set($debugPosicionYOverrideName_Identifier, (string)round($centeredPanY, 2));
                                    // --- Lógica para calcular y establecer $centeredPanX (Ancho) ---

                                    // Definir los valores predeterminados para el ancho basados en la orientación.
                                    // Ajusta estos valores (650 y 1004) según el ancho nativo de tu workspace
                                    // para la orientación 'Vertical' y 'Horizontal' respectivamente.
                                    $defaultWidthPx = ($currentPreviewOrientacion === 'Vertical' ? 650 : 1004);

                                    // Obtener el valor del ancho del workspace (si está sobreescrito).
                                    $anchoWorkspaceValue = $get($debugAnchoWorkspaceName_Identifier);
                                    $workspaceNativeWidth = (float)(!empty($anchoWorkspaceValue) ? $anchoWorkspaceValue : $defaultWidthPx);

                                    // Asegurar que el ancho nativo del workspace no sea cero o negativo para evitar divisiones por cero.
                                    if ($workspaceNativeWidth <= 0) {
                                        $workspaceNativeWidth = $defaultWidthPx;
                                    }

                                    // Calcular el factor de escala a partir del porcentaje.
                                    $newScaleFactor = round($newScalePercentage) / 100;
                                    // Asegurar que el factor de escala sea válido.
                                    if ($newScaleFactor <= 0 || $newScaleFactor > 1) {
                                        $newScaleFactor = self::MIN_SCALE_FACTOR;
                                    }

                                    // Calcular la posición central X.
                                    // self::VIEWPORT_FIXED_RENDER_WIDTH debe ser el ancho de tu área de visualización.
                                    $centeredPanX = (self::VIEWPORT_FIXED_RENDER_WIDTH - ($workspaceNativeWidth * $newScaleFactor)) / 2;

                                    // Establecer el valor calculado en el campo de posición X del formulario.
                                    $set($debugPosicionXOverrideName_Identifier, (string)round($centeredPanX, 2));
                                }),
                            FormAction::make('ZoomInfo_{$faceNumber}')
                                ->extraAttributes([
                                    'id' => 'Boton_ZoomInfo_'.$faceNumber,
                                ])
                                ->label(fn (Get $get): string => '' . ($get($var_Zoom_Scale_Identifier) ?? '100') . ' %')
                                ->badge()
                                ->tooltip('Establecer Zoom Manualmente')
                                ->modalHeading('Establecer Zoom Manual')
                                ->modalSubmitActionLabel('Aplicar Zoom')
                                ->modalWidth('xs') // <--- Aquí controlas el tamaño

                                ->fillForm(function (Get $get) use ( $var_Zoom_Scale_Identifier, $faceNumber): array {
                                    return [
                                        "new_zoom_value_{$faceNumber}" => $get($var_Zoom_Scale_Identifier), // Pasa el valor de texto1 al campo TextoModal
                                    ];
                                })
                                ->form([
                                    TextInput::make("new_zoom_value_{$faceNumber}")
                                        ->label('Porcentaje de Zoom')
                                        ->required()
                                        ->suffix('%')
                                        ->helperText('Ingrese un valor entre ' . (self::MIN_SCALE_FACTOR * 100) . ' y 100.')

                                ])

                                ->action(function (Get $get, Set $set, array $data) use (
                                    $var_Zoom_Scale_Identifier,
                                    $debugOrientacionOverrideName_Identifier,
                                    $debugAltoWorkspaceName_Identifier,
                                    $debugAnchoWorkspaceName_Identifier, // <-- Nueva variable para el ancho del workspace
                                    $debugPosicionXOverrideName_Identifier,
                                    $debugPosicionYOverrideName_Identifier,
                                    $faceNumber,
                                ) {
                                    // Obtenemos el nuevo valor de la escala del formulario del modal
                                    $newScalePercentage = (float) $data["new_zoom_value_{$faceNumber}"];
                                    // Establecemos el nuevo valor de la escala en el campo principal del formulario
                                    $set($var_Zoom_Scale_Identifier, (string)round($newScalePercentage));

                                    // --- Lógica para centrar Y (Altura) - NO CAMBIADA ---
                                    $orientacionOverrideValue = $get($debugOrientacionOverrideName_Identifier);
                                    $mainOrientacionValue = $get('../../orientacion') ?? 'Vertical';
                                    $currentPreviewOrientacion = !empty($orientacionOverrideValue) ? $orientacionOverrideValue : $mainOrientacionValue;
                                    $defaultHeightPx = ($currentPreviewOrientacion === 'Vertical' ? 1004 : 650); // Valores predeterminados para altura
                                    $altoWorkspaceValue = $get($debugAltoWorkspaceName_Identifier);
                                    $workspaceNativeHeight = (float)(!empty($altoWorkspaceValue) ? $altoWorkspaceValue : $defaultHeightPx);
                                    if ($workspaceNativeHeight <= 0) $workspaceNativeHeight = $defaultHeightPx;

                                    $newScaleFactor = round($newScalePercentage) / 100;
                                    if ($newScaleFactor <= 0 || $newScaleFactor > 1) $newScaleFactor = self::MIN_SCALE_FACTOR;

                                    // Cálculo de $centeredPanY (sin cambios)
                                    $centeredPanY = (self::VIEWPORT_FIXED_RENDER_HEIGHT - ($workspaceNativeHeight * $newScaleFactor)) / 2;
                                    $set($debugPosicionYOverrideName_Identifier, (string)round($centeredPanY, 2));

                                    // --- Lógica para centrar X (Ancho) - NUEVA ---
                                    // Definir los valores predeterminados para el ancho basados en la orientación
                                    // Aquí asumimos que si la orientación es 'Vertical', el ancho predeterminado es 650,
                                    // y si es 'Horizontal', es 1004. Esto es lo opuesto a la altura, lo cual es común.
                                    $defaultWidthPx = ($currentPreviewOrientacion === 'Vertical' ? 650 : 1004); // Valores predeterminados para ancho

                                    // Obtener el valor del ancho del workspace (si está overrideado)
                                    $anchoWorkspaceValue = $get($debugAnchoWorkspaceName_Identifier);
                                    $workspaceNativeWidth = (float)(!empty($anchoWorkspaceValue) ? $anchoWorkspaceValue : $defaultWidthPx);
                                    if ($workspaceNativeWidth <= 0) $workspaceNativeWidth = $defaultWidthPx; // Asegurar que no sea cero o negativo

                                    // Cálculo de $centeredPanX
                                    // Utilizamos self::VIEWPORT_FIXED_RENDER_WIDTH para el ancho de la vista
                                    $centeredPanX = (self::VIEWPORT_FIXED_RENDER_WIDTH - ($workspaceNativeWidth * $newScaleFactor)) / 2;
                                    // Establecemos el valor calculado en el campo de posición X
                                    $set($debugPosicionXOverrideName_Identifier, (string)round($centeredPanX, 2));

                                    // Opcional: Notificación para depuración o confirmación
                                    Notification::make()
                                        ->title('Escala y Posición Actualizadas')
                                        ->body("Escala: {$newScalePercentage}% | Posición X: " . round($centeredPanX, 2) . " | Posición Y: " . round($centeredPanY, 2))
                                        ->success()
                                        ->send();
                                }),
                            FormAction::make("ZoomMas_{$faceNumber}")
                                ->extraAttributes([
                                    'id' => 'Boton_ZoomMas_'.$faceNumber,
                                ])
                                ->label(false)
                                ->tooltip('Acercar')
                                ->icon('heroicon-o-magnifying-glass-plus')
                                ->iconButton(true)
                                ->action(function (Get $get, Set $set) use (
                                    $var_Zoom_Scale_Identifier,
                                    $debugOrientacionOverrideName_Identifier,
                                    $debugAltoWorkspaceName_Identifier,
                                    $debugAnchoWorkspaceName_Identifier, // <-- Nueva variable para el ancho del workspace
                                    $debugPosicionXOverrideName_Identifier,
                                    $debugPosicionYOverrideName_Identifier
                                ) {
                                    $currentScale = $get($var_Zoom_Scale_Identifier) !== null ? (float) $get($var_Zoom_Scale_Identifier) : 1.0;
                                    $incrementStep = 1; // Increment by 1%
                                    $newScalePercentage = $currentScale + $incrementStep;
                                    $maxScalePercentage = 100;
                                    $newScalePercentage = min($newScalePercentage, $maxScalePercentage);
                                    $minScalePercentage = self::MIN_SCALE_FACTOR * 100;
                                    $newScalePercentage = max($newScalePercentage, $minScalePercentage);
                                    $set($var_Zoom_Scale_Identifier, (string)round($newScalePercentage));

                                    // Centrar después de cambiar la escala
                                    $orientacionOverrideValue = $get($debugOrientacionOverrideName_Identifier);
                                    $mainOrientacionValue = $get('../../orientacion') ?? 'Vertical';
                                    $currentPreviewOrientacion = !empty($orientacionOverrideValue) ? $orientacionOverrideValue : $mainOrientacionValue;
                                    $defaultHeightPx = ($currentPreviewOrientacion === 'Vertical' ? 1004 : 650);
                                    $altoWorkspaceValue = $get($debugAltoWorkspaceName_Identifier);
                                    $workspaceNativeHeight = (float)(!empty($altoWorkspaceValue) ? $altoWorkspaceValue : $defaultHeightPx);
                                    if ($workspaceNativeHeight <= 0) $workspaceNativeHeight = $defaultHeightPx;

                                    $newScaleFactor = round($newScalePercentage) / 100;
                                    if ($newScaleFactor <= 0 || $newScaleFactor > 1) $newScaleFactor = self::MIN_SCALE_FACTOR;

                                    $set($debugPosicionXOverrideName_Identifier, '0.00');
                                    $centeredPanY = (self::VIEWPORT_FIXED_RENDER_HEIGHT - ($workspaceNativeHeight * $newScaleFactor)) / 2;
                                    $set($debugPosicionYOverrideName_Identifier, (string)round($centeredPanY, 2));

                                    // --- Lógica para calcular y establecer $centeredPanX (Ancho) ---

                                    // Definir los valores predeterminados para el ancho basados en la orientación.
                                    // Ajusta estos valores (650 y 1004) según el ancho nativo de tu workspace
                                    // para la orientación 'Vertical' y 'Horizontal' respectivamente.
                                    $defaultWidthPx = ($currentPreviewOrientacion === 'Vertical' ? 650 : 1004);

                                    // Obtener el valor del ancho del workspace (si está sobreescrito).
                                    $anchoWorkspaceValue = $get($debugAnchoWorkspaceName_Identifier);
                                    $workspaceNativeWidth = (float)(!empty($anchoWorkspaceValue) ? $anchoWorkspaceValue : $defaultWidthPx);

                                    // Asegurar que el ancho nativo del workspace no sea cero o negativo para evitar divisiones por cero.
                                    if ($workspaceNativeWidth <= 0) {
                                        $workspaceNativeWidth = $defaultWidthPx;
                                    }

                                    // Calcular el factor de escala a partir del porcentaje.
                                    $newScaleFactor = round($newScalePercentage) / 100;
                                    // Asegurar que el factor de escala sea válido.
                                    if ($newScaleFactor <= 0 || $newScaleFactor > 1) {
                                        $newScaleFactor = self::MIN_SCALE_FACTOR;
                                    }

                                    // Calcular la posición central X.
                                    // self::VIEWPORT_FIXED_RENDER_WIDTH debe ser el ancho de tu área de visualización.
                                    $centeredPanX = (self::VIEWPORT_FIXED_RENDER_WIDTH - ($workspaceNativeWidth * $newScaleFactor)) / 2;

                                    // Establecer el valor calculado en el campo de posición X del formulario.
                                    $set($debugPosicionXOverrideName_Identifier, (string)round($centeredPanX, 2));
                                }),
                            FormAction::make("ZoomFit_{$faceNumber}")
                                ->extraAttributes([
                                    'id' => 'Boton_ZoomFit_'.$faceNumber,
                                ])
                                ->label(false)
                                ->tooltip('Ajustar')
                                ->icon('heroicon-o-arrows-pointing-out')
                                ->iconButton(true)
                                ->action(function (Get $get, Set $set) use (
                                    $var_Zoom_Scale_Identifier,
                                    $debugOrientacionOverrideName_Identifier,
                                    $debugAltoWorkspaceName_Identifier,
                                    $debugPosicionXOverrideName_Identifier,
                                    $debugAnchoWorkspaceName_Identifier,
                                    $debugPosicionYOverrideName_Identifier
                                ) {
                                    $orientacionOverrideValue = $get($debugOrientacionOverrideName_Identifier);
                                    $mainOrientacionValue = $get('../../orientacion') ?? 'Vertical';
                                    $currentPreviewOrientacion = !empty($orientacionOverrideValue) ? $orientacionOverrideValue : $mainOrientacionValue;

                                    $defaultHeightPx = ($currentPreviewOrientacion === 'Vertical' ? 1004 : 650);
                                    $defaultWidthPx = ($currentPreviewOrientacion === 'Vertical' ? 650 : 1004);


                                    $altoWorkspaceValue = $get($debugAltoWorkspaceName_Identifier);
                                    $AnchoWorkspaceValue = $get($debugAnchoWorkspaceName_Identifier);
                                    $workspaceNativeHeight = (float)(!empty($altoWorkspaceValue) ? $altoWorkspaceValue : $defaultHeightPx);
                                    $workspaceNativeWidth = (float)(!empty($AnchoWorkspaceValue) ? $AnchoWorkspaceValue : $defaultWidthPx);
                                    if ($workspaceNativeHeight <= 0) $workspaceNativeHeight = $defaultHeightPx;
                                    if ($workspaceNativeWidth <= 0) $workspaceNativeWidth = $defaultWidthPx;

                                    $scaleToFitHeight = 1.0;
                                    if ($workspaceNativeHeight > 0 && self::VIEWPORT_FIXED_RENDER_HEIGHT > 0) {
                                        $scaleToFitHeight = self::VIEWPORT_FIXED_RENDER_HEIGHT / $workspaceNativeHeight;
                                    }

                                    $scaleToFitWidth = 1.0;
                                    if ($workspaceNativeWidth > 0 && self::VIEWPORT_FIXED_RENDER_WIDTH > 0) {
                                        $scaleToFitWidth = self::VIEWPORT_FIXED_RENDER_WIDTH / $workspaceNativeWidth;
                                    }

                                    if ($currentPreviewOrientacion == 'Horizontal'){
                                        $calculatedScale = min($scaleToFitWidth, 1.0) * self::FIT_SCALE_REDUCTION_FACTOR;
                                    }else{
                                        $calculatedScale = min($scaleToFitHeight, 1.0) * self::FIT_SCALE_REDUCTION_FACTOR;
                                    }

                                    $finalFitScaleFactor = max($calculatedScale, self::MIN_SCALE_FACTOR);
                                    $fitScalePercentage = round($finalFitScaleFactor * 100);
                                    $set($var_Zoom_Scale_Identifier, (string)$fitScalePercentage);

                                    // Centrar
                                    $centeredPanY = (self::VIEWPORT_FIXED_RENDER_HEIGHT - ($workspaceNativeHeight * $fitScalePercentage)) / 2;
                                    $set($debugPosicionYOverrideName_Identifier, (string)round($centeredPanY, 2));

                                    $centeredPanX = (self::VIEWPORT_FIXED_RENDER_WIDTH - ($workspaceNativeWidth * $fitScalePercentage)) / 2;
                                    $set($debugPosicionXOverrideName_Identifier, (string)round($centeredPanX, 2));

                                    $newScaleFactor = round($fitScalePercentage) / 100;
                                    if ($newScaleFactor <= 0 || $newScaleFactor > 1) $newScaleFactor = self::MIN_SCALE_FACTOR;

                                    $centeredPanY = (self::VIEWPORT_FIXED_RENDER_HEIGHT - ($workspaceNativeHeight * $newScaleFactor)) / 2;
                                    $set($debugPosicionYOverrideName_Identifier, (string)round($centeredPanY, 2));

                                    $centeredPanX = (self::VIEWPORT_FIXED_RENDER_WIDTH - ($workspaceNativeWidth * $newScaleFactor)) / 2;
                                    $set($debugPosicionXOverrideName_Identifier, (string)round($centeredPanX, 2));
// lISTO ESTA FUNCION SI ORGANIZA Y CENTRA, HAY QUE CONVERTIRLA EN UNA FUNCION
                                    Notification::make()
                                        ->title('Valor de Zoom Actual')
                                        ->body('El valor: ' . (string)($currentPreviewOrientacion ) . ' / '. (string)($fitScalePercentage) . ' / '. (string)($centeredPanX) . ' X/ '. (string)($centeredPanY) . ' Y/ ')
                                        ->success() // Opcional: puedes usar ->warning(), ->danger(), ->info()
                                        ->send();
                                })
                        ])
                        ->schema([
                            self::getFacePreviewPlaceholder($faceNumber),
                            SectionActions::make([])->alignEnd(),
                        ])
                        ->columnSpan(2),
                    Section::make('SectionElementos_'.$faceNumber)
                        ->heading('Elementos del Carnet ')
                        ->schema([
                            self::getRelationalElementsRepeater($faceNumber),
                        ])->columnSpan(3),
                ])
        ];
    }


    /**
     * Ayudante para los campos de información principal del diseño.
     */
    protected static function getDesignInfoSchema(): array
    {
        return [
            TextInput::make('nombre')->label('Nombre del Diseño')->required()->maxLength(150)->columnSpan(1),
            Select::make('institution_id')->label('Instituto')->required()
                ->relationship('institution', 'nombre')->searchable()->preload()
                ->reactive()->afterStateUpdated(fn (Set $set) => $set('group_id', null))
                ->columnSpan(1),
            Select::make('orientacion')->options(['Horizontal' => 'Horizontal', 'Vertical' => 'Vertical'])
                ->default('Vertical')->required()->live()
                ->columnSpan(1)
                ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                    if ($state) {



                        $newWorkspaceWidth = ($state === 'Vertical') ? 650 : 1004;
                        $newWorkspaceHeight = ($state === 'Vertical') ? 1004 : 650;

                        $scaleToFitHeight = 1.0;
                        if ($newWorkspaceHeight > 0 && self::VIEWPORT_FIXED_RENDER_HEIGHT > 0) {
                            $scaleToFitHeight = self::VIEWPORT_FIXED_RENDER_HEIGHT / $newWorkspaceHeight;
                        }

                        $scaleToFitHeight = 1.0;
                        if ($newWorkspaceWidth > 0 && self::VIEWPORT_FIXED_RENDER_WIDTH > 0) {
                            $scaleToFitWidth = self::VIEWPORT_FIXED_RENDER_WIDTH / $newWorkspaceHeight;
                        }
                        if ($state === 'Vertical'){
                            $calculatedScale = min($scaleToFitHeight, 1.0) * self::FIT_SCALE_REDUCTION_FACTOR;
                        }else{
                            $calculatedScale = min($scaleToFitWidth, 1.0) * self::FIT_SCALE_REDUCTION_FACTOR;

                        }
                        $calculatedScale = min($scaleToFitHeight, 1.0) * self::FIT_SCALE_REDUCTION_FACTOR;
                        $finalScaleFactor = max($calculatedScale, self::MIN_SCALE_FACTOR);
                        $scalePercentage = round($finalScaleFactor * 100, 2);

                        $centeredPanY = (self::VIEWPORT_FIXED_RENDER_HEIGHT - ($newWorkspaceHeight * $finalScaleFactor)) / 2;
                        $centeredPanYRounded = round($centeredPanY, 2);

                        $centeredPanX = (self::VIEWPORT_FIXED_RENDER_WIDTH - ($newWorkspaceWidth * $finalScaleFactor)) / 2;
                        $centeredPanXRounded = round($centeredPanX, 2);



                        // Aseguramos que los campos de depuración se actualicen siempre
                        // cuando la orientación principal cambie.
                        for ($i=1; $i <= 2; $i++) {
                            $set("debug_orientacion_override_face_{$i}", $state); // Esta línea es clave
                            $set("debug_ancho_workspace_face_{$i}", (string)$newWorkspaceWidth);
                            $set("debug_alto_workspace_face_{$i}", (string)$newWorkspaceHeight);
                            $set("debug_escala_override_face_{$i}", (string)$scalePercentage);
                            $set("debug_posicion_x_override_face_{$i}", $centeredPanX);
                            $set("debug_posicion_y_override_face_{$i}", (string)$centeredPanYRounded);
                        }
                    }
                }),
            Select::make('group_id')->label('Grupo Asociado')->required()
                ->options(function (Get $get): array {
                    $institutionId = $get('institution_id');
                    if (!$institutionId) return [];
                    $groupIds = Peopletype::where('institution_id', $institutionId)->distinct()->pluck('group_id');
                    return GroupModel::whereIn('id', $groupIds)->pluck('nombre', 'id')->toArray();
                })
                ->visible(fn (Get $get) => filled($get('institution_id')))
                ->searchable()->preload()->columnSpan(1),
            Select::make('caras')->options(['01 Cara' => '01 Cara', '02 Caras' => '02 Caras'])
                ->default('01 Cara')->required()->live()->columnSpan(1),
            Placeholder::make('design_info_spacer')->columnSpan(1),
        ];
    }

    /**
     * Actualiza la vista previa de una cara cuando cambia la orientación principal.
     */
    /**
     * Ayudante para el placeholder de la vista previa de la cara con zoom.
     */
    protected static function getFacePreviewPlaceholder(int $targetFace): Grid
    {
        $placeholderName = "interactive_preview_face_{$targetFace}";
        $viewportId = "viewport_{$targetFace}_" . Str::random(8);
        $workspaceId = "workspace_{$targetFace}_" . Str::random(8);

        $debugOrientacionOverrideName = "debug_orientacion_override_face_{$targetFace}";
        $debugAnchoWorkspaceName = "debug_ancho_workspace_face_{$targetFace}";
        $debugAltoWorkspaceName = "debug_alto_workspace_face_{$targetFace}";
        $var_Zoom_Scale = "info_Zoom_Scale_{$targetFace}";
        $debugPosicionXOverrideName = "debug_posicion_x_override_face_{$targetFace}";
        $debugPosicionYOverrideName = "debug_posicion_y_override_face_{$targetFace}";
        $debugAnchoPreviewName = "debug_ancho_preview_face_{$targetFace}";
        $debugAltoPreviewName = "debug_alto_preview_face_{$targetFace}";
        $debugRefreshTriggerName = "debug_refresh_trigger_face_{$targetFace}";

        return Grid::make(5)
            ->schema([
                Placeholder::make($placeholderName)
                    ->label(false)
                    ->content(function (Get $get, ?Cardsdesign $record) use (
                        $targetFace, $viewportId, $workspaceId,
                        $debugOrientacionOverrideName, $debugAnchoWorkspaceName, $debugAltoWorkspaceName,
                        $var_Zoom_Scale, $debugPosicionXOverrideName, $debugPosicionYOverrideName,
                        $debugRefreshTriggerName, $debugAnchoPreviewName, $debugAltoPreviewName // Añadido para JS
                    ): HtmlString {
                        $get($debugRefreshTriggerName);

                        $orientacionOverrideValue = $get($debugOrientacionOverrideName);
                        $mainOrientacionValue = $get('../../orientacion') ?? $record?->orientacion ?? 'Vertical';
                        $currentPreviewOrientacion = !empty($orientacionOverrideValue) ? $orientacionOverrideValue : $mainOrientacionValue;

                        $defaultWidthPx = ($currentPreviewOrientacion === 'Vertical' ? 650 : 1004);
                        $defaultHeightPx = ($currentPreviewOrientacion === 'Vertical' ? 1004 : 650);

                        $anchoWorkspaceValue = $get($debugAnchoWorkspaceName);
                        $workspaceNativeWidth = (float)(!empty($anchoWorkspaceValue) ? $anchoWorkspaceValue : $defaultWidthPx);
                        if ($workspaceNativeWidth <= 0) $workspaceNativeWidth = $defaultWidthPx;

                        $altoWorkspaceValue = $get($debugAltoWorkspaceName);
                        $workspaceNativeHeight = (float)(!empty($altoWorkspaceValue) ? $altoWorkspaceValue : $defaultHeightPx);
                        if ($workspaceNativeHeight <= 0) $workspaceNativeHeight = $defaultHeightPx;

                        $escalaOverrideValue = $get($var_Zoom_Scale);
                        $scaleFactor = 0;

                        if ($escalaOverrideValue !== null && (string)$escalaOverrideValue !== '') {
                            $chosenScalePercentage = (float) $escalaOverrideValue;
                        } else {
                            $scaleToFitHeight = 1.0;
                            if ($workspaceNativeHeight > 0 && self::VIEWPORT_FIXED_RENDER_HEIGHT > 0) {
                                $scaleToFitHeight = self::VIEWPORT_FIXED_RENDER_HEIGHT / $workspaceNativeHeight;
                            }
                            $calculatedScale = min($scaleToFitHeight, 1.0) * self::FIT_SCALE_REDUCTION_FACTOR;
                            $finalFitScale = max($calculatedScale, self::MIN_SCALE_FACTOR);
                            $chosenScalePercentage = round($finalFitScale * 100);
                        }
                        $scaleFactor = $chosenScalePercentage / 100;
                        if ($scaleFactor <= 0 || $scaleFactor > 1) $scaleFactor = self::MIN_SCALE_FACTOR;

                        $viewportFixedRenderHeight = self::VIEWPORT_FIXED_RENDER_HEIGHT;
                        $posicionXOverrideValue = $get($debugPosicionXOverrideName);
                        $initialPanX = ($posicionXOverrideValue !== null && (string)$posicionXOverrideValue !== '')
                            ? (float)$posicionXOverrideValue
                            : (self::VIEWPORT_FIXED_RENDER_WIDTH - ($workspaceNativeWidth * $scaleFactor)) / 2;
                        $posicionYOverrideValue = $get($debugPosicionYOverrideName);
                        $initialPanY = ($posicionYOverrideValue !== null && (string)$posicionYOverrideValue !== '')
                            ? (float)$posicionYOverrideValue
                            : ($viewportFixedRenderHeight - ($workspaceNativeHeight * $scaleFactor)) / 2;

                        $allElementsState = $get('cardselements') ?? [];
                        $faceElements = collect($allElementsState)
                            ->filter(fn ($item) => (int)($item['cara'] ?? 0) === $targetFace)
                            ->sortBy('z_index')
                            ->values()
                            ->all();

                        if (empty($faceElements) && $record) {
                             $faceElements = $record->cardselements()
                                                ->where('cara', $targetFace)
                                                ->orderBy('z_index')
                                                ->get()
                                                ->toArray();
                             $faceElements = array_map(fn ($element) => static::prepareElementForForm($element), $faceElements);
                        }

                        $renderedCanvasContent = self::renderPreviewFaceCanvas($faceElements, $targetFace, $workspaceNativeWidth, $workspaceNativeHeight);
                        $workspaceTransformStyle = "transform: translate({$initialPanX}px, {$initialPanY}px) scale({$scaleFactor});";
                        $viewportWidthStyle = "100%";
                        $viewportHeightStyle = "{$viewportFixedRenderHeight}px";

                        $html = <<<HTML
                        <div id="{$viewportId}" data-native-width="{$workspaceNativeWidth}" data-native-height="{$workspaceNativeHeight}" style="width: {$viewportWidthStyle}; height: {$viewportHeightStyle}; overflow: hidden; border: 1px solid #ccc; margin: 0 auto; position: relative; background-color: #e9e9e9;">
                            <div id="{$workspaceId}"
                                 style="width: {$workspaceNativeWidth}px;
                                        height: {$workspaceNativeHeight}px;
                                        transform-origin: 0 0;
                                        position: absolute;
                                        background-color: lightgreen; /* Mantenido como en el original */
                                        box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                        border: 1px solid #aaa;
                                        {$workspaceTransformStyle}"
                                 data-initial-pan-x="{$initialPanX}"
                                 data-initial-pan-y="{$initialPanY}">
                                {$renderedCanvasContent}
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                // Usamos un pequeño timeout para dar tiempo a Filament/Alpine a inicializar completamente los elementos.
                                setTimeout(function() {
                                    const viewportId = '{$viewportId}';
                                    const anchoInputId = '{$debugAnchoPreviewName}';
                                    const altoInputId = '{$debugAltoPreviewName}';

                                    const viewportEl = document.getElementById(viewportId);
                                    const anchoInputEl = document.getElementById(anchoInputId);
                                    const altoInputEl = document.getElementById(altoInputId);

                                    if (!viewportEl) {
                                        console.warn("CardsdesignResource: Viewport element '" + viewportId + "' not found for face {$targetFace}.");
                                    }
                                    if (!anchoInputEl) {
                                        console.warn("CardsdesignResource: Ancho input element '" + anchoInputId + "' not found for face {$targetFace}.");
                                    }
                                    if (!altoInputEl) {
                                        console.warn("CardsdesignResource: Alto input element '" + altoInputId + "' not found for face {$targetFace}.");
                                    }

                                    function updateViewportDimensionsDisplay_{$targetFace}() {
                                        if (viewportEl && anchoInputEl) {
                                            anchoInputEl.value = viewportEl.clientWidth;
                                        }
                                        if (viewportEl && altoInputEl) {
                                            altoInputEl.value = viewportEl.clientHeight;
                                        }
                                    }

                                    if (viewportEl) { // Solo proceder si el viewport existe
                                        updateViewportDimensionsDisplay_{$targetFace}(); // Actualización inicial

                                        if (typeof ResizeObserver !== 'undefined') {
                                            const resizeObserver = new ResizeObserver(updateViewportDimensionsDisplay_{$targetFace});
                                            resizeObserver.observe(viewportEl);
                                        } else {
                                            // Fallback si ResizeObserver no está disponible
                                            window.addEventListener('resize', updateViewportDimensionsDisplay_{$targetFace});
                                        }
                                    }
                                }, 100); // Un retardo de 100ms para ser un poco más seguro
                            });
                        </script>
                        HTML;
                        return new HtmlString($html);
                    })->columnSpanFull(),
                Section::make('Controles Internos del Preview (Debug)')
                    ->schema([
                        TextInput::make($debugOrientacionOverrideName)
                            ->id($debugOrientacionOverrideName)
                            ->label('Orientación')
                            ->afterStateHydrated(function (TextInput $component, $state, Get $get, Set $set): void {
                                $component->state( $get('orientacion') );
                            })
                            ->live(debounce: '500ms')
                            ->afterStateUpdated(function (Get $get, Set $set) use (
                                    $debugAnchoWorkspaceName,
                                    $debugAltoWorkspaceName,
                                    $var_Zoom_Scale,
                                    $debugPosicionXOverrideName,
                                    $debugPosicionYOverrideName,
                                    $debugOrientacionOverrideName,
                                ) {
                                    $orientacionOverrideValue = $get($debugOrientacionOverrideName);
                                    $mainOrientacionValue = $get('../../orientacion') ?? 'Vertical';
                                    $currentPreviewOrientacion = !empty($orientacionOverrideValue) ? $orientacionOverrideValue : $mainOrientacionValue;

                                    $defaultHeightPx = ($currentPreviewOrientacion === 'Vertical' ? 1004 : 650);
                                    $altoWorkspaceValue = $get($debugAltoWorkspaceName);
                                    $workspaceNativeHeight = (float)(!empty($altoWorkspaceValue) ? $altoWorkspaceValue : $defaultHeightPx);
                                    if ($workspaceNativeHeight <= 0) $workspaceNativeHeight = $defaultHeightPx;

                                    $scaleToFitHeight = 1.0;
                                    if ($workspaceNativeHeight > 0 && self::VIEWPORT_FIXED_RENDER_HEIGHT > 0) {
                                        $scaleToFitHeight = self::VIEWPORT_FIXED_RENDER_HEIGHT / $workspaceNativeHeight;
                                    }
                                    $calculatedScale = min($scaleToFitHeight, 1.0) * self::FIT_SCALE_REDUCTION_FACTOR;
                                    $finalFitScaleFactor = max($calculatedScale, self::MIN_SCALE_FACTOR);
                                    $fitScalePercentage = round($finalFitScaleFactor * 100);
                                    $set($var_Zoom_Scale, (string)$fitScalePercentage);

                                    // Centrar
                                    $centeredPanY = (self::VIEWPORT_FIXED_RENDER_HEIGHT - ($workspaceNativeHeight * $finalFitScaleFactor)) / 2;
                                    $set($debugPosicionYOverrideName, (string)round($centeredPanY, 2));

                                    // Definir los valores predeterminados para el ancho basados en la orientación.
                                    // Ajusta estos valores (650 y 1004) según el ancho nativo de tu workspace
                                    // para la orientación 'Vertical' y 'Horizontal' respectivamente.
                                    $defaultWidthPx = ($currentPreviewOrientacion === 'Vertical' ? 650 : 1004);

                                    // Obtener el valor del ancho del workspace (si está sobreescrito).
                                    $anchoWorkspaceValue = $get($debugAnchoWorkspaceName);
                                    $workspaceNativeWidth = (float)(!empty($anchoWorkspaceValue) ? $anchoWorkspaceValue : $defaultWidthPx);

                                    // Asegurar que el ancho nativo del workspace no sea cero o negativo para evitar divisiones por cero.
                                    if ($workspaceNativeWidth <= 0) {
                                        $workspaceNativeWidth = $defaultWidthPx;
                                    }

                                    // Calcular la posición central X.
                                    // self::VIEWPORT_FIXED_RENDER_WIDTH debe ser el ancho de tu área de visualización.
                                    $centeredPanX = (self::VIEWPORT_FIXED_RENDER_WIDTH - ($workspaceNativeWidth * $finalFitScaleFactor)) / 2;

                                    // Establecer el valor calculado en el campo de posición X del formulario.
                                    $set($debugPosicionXOverrideName, (string)round($centeredPanX, 2));
                                })

                            ->dehydrated(false),
                        TextInput::make($var_Zoom_Scale)
                            ->id($var_Zoom_Scale)
                            ->label('Escala Área Trabajo')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(100)
                            ->suffix('%')
                            ->afterStateHydrated(function (TextInput $component, Get $get) use ($debugAltoWorkspaceName, $debugOrientacionOverrideName) {
                                if ($component->getState() === null || $component->getState() === '') {
                                    $orientacionValue = $get($debugOrientacionOverrideName) ?: $get('../../orientacion') ?? 'Vertical';
                                    $workspaceHValue = $get($debugAltoWorkspaceName);
                                    $workspaceH = (float) (!empty($workspaceHValue) ? $workspaceHValue : ($orientacionValue === 'Vertical' ? 1004 : 650));
                                    $scaleToFitHeight = 1.0;
                                    if ($workspaceH > 0 && self::VIEWPORT_FIXED_RENDER_HEIGHT > 0) {
                                        $scaleToFitHeight = self::VIEWPORT_FIXED_RENDER_HEIGHT / $workspaceH;
                                    }
                                    $calculatedScale = min($scaleToFitHeight, 1.0) * self::FIT_SCALE_REDUCTION_FACTOR;
                                    $finalScale = max($calculatedScale, self::MIN_SCALE_FACTOR);
                                    $scalePercentage = round($finalScale * 100);
                                    $component->state((string)$scalePercentage);
                                }
                            })
                            ->dehydrated(false)
                            ->live(debounce: '500ms'),
                        TextInput::make($debugAnchoWorkspaceName)
                            ->id($debugAnchoWorkspaceName)
                            ->label('Ancho Área Trabajo')
                            ->suffix('PX')
                            ->numeric()
                            ->afterStateHydrated(function (TextInput $component, Get $get) use ($debugOrientacionOverrideName) {
                                if ($component->getState() === null || $component->getState() === '') {
                                    $currentPreviewOrientation = $get($debugOrientacionOverrideName) ?: $get('../../orientacion') ?? 'Vertical';
                                    $component->state(($currentPreviewOrientation === 'Vertical') ? '650' : '1004');
                                }
                            })
                            ->dehydrated(false)
                            ->live(debounce: '500ms'),
                        TextInput::make($debugAltoWorkspaceName)
                            ->id($debugAltoWorkspaceName)
                            ->label('Alto Área Trabajo')
                            ->suffix('PX')
                            ->numeric()
                            ->afterStateHydrated(function (TextInput $component, Get $get) use ($debugOrientacionOverrideName) {
                                if ($component->getState() === null || $component->getState() === '') {
                                    $currentPreviewOrientation = $get($debugOrientacionOverrideName) ?: $get('../../orientacion') ?? 'Vertical';
                                    $component->state(($currentPreviewOrientation === 'Vertical') ? '1004' : '650');
                                }
                            })
                            ->dehydrated(false)
                            ->live(debounce: '500ms'),
                        TextInput::make($debugPosicionXOverrideName)
                            ->id($debugPosicionXOverrideName)
                            ->label('Posición X Área Trabajo')
                            ->suffix('PX')
                            ->numeric()->step(0.01)
                            ->afterStateHydrated(function (TextInput $component, Get $get) use ($debugAnchoWorkspaceName, $var_Zoom_Scale, $debugOrientacionOverrideName, $debugAltoWorkspaceName) {
                                if ($component->getState() === null || $component->getState() === '') {
                                    $currentPreviewOrientation = $get($debugOrientacionOverrideName) ?: $get('../../orientacion') ?? 'Vertical';
                                    $workspaceWValue = $get($debugAnchoWorkspaceName);
                                    $workspaceW = (float) (!empty($workspaceWValue) ? $workspaceWValue : ($currentPreviewOrientation === 'Vertical' ? 650 : 1004));

                                    $scaleValueFromOverride = $get($var_Zoom_Scale);
                                    $scaleFactor = 0;
                                    if ($scaleValueFromOverride !== null && (string)$scaleValueFromOverride !== '') {
                                        $scalePercentage = (float)$scaleValueFromOverride;
                                        $scaleFactor = $scalePercentage / 100;
                                    } else {
                                        $workspaceHValue = $get($debugAltoWorkspaceName);
                                        $workspaceH = (float) (!empty($workspaceHValue) ? $workspaceHValue : ($currentPreviewOrientation === 'Vertical' ? 1004 : 650));
                                        $scaleToFitHeight = 1.0;
                                        if ($workspaceH > 0 && self::VIEWPORT_FIXED_RENDER_HEIGHT > 0) {
                                            $scaleToFitHeight = self::VIEWPORT_FIXED_RENDER_HEIGHT / $workspaceH;
                                        }
                                        $calculatedScale = min($scaleToFitHeight, 1.0) * self::FIT_SCALE_REDUCTION_FACTOR;
                                        $scaleFactor = max($calculatedScale, self::MIN_SCALE_FACTOR);
                                    }
                                     if ($scaleFactor <= 0 || $scaleFactor > 1) $scaleFactor = self::MIN_SCALE_FACTOR;
                                    $centeredX = (self::VIEWPORT_FIXED_RENDER_WIDTH - ($workspaceW * $scaleFactor)) / 2;
                                    $component->state((string)round($centeredX, 2));
                                }
                            })
                            ->dehydrated(false)
                            ->live(debounce: '500ms'),
                        TextInput::make($debugPosicionYOverrideName)
                            ->id($debugPosicionYOverrideName)
                            ->label('Posición Y Área Trabajo')
                            ->suffix('PX')
                            ->numeric()->step(0.01)
                             ->afterStateHydrated(function (TextInput $component, Get $get) use ($debugAltoWorkspaceName, $var_Zoom_Scale, $debugOrientacionOverrideName) {
                                if ($component->getState() === null || $component->getState() === '') {
                                    $currentPreviewOrientation = $get($debugOrientacionOverrideName) ?: $get('../../orientacion') ?? 'Vertical';
                                    $workspaceHValue = $get($debugAltoWorkspaceName);
                                    $workspaceH = (float) (!empty($workspaceHValue) ? $workspaceHValue : ($currentPreviewOrientation === 'Vertical' ? 1004 : 650));
                                    $scaleValueFromOverride = $get($var_Zoom_Scale);
                                    $scaleFactor = 0;
                                    if ($scaleValueFromOverride !== null && (string)$scaleValueFromOverride !== '') {
                                        $scalePercentage = (float)$scaleValueFromOverride;
                                        $scaleFactor = $scalePercentage / 100;
                                    } else {
                                        $scaleToFitHeight = 1.0;
                                        if ($workspaceH > 0 && self::VIEWPORT_FIXED_RENDER_HEIGHT > 0) {
                                            $scaleToFitHeight = self::VIEWPORT_FIXED_RENDER_HEIGHT / $workspaceH;
                                        }
                                        $calculatedScale = min($scaleToFitHeight, 1.0) * self::FIT_SCALE_REDUCTION_FACTOR;
                                        $scaleFactor = max($calculatedScale, self::MIN_SCALE_FACTOR);
                                    }
                                     if ($scaleFactor <= 0 || $scaleFactor > 1) $scaleFactor = self::MIN_SCALE_FACTOR;
                                    $centeredY = (self::VIEWPORT_FIXED_RENDER_HEIGHT - ($workspaceH * $scaleFactor)) / 2;
                                    $component->state( (string)round($centeredY, 2) );
                                }
                            })
                            ->dehydrated(false)
                            ->live(debounce: '500ms'),

                        TextInput::make($debugAnchoPreviewName)
                            ->id($debugAnchoPreviewName)
                            ->label('Ancho Actual Preview')
                            ->readOnly()
                            ->live()
                            ->suffix('PX')
                            ->afterStateHydrated(function (TextInput $component) {
                                $component->state((string)self::VIEWPORT_FIXED_RENDER_WIDTH);
                            })

                            ->dehydrated(false),
                        TextInput::make($debugAltoPreviewName)
                            ->id($debugAltoPreviewName)
                            ->label('Alto Actual Preview')
                            ->readOnly()
                            ->live()
                            ->suffix('PX')
                            ->afterStateHydrated(function (TextInput $component) {
                                $component->state((string)self::VIEWPORT_FIXED_RENDER_HEIGHT);
                            })
                            ->dehydrated(false),

                        FormComponentActions::make([
                            FormAction::make("center_x_debug_face_{$targetFace}")
                                ->label('Centrar X (Debug)')
                                ->icon('heroicon-o-arrows-right-left')
                                ->action(null)
                                ->extraAttributes(['id' => "center_x_debug_btn_{$targetFace}"]),
                        ])->columnSpanFull()->alignCenter(),

                        FormComponentActions::make([
                            FormAction::make("refresh_preview_with_debug_controls_{$targetFace}")
                                ->label('Refrescar Preview con Controles Debug')
                                ->icon('heroicon-o-arrow-path')
                                ->action(function (Set $set) use ($debugRefreshTriggerName) {
                                    $set($debugRefreshTriggerName, Str::random());
                                    Notification::make()->success()->title('Preview Refrescado')->body("Usando valores de los campos de depuración.")->send();
                                })
                        ])->alignCenter()->columnSpanFull(),
                    ])->columns(2)->collapsible()->collapsed(),
            ]);
    }

    /**
     * Define y configura el Repeater relacional filtrado por cara.
     */
    protected static function getRelationalElementsRepeater(int $targetFace): Repeater
    {
        return Repeater::make('cardselements')
            ->relationship('cardselements', null, function (Builder $query) use ($targetFace) {
                $query->where('cara', $targetFace);
            })
            ->schema(static::getElementSchemaForRepeater($targetFace))
            ->orderColumn('z_index')
            ->addActionLabel('Añadir Elemento')
            ->collapsible()
            ->cloneable()
            ->reorderableWithDragAndDrop('z_index')
            ->live()
            ->itemLabel(function (array $state): ?string {
                $tipo = $state['tipo_elemento'] ?? 'Desconocido';
                $info = $state['informacion'] ?? '';
                $description = '';
                switch ($tipo) {
                    case 'Imagen': $description = basename($info); break;
                    case 'Texto':
                        if (Str::isJson($info)) {
                            $data = json_decode($info, true);
                            $description = Str::limit($data['content'] ?? '', 30);
                        } else {
                            $description = Str::limit($state['text_content'] ?? $info, 30);
                        }
                        break;
                    case 'Registro':
                         if (Str::isJson($info)) {
                            $data = json_decode($info, true);
                            $description = "Campo: " . ($data['field'] ?? 'N/A');
                        } else {
                             $description = "Campo: " . $info;
                        }
                        break;
                    case 'Foto': $description = 'Foto de Persona'; break;
                }
                return "**{$tipo}**" . ($description ? " - {$description}" : '');
            })
            ->defaultItems(0);
    }


    /**
     * Define el schema para cada item dentro del Repeater relacional.
     */
    protected static function getElementSchemaForRepeater(int $targetFace): array
    {
        $institutionIdPathForGalleryOptions = '../../institution_id';
        $correctInstitutionIdPathForAction = '../../institution_id';

        return [
            Hidden::make('cara')->default($targetFace),

            Hidden::make('original_image_width')->dehydrated(false),
            Hidden::make('original_image_height')->dehydrated(false),

            Group::make()
                ->schema([
                    Select::make('tipo_elemento')->label('Tipo')
                        ->options([
                            'Imagen' => 'Imagen', 'Texto' => 'Texto',
                            'Registro' => 'Registro', 'Foto' => 'Foto'
                        ])
                        ->required()->reactive()->live()
                        ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                            $set('informacion', null);
                            $set('text_content', null); $set('text_color', '#000000');
                            $set('text_size', 10); $set('text_font', 'Arial');
                            $set('field_name', null); $set('reg_color', '#000000');
                            $set('reg_size', 10); $set('reg_font', 'Arial');
                            $set('original_image_width', null);
                            $set('original_image_height', null);

                            if ($state === 'Imagen') {
                                $set('tamano_W', null);
                                $set('tamano_H', null);
                            } else {
                                $set('tamano_W', '150');
                                $set('tamano_H', '50');
                            }
                            $set('tipo_elemento_confirmed', $state !== null);
                        })
                        ->visible(fn (Get $get): bool => $get('tipo_elemento_confirmed') !== true),

                    Placeholder::make('display_tipo_elemento_simple')
                        ->label(false)
                        ->content(fn (Get $get): string => ($get('tipo_elemento') ?? ''))
                        ->visible(fn (Get $get): bool => $get('tipo_elemento_confirmed') === true && filled($get('tipo_elemento'))),

                    Hidden::make('tipo_elemento_confirmed')->default(false)->reactive()->live(),
                ])
                ->columnSpanFull(),

            Section::make('Detalles de Imagen')
                ->label('Galería')->collapsible()
                ->visible(fn (Get $get) => $get('tipo_elemento') === 'Imagen')
                ->headerActions([
                    FormAction::make('openAddToGalleryModal')
                        ->label('Añadir a Galería')->icon('heroicon-o-plus-circle')
                        ->modalHeading('Añadir Nueva Imagen a la Galería')
                        ->form([
                            FileUpload::make('new_asset_file')
                                ->label('Archivo de Imagen')->image()->imageEditor()->required()->directory('card-assets')
                                ->getUploadedFileNameForStorageUsing(
                                    fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())->prepend(auth()->id() . '-asset-' . time() . '-')
                                ),
                        ])
                        ->action(function (array $data, Get $get, Set $set) use ($correctInstitutionIdPathForAction) {
                            $institutionId = $get($correctInstitutionIdPathForAction);
                            if (!$institutionId) {
                                Notification::make()->danger()->title('Error de Institución')->body('Seleccione una institución para el diseño antes de añadir imágenes.')->send();
                                return;
                            }
                            $filePath = $data['new_asset_file'];
                            try {
                                Cardsasset::create([
                                    'institution_id' => $institutionId, 'path_archivo' => $filePath,
                                    'nombre_descriptivo' => 'galeria_' . time() . '_' . Str::random(5),
                                    'tipo_archivo' => Storage::disk('public')->mimeType($filePath),
                                    'tamano_archivo' => Storage::disk('public')->size($filePath),
                                ]);
                                Notification::make()->success()->title('Imagen añadida.')->send();
                                $set('gallery_refresh_trigger', time());
                            } catch (\Exception $e) {
                                Log::error("Error al añadir asset: " . $e->getMessage());
                                Notification::make()->danger()->title('Error al añadir imagen.')->send();
                            }
                        }),
                ])
                ->schema([
                    Hidden::make('gallery_refresh_trigger')->live(),
                    Radio::make('informacion')
                        ->label(false)->live()
                        ->required(fn (Get $get): bool => $get('tipo_elemento') === 'Imagen')
                        ->options(function (Get $get) use ($institutionIdPathForGalleryOptions): array {
                            $get('gallery_refresh_trigger');
                            $currentInstitutionId = $get($institutionIdPathForGalleryOptions);
                            if (!$currentInstitutionId) return [];
                            $assets = Cardsasset::where('institution_id', $currentInstitutionId)->orderBy('created_at', 'desc')->get();
                            $formattedOptions = [];
                            foreach ($assets as $asset) {
                                $path = $asset->path_archivo;
                                $assetName = $asset->nombre_descriptivo ?? basename($path);
                                try {
                                    $url = Storage::disk('public')->url($path);
                                    $formattedOptions[$path] = new HtmlString(
                                        "<div class='gallery-item-container'><img src='{$url}' alt='" . htmlspecialchars($assetName) . "' class='gallery-item-image' /></div>"
                                    );
                                } catch (\Exception $e) {
                                    Log::error("Error URL asset '{$path}': " . $e->getMessage());
                                    $formattedOptions[$path] = new HtmlString('<span>Error</span>');
                                }
                            }
                            return $formattedOptions;
                        })
                        ->extraAttributes(['class' => 'custom-grid-radio-gallery'])
                        ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                            if ($state && Storage::disk('public')->exists($state)) {
                                try {
                                    $imagePath = Storage::disk('public')->path($state);
                                    $imageSize = getimagesize($imagePath);
                                    if ($imageSize) {
                                        $imgWidth = $imageSize[0];
                                        $imgHeight = $imageSize[1];

                                        $set('original_image_width', $imgWidth);
                                        $set('original_image_height', $imgHeight);
                                        $set('tamano_W', (string)$imgWidth);
                                        $set('tamano_H', (string)$imgHeight);
                                    }
                                } catch (\Exception $e) {
                                    Log::error("Error obteniendo tamaño de imagen: " . $e->getMessage());
                                    $set('original_image_width', null);
                                    $set('original_image_height', null);
                                    $set('tamano_W', null);
                                    $set('tamano_H', null);
                                }
                            } else {
                                $set('original_image_width', null);
                                $set('original_image_height', null);
                            }
                        }),
                ])
                ->columnSpan(1)->columns(1),

            Section::make('Detalles de Texto')->collapsible()
                ->schema([
                    Textarea::make('text_content')->label('Contenido')->required()->live()->columnSpanFull(),
                    ColorPicker::make('text_color')->label('Color')->default('#000000')->live(),
                    TextInput::make('text_size')->label('Tamaño (px)')->numeric()->default(10)->minValue(1)->live(),
                    Select::make('text_font')->label('Fuente')->options(self::getFontOptions())->default('Arial')->live()->searchable(),
                    Hidden::make('informacion')
                         ->dehydrateStateUsing(fn (Get $get): ?string => json_encode([
                             'content' => $get('text_content'), 'color' => $get('text_color') ?? '#000000',
                             'size' => $get('text_size') ?? 10, 'font' => $get('text_font') ?? 'Arial',
                         ])),
                    Hidden::make('text_content')->dehydrated(false), Hidden::make('text_color')->dehydrated(false),
                    Hidden::make('text_size')->dehydrated(false), Hidden::make('text_font')->dehydrated(false),
                ])->columns(3)->visible(fn (Get $get) => $get('tipo_elemento') === 'Texto'),

            Section::make('Detalles de Registro')->collapsible()
                ->schema([
                    Select::make('field_name')->label('Campo de Persona')->options(self::getPeopleFieldOptions())->required()->live()->searchable()->columnSpanFull(),
                    ColorPicker::make('reg_color')->label('Color')->default('#000000')->live(),
                    TextInput::make('reg_size')->label('Tamaño (px)')->numeric()->default(10)->minValue(1)->live(),
                    Select::make('reg_font')->label('Fuente')->options(self::getFontOptions())->default('Arial')->live()->searchable(),
                     Hidden::make('informacion')
                          ->dehydrateStateUsing(fn (Get $get): ?string => json_encode([
                              'field' => $get('field_name'), 'color' => $get('reg_color') ?? '#000000',
                              'size' => $get('reg_size') ?? 10, 'font' => $get('reg_font') ?? 'Arial',
                          ])),
                     Hidden::make('field_name')->dehydrated(false), Hidden::make('reg_color')->dehydrated(false),
                     Hidden::make('reg_size')->dehydrated(false), Hidden::make('reg_font')->dehydrated(false),
                ])->columns(3)->visible(fn (Get $get) => $get('tipo_elemento') === 'Registro'),

             Section::make('Detalles de Foto')->collapsible()
                 ->schema([
                     Placeholder::make('foto_info')->content('Este elemento mostrará la foto asociada a la persona.'),
                     Hidden::make('informacion')->default('[[PHOTO_PLACEHOLDER]]'),
                 ])->visible(fn (Get $get) => $get('tipo_elemento') === 'Foto'),

            Section::make('Posición y Tamaño (px o %)')->columns(2)->collapsible()
                ->schema([
                    TextInput::make('posicion_X')->label('X (px o %)')->required()->default('10')->live(),
                    TextInput::make('posicion_Y')->label('Y (px o %)')->required()->default('10')->live(),
                    TextInput::make('tamano_W')->label('Ancho (px o %)')->required()->default('150')->live()
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            $orientacion = $get('../../orientacion') ?? 'Vertical';
                            $workspaceNativeWidth = ($orientacion === 'Vertical' ? 650 : 1004);

                            $currentValue = $state;
                            $isPercentageInput = is_string($currentValue) && str_ends_with($currentValue, '%');
                            $valueToProcess = $currentValue;

                            if ($isPercentageInput) {
                                $percentage = (float) rtrim($currentValue, '%');
                                $calculatedPxValue = round(($percentage / 100) * $workspaceNativeWidth);
                                $set('tamano_W', (string)$calculatedPxValue);
                                $valueToProcess = (string)$calculatedPxValue;
                            }

                            if ($get('tipo_elemento') !== 'Imagen') return;
                            if (!is_numeric($valueToProcess)) return;

                            $newWidth = (float) $valueToProcess;
                            if ($newWidth <= 0) return;

                            $tamanoH_state = $get('tamano_H');
                            if (is_string($tamanoH_state) && str_ends_with($tamanoH_state, '%')) {
                                return;
                            }

                            $originalWidth = (float) $get('original_image_width');
                            $originalHeight = (float) $get('original_image_height');

                            if ($originalWidth > 0 && $originalHeight > 0) {
                                $newHeight = round(($newWidth / $originalWidth) * $originalHeight);
                                $currentTamanoH = $get('tamano_H');
                                if (!is_numeric($currentTamanoH) || (string)round((float)$currentTamanoH) !== (string)$newHeight) {
                                    $set('tamano_H', $newHeight > 0 ? (string)$newHeight : null);
                                }
                            }
                        }),
                    TextInput::make('tamano_H')->label('Alto (px o %)')->required()->default('50')->live()
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            $orientacion = $get('../../orientacion') ?? 'Vertical';
                            $workspaceNativeHeight = ($orientacion === 'Vertical' ? 1004 : 650);

                            $currentValue = $state;
                            $isPercentageInput = is_string($currentValue) && str_ends_with($currentValue, '%');
                            $valueToProcess = $currentValue;

                            if ($isPercentageInput) {
                                $percentage = (float) rtrim($currentValue, '%');
                                $calculatedPxValue = round(($percentage / 100) * $workspaceNativeHeight);
                                $set('tamano_H', (string)$calculatedPxValue);
                                $valueToProcess = (string)$calculatedPxValue;
                            }

                            if ($get('tipo_elemento') !== 'Imagen') return;
                            if (!is_numeric($valueToProcess)) return;

                            $newHeight = (float) $valueToProcess;
                            if ($newHeight <= 0) return;

                            $tamanoW_state = $get('tamano_W');
                            if (is_string($tamanoW_state) && str_ends_with($tamanoW_state, '%')) {
                                return;
                            }

                            $originalWidth = (float) $get('original_image_width');
                            $originalHeight = (float) $get('original_image_height');

                            if ($originalHeight > 0 && $originalWidth > 0) {
                                $newWidth = round(($newHeight / $originalHeight) * $originalWidth);
                                $currentTamanoW = $get('tamano_W');
                                if (!is_numeric($currentTamanoW) || (string)round((float)$currentTamanoW) !== (string)$newWidth) {
                                    $set('tamano_W', $newWidth > 0 ? (string)$newWidth : null);
                                }
                            }
                        }),
                ])
                ,

            Toggle::make('visible')->label('Visible')->default(true)->inline(false),
        ];
    }

    protected static function getFontOptions(): array
    {
        return [
            'Arial' => 'Arial, Helvetica, sans-serif', 'Verdana' => 'Verdana, Geneva, sans-serif',
            'Tahoma' => 'Tahoma, Geneva, sans-serif', 'Times New Roman' => '"Times New Roman", Times, serif',
            'Georgia' => 'Georgia, serif', 'Courier New' => '"Courier New", Courier, monospace'
        ];
    }

    protected static function getPeopleFieldOptions(): array
    {
        // Aquí podrías obtener dinámicamente los campos del modelo People si lo deseas
        // Por ahora, mantenemos una lista estática.
        // Ejemplo dinámico (requiere reflexión o una lista predefinida en el modelo People):
        // return array_fill_keys((new \App\Models\People())->getFillable(), fn($item) => Str::title(str_replace('_', ' ', $item)));
        return [
            'nombres' => 'Nombres',
            'apellidos' => 'Apellidos',
            'cedula' => 'Cédula',
            'fecha_nacimiento' => 'Fecha Nac.',
            // Añade más campos relevantes del modelo People aquí
        ];
    }

    public static function inferElementType(?array $state): ?string
    {
        if (!$state || empty($state['tipo_elemento'])) {
            $info = $state['informacion'] ?? null;
            if ($info === '[[PHOTO_PLACEHOLDER]]') return 'Foto';
            if (is_string($info) && Str::isJson($info)) {
                 $data = json_decode($info, true);
                 if (isset($data['content'])) return 'Texto';
                 if (isset($data['field'])) return 'Registro';
            }
            // Una heurística simple para imágenes: si contiene / o . y no es JSON
            if (is_string($info) && (Str::contains($info, '/') || Str::contains($info, '.')) && !Str::isJson($info)) return 'Imagen';
            return null; // No se pudo inferir
        }
        return $state['tipo_elemento'];
    }

     protected static function prepareElementForForm(array $elementData): array
     {
         $processedElement = $elementData;
         // Asegurar que tipo_elemento esté presente, infiriéndolo si es necesario
         $processedElement['tipo_elemento'] = $processedElement['tipo_elemento'] ?? static::inferElementType($processedElement);
         $tipo = $processedElement['tipo_elemento'];
         $info = $processedElement['informacion'] ?? null;

         if ($tipo === 'Texto' && is_string($info) && Str::isJson($info)) {
             $textData = json_decode($info, true);
             $processedElement['text_content'] = $textData['content'] ?? null;
             $processedElement['text_color'] = $textData['color'] ?? '#000000';
             $processedElement['text_size'] = $textData['size'] ?? 10;
             $processedElement['text_font'] = $textData['font'] ?? 'Arial';
         } elseif ($tipo === 'Registro' && is_string($info) && Str::isJson($info)) {
              $regData = json_decode($info, true);
              $processedElement['field_name'] = $regData['field'] ?? null;
              $processedElement['reg_color'] = $regData['color'] ?? '#000000';
              $processedElement['reg_size'] = $regData['size'] ?? 10;
              $processedElement['reg_font'] = $regData['font'] ?? 'Arial';
         } elseif ($tipo === 'Imagen' && is_string($info) && !empty($info)) {
            // Intentar obtener dimensiones originales de la imagen
            if (Storage::disk('public')->exists($info)) {
                try {
                    $imagePath = Storage::disk('public')->path($info);
                    $imageSize = @getimagesize($imagePath); // Usar @ para suprimir errores si la imagen no es válida
                    if ($imageSize) {
                        $processedElement['original_image_width'] = $imageSize[0];
                        $processedElement['original_image_height'] = $imageSize[1];
                    } else {
                        // No se pudieron obtener las dimensiones, establecer a null o un valor por defecto
                        $processedElement['original_image_width'] = null;
                        $processedElement['original_image_height'] = null;
                    }
                } catch (\Exception $e) {
                    // Error al procesar la imagen
                    $processedElement['original_image_width'] = null;
                    $processedElement['original_image_height'] = null;
                }
            } else {
                 // La imagen no existe, establecer a null
                 $processedElement['original_image_width'] = null;
                 $processedElement['original_image_height'] = null;
            }
         }
         return $processedElement;
     }

    protected static function parsePositionValue(string|int|null $value, float $totalCanvasDimension): float
    {
        if ($value === null) return 0.0;
        if (is_string($value) && str_ends_with($value, '%')) {
            $percentage = (float) rtrim($value, '%');
            return ($percentage / 100) * $totalCanvasDimension;
        }
        return (float) $value;
    }

    protected static function parseSizeValue(string|int|null $value, float $totalCanvasDimension): float
    {
        if ($value === null) return 0.0;
        $pxVal = 0.0;
        if (is_string($value) && str_ends_with($value, '%')) {
            $percentage = (float) rtrim($value, '%');
            $pxVal = ($percentage / 100) * $totalCanvasDimension;
        } else {
            $pxVal = (float) $value;
        }
        return $pxVal < 0 ? 0 : $pxVal; // Evitar tamaños negativos
    }

    private static function renderPreviewFaceCanvas(array $elements, int $faceNumber, float $canvasWidthPx, float $canvasHeightPx): string
    {
        $output = "";
        foreach ($elements as $elementState) {
            $preparedElement = static::prepareElementForForm($elementState); // Usar el método preparado
            if (!($preparedElement['visible'] ?? true)) continue;
            $type = $preparedElement['tipo_elemento'] ?? null;
            if (!$type) continue; // Si no hay tipo, no se puede renderizar

            $x_val_str = (string) ($preparedElement['posicion_X'] ?? '10');
            $y_val_str = (string) ($preparedElement['posicion_Y'] ?? '10');
            $w_val_str = (string) ($preparedElement['tamano_W'] ?? '150');
            $h_val_str = (string) ($preparedElement['tamano_H'] ?? '50');

            // Para imágenes, si el tamaño no está definido pero sí las dimensiones originales, usarlas
            if ($type === 'Imagen') {
                if (($preparedElement['tamano_W'] === null || $preparedElement['tamano_W'] === '') && isset($preparedElement['original_image_width'])) {
                    $w_val_str = (string)$preparedElement['original_image_width'];
                }
                if (($preparedElement['tamano_H'] === null || $preparedElement['tamano_H'] === '') && isset($preparedElement['original_image_height'])) {
                    $h_val_str = (string)$preparedElement['original_image_height'];
                }
            }

            $x_px = self::parsePositionValue($x_val_str, $canvasWidthPx);
            $y_px = self::parsePositionValue($y_val_str, $canvasHeightPx);
            $w_px = self::parseSizeValue($w_val_str, $canvasWidthPx);
            $h_px = self::parseSizeValue($h_val_str, $canvasHeightPx);

            $info = $preparedElement['informacion'] ?? null;
            $zIndex = $preparedElement['z_index'] ?? 0;
            $baseStyle = "position: absolute; left: {$x_px}px; top: {$y_px}px; width: {$w_px}px; height: {$h_px}px; border: 0.5px dashed #d0d0d0; overflow: hidden; box-sizing: border-box; z-index: {$zIndex};";
            $style = $baseStyle; $content = ''; $error = false;

            switch ($type) {
                case 'Imagen':
                    $imagePath = $info;
                    if (is_string($imagePath) && !empty($imagePath)) {
                         if (Storage::disk('public')->exists($imagePath)) {
                            try {
                                $imageUrl = Storage::disk('public')->url($imagePath);
                                $content = "<img src='{$imageUrl}' style='display: block; width: 100%; height: 100%; object-fit: contain;' alt='".htmlspecialchars(basename($imagePath))."'>"; // Added htmlspecialchars
                                $style = str_replace('border: 0.5px dashed', 'border: none', $style);
                                $style .= " background-color: transparent;";
                            }
                            catch (\Exception $e) { $content = "<small style='color: orange;'>URL ERR</small>"; $error = true; }
                        } else {
                             if (Str::startsWith($imagePath, 'livewire-tmp/')) { // Manejar imágenes temporales de FileUpload
                                 // Intentar obtener la URL temporal si es posible, o mostrar un placeholder
                                 // Esto es un desafío porque el TemporaryUploadedFile no está disponible aquí directamente.
                                 // Se podría pasar la URL temporal al estado del elemento si es una nueva subida.
                                 $content = "<div style='width:100%;height:100%;background:#f0f0f0;display:flex;align-items:center;justify-content:center;font-size:0.8em;color:#aaa;'>Nueva Imagen</div>";
                                 // $error = true; // No necesariamente un error, es una imagen temporal
                             } else {
                                 $content = "<small style='color: red;'>IMG NF</small>"; $error = true;
                             }
                         }
                    } else { $content = "<small style='color: red;'>IMG ERR</small>"; $error = true; }
                    break;
                case 'Texto':
                    $textContent = $preparedElement['text_content'] ?? '';
                    $content = nl2br(htmlspecialchars($textContent));
                    $textColor = $preparedElement['text_color'] ?? '#000000';
                    $textSize = $preparedElement['text_size'] ?? 10;
                    $textFont = $preparedElement['text_font'] ?? 'Arial';
                    $style = str_replace('border: 0.5px dashed', 'border: 0.5px dashed', $style); $style .= " border-color: #e0e0e0; background-color: rgba(255, 255, 255, 0.0); word-wrap: break-word; white-space: pre-wrap;";
                    $style .= " color: {$textColor}; font-size: {$textSize}px; font-family: {$textFont}; padding: 2px;";
                    if (empty($textContent)) { $content = "<small style='color: red;'>CONT ERR</small>"; $error = true; }
                    break;
                case 'Registro':
                    $fieldName = $preparedElement['field_name'] ?? '';
                    $regColor = $preparedElement['reg_color'] ?? '#0277bd'; // Un color azul por defecto
                    $regSize = $preparedElement['reg_size'] ?? 10;
                    $regFont = $preparedElement['reg_font'] ?? 'Arial';
                    $regStyle = "color: {$regColor}; font-size: {$regSize}px; font-family: {$regFont}; padding: 0; margin: 0; line-height: 1.2; display: inline-block; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;";
                    $content = "<span style='{$regStyle}'>[".htmlspecialchars($fieldName ?: 'CAMPO')."]</span>";
                    $style = str_replace('border: 0.5px dashed', 'border: 0.5px dashed', $style); $style .= " border-color: #b3e5fc; background-color: rgba(224, 247, 250, 0.0); display: flex; align-items: center; padding: 2px;"; // Fondo azul claro
                    if (empty($fieldName)) { $content = "<small style='color: red;'>CAMPO ERR</small>"; $error = true; }
                    break;
                case 'Foto':
                    $content = "<div style='width:100%; height:100%; background:#e0e0e0; display:flex; align-items:center; justify-content:center;'><svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='#9e9e9e' style='width:60%; height:60%; max-width:40px;'><path stroke-linecap='round' stroke-linejoin='round' d='M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z' /></svg></div>"; $style .= " background-color: transparent; border-style: dashed; border-color: #bdbdbd;";
                    break;
                default: $content = "<small style='color: #aaa; padding: 2px;'>(".htmlspecialchars($type).")</small>"; $style .= " border-style: dotted; padding: 2px;"; break;
            }
            if ($error) $style .= " border-color: red !important; border-style: solid !important;";
            $output .= "<div style='{$style}'>{$content}</div>";
        }
        return $output;
    }

    public static function mutateFormDataBeforeFill(array $data): array
    {
        $record = static::getModel()::find($data['id'] ?? null);
        if ($record) {
            $relatedElements = $record->cardselements()->orderBy('cara')->orderBy('z_index')->get()->toArray();
            $data['cardselements'] = array_map(function ($element) {
                 $prepared = static::prepareElementForForm($element);
                 $prepared['tipo_elemento_confirmed'] = !empty($prepared['tipo_elemento']); // Confirmar tipo si ya existe
                 // Asegurar que los valores numéricos se traten como strings para los inputs
                 foreach (['posicion_X', 'posicion_Y', 'tamano_W', 'tamano_H'] as $key) {
                     if (isset($prepared[$key]) && is_numeric($prepared[$key])) {
                         $prepared[$key] = (string)$prepared[$key];
                     }
                 }
                 return $prepared;
             }, $relatedElements);
        } else {
            $data['cardselements'] = []; // Asegurar que cardselements sea un array vacío si no hay record
        }
        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('orientacion'), TextColumn::make('caras'),
                TextColumn::make('group.nombre')->label('Grupo')->sortable(),
                TextColumn::make('institution.nombre')->label('Institución')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('institution')
                    ->relationship('institution', 'nombre')->label('Filtrar por Institución'),
            ])
            ->actions([ TableEditAction::make(), TableDeleteAction::make(), ])
            ->bulkActions([ BulkActionGroup::make([ DeleteBulkAction::make(), ]), ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListCardsdesigns::route('/'),
            'create' => Pages\CreateCardsdesign::route('/create'),
            'edit' => Pages\EditCardsdesign::route('/{record}/edit'),
        ];
    }
}
