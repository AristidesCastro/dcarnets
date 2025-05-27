<?php

namespace App\Livewire;

use Livewire\Attributes\On; // <-- Añadir importación
use Livewire\Component;
// use Filament\Forms\Contracts\HasForms; // No necesario si no usas $this->form
// use Filament\Forms\Concerns\InteractsWithForms; // No necesario si no usas $this->form
// use Filament\Forms\Contracts\HasState; // <-- Eliminado
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // Para depuración si es necesario

class MyCompList extends Component // <-- Quitado HasState (y HasForms)
{
    // use InteractsWithForms; // <-- Quitado si no usas $this->form

    // --- Propiedades Públicas (El estado se sincroniza automáticamente vía statePath en Filament) ---
    public array $items = [];      // Almacenará los elementos (recibidos del estado de Filament)
    public int $faceNumber = 1;    // Número de cara a mostrar (1 o 2)
    // public string $statePath;   // <-- Eliminado

    // --- Métodos HasState eliminados (statePath, getState, setState) ---

    // --- Método Mount: Se ejecuta al inicializar el componente ---
    public function mount(int $faceNumber = 1, array $items = []): void
    {
        $this->faceNumber = $faceNumber;
        // La inicialización principal de 'items' la hará Filament automáticamente
        // a través de la sincronización del statePath.
        // Si necesitas asegurar UUIDs/visibilidad al inicio, podrías hacerlo aquí
        // o en un método updatedItems si fuera necesario.
        $this->ensureItemsIntegrity($items);
    }

    /**
     * Asegura que los items tengan UUID y estado 'visible'.
     * Se puede llamar desde mount o updatedItems si es necesario.
     */
    protected function ensureItemsIntegrity(array $initialItems): void
    {
        $this->items = collect($initialItems)->map(function ($item) {
            $item['uuid'] = $item['uuid'] ?? (string) Str::uuid();
            $item['visible'] = isset($item['visible']) ? (bool)$item['visible'] : true;
            return $item;
        })->values()->toArray();
    }

    // --- Escucha de cambios desde Filament (si es necesario) ---
    // Livewire maneja la sincronización de $items automáticamente con statePath.
    // Si necesitaras reaccionar *específicamente* cuando $items cambia DESDE Filament,
    // podrías usar un método updated<PropertyName>.
    public function updatedItems(array $value): void
    {
        // --- DEBUGGING ---
        \Illuminate\Support\Facades\Log::info('MyCompList: updatedItems fue llamado. Valor recibido:', $value);


        // Código a ejecutar cuando $items es actualizado desde fuera (Filament)
        // Asegurarse de que los UUIDs y 'visible' estén correctos
        $this->ensureItemsIntegrity($value);
    }

    /**
     * Escucha el evento despachado desde CardsdesignResource
     * para forzar la actualización del estado de items.
     * El nombre del evento incluye el faceNumber para asegurar
     * que solo la instancia correcta reaccione.
     */
    #[On('updateMyCompList-{faceNumber}')]
    public function forceUpdateItems(array $items): void
    {
        \Illuminate\Support\Facades\Log::info('MyCompList: Evento updateMyCompList-' . $this->faceNumber . ' recibido.');
        // Llamamos manualmente a la lógica de updatedItems con los nuevos datos
        $this->updatedItems($items);
    }


    // --- Acciones Livewire ---

    /**
     * Mueve un elemento una posición hacia arriba en la lista general.
     */
    public function moveUp(string $uuid): void
    {
        $index = $this->findIndexByUuid($uuid);
        if ($index === null || $index === 0) return; // No se puede mover si no existe o es el primero

        // Intercambiar con el elemento anterior
        $previousIndex = $index - 1;
        $temp = $this->items[$previousIndex];
        $this->items[$previousIndex] = $this->items[$index];
        $this->items[$index] = $temp;

        // Reasignar z_index basado en el nuevo orden
        $this->recalculateZIndex();
        // $this->dispatchStateUpdate(); // <-- Eliminado
    }

    /**
     * Mueve un elemento una posición hacia abajo en la lista general.
     */
    public function moveDown(string $uuid): void
    {
        $index = $this->findIndexByUuid($uuid);
        if ($index === null || $index === (count($this->items) - 1)) return; // No se puede mover si no existe o es el último

        // Intercambiar con el elemento siguiente
        $nextIndex = $index + 1;
        $temp = $this->items[$nextIndex];
        $this->items[$nextIndex] = $this->items[$index];
        $this->items[$index] = $temp;

        // Reasignar z_index basado en el nuevo orden
        $this->recalculateZIndex();
        // $this->dispatchStateUpdate(); // <-- Eliminado
    }

    /**
     * Cambia el estado de visibilidad de un elemento.
     */
    public function toggleVisibility(string $uuid): void
    {
        $index = $this->findIndexByUuid($uuid);
        if ($index === null) return;

        $this->items[$index]['visible'] = !$this->items[$index]['visible'];
        // $this->dispatchStateUpdate(); // <-- Eliminado
    }

    /**
     * Elimina un elemento de la lista.
     */
    public function deleteItem(string $uuid): void
    {
        $this->items = collect($this->items)->reject(function ($item) use ($uuid) {
            return ($item['uuid'] ?? null) === $uuid;
        })->values()->toArray(); // Reindexar el array

        // Reasignar z_index basado en el nuevo orden
        $this->recalculateZIndex();
        // $this->dispatchStateUpdate(); // <-- Eliminado
    }

    /**
     * Añade un nuevo elemento con datos aleatorios a la lista.
     */
    public function addRandomItem(): void
    {
        $randomType = Arr::random(['Imagen', 'Texto', 'Registro', 'Foto']);
        $newItem = [
            'uuid' => (string) Str::uuid(),
            'tipo_elemento' => $randomType,
            'informacion' => null, // Se establecerá abajo según el tipo
            'posicion_X' => rand(5, 50),
            'posicion_Y' => rand(5, 80),
            'tamano_W' => rand(20, 60),
            'tamano_H' => rand(10, 30),
            'cara' => $this->faceNumber,
            'z_index' => count($this->items), // El nuevo item va al final
            'visible' => true,
        ];

        // Establecer información específica y campos relacionados
        switch ($randomType) {
            case 'Imagen':
                $newItem['informacion'] = 'placeholder/image_' . rand(1, 5) . '.jpg'; // Ejemplo
                break;
            case 'Texto':
                $newItem['text_content'] = 'Texto aleatorio: ' . Str::random(15);
                $newItem['text_color'] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                $newItem['text_size'] = rand(8, 16);
                $newItem['text_font'] = Arr::random(['Arial', 'Verdana', 'Times New Roman']);
                break;
            case 'Registro':
                $newItem['informacion'] = Arr::random(['nombres', 'apellidos', 'cedula']); // Campo a mostrar
                $newItem['text_color'] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                $newItem['text_size'] = rand(8, 14);
                $newItem['text_font'] = Arr::random(['Arial', 'Verdana', 'Tahoma']);
                break;
            case 'Foto':
                $newItem['informacion'] = '[[PHOTO_PLACEHOLDER]]';
                break;
        }

        $this->items[] = $newItem;
        // No es necesario recalcular Z-Index aquí, ya se asignó correctamente.
        // Livewire sincronizará $items con el statePath automáticamente.
    }

    /**
     * Emite un evento para solicitar la edición de un elemento.
     * La lógica de abrir el modal la manejará Filament/AlpineJS.
     */
    public function editItem(string $uuid): void
    {
        // Emitimos un evento que el componente padre (Filament Form) puede escuchar.
        // Pasamos el UUID del item a editar.
        $this->dispatch('open-edit-modal', itemUuid: $uuid);
        // NOTA: Necesitarás implementar la escucha de este evento en tu
        //       recurso Filament (probablemente con AlpineJS) para abrir
        //       el modal de edición correcto.
    }

    // --- Métodos Auxiliares ---

    /**
     * Encuentra el índice de un item por su UUID.
     */
    protected function findIndexByUuid(string $uuid): ?int
    {
        foreach ($this->items as $index => $item) {
            if (($item['uuid'] ?? null) === $uuid) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Recalcula el z_index de todos los elementos basado en su orden actual.
     */
    protected function recalculateZIndex(): void
    {
        foreach ($this->items as $index => &$item) { // Usar referencia (&) para modificar directamente
            $item['z_index'] = $index;
        }
        // No es necesario unset($item) aquí porque el bucle termina.
    }

    // --- dispatchStateUpdate() eliminado ---

    /**
     * Infiere el tipo de elemento (copiado/adaptado de CardsdesignResource).
     */
    protected function inferElementType(array $state): ?string
    {
        if (!empty($state['tipo_elemento'])) return $state['tipo_elemento'];
        $info = $state['informacion'] ?? null;
        if ($info === '[[PHOTO_PLACEHOLDER]]') return 'Foto';
        if (is_string($info) && Str::isJson($info)) {
            $data = json_decode($info, true);
            if (isset($data['content'])) return 'Texto'; // Asumimos Texto si tiene 'content'
            if (isset($data['field'])) return 'Registro'; // Asumimos Registro si tiene 'field'
        }
        if (in_array($info, ['nombres', 'apellidos', 'cedula', 'fecha_nacimiento'])) return 'Registro';
        if (is_string($info) && (Str::contains($info, '/') || Str::contains($info, '.')) && !Str::isJson($info)) return 'Imagen';
        return null;
    }

    /**
     * Obtiene una descripción breve para mostrar en la lista.
     */
    protected function getItemDescription(array $item): string
    {
        $type = $this->inferElementType($item);
        $info = $item['informacion'] ?? '';

        switch ($type) {
            case 'Imagen':
                // Si 'new_image_upload' existe (es un objeto TemporaryUploadedFile o un path ya procesado)
                if (isset($item['new_image_upload'])) {
                    if ($item['new_image_upload'] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                        return Str::limit($item['new_image_upload']->getClientOriginalName(), 30);
                    } elseif (is_string($item['new_image_upload'])) {
                        return basename($item['new_image_upload']);
                    }
                }
                // Si no, usar 'informacion' (path de galería o placeholder aleatorio)
                return is_string($info) ? basename($info) : 'Imagen inválida';
            case 'Texto':
                // Priorizar 'text_content' si existe (estado en vivo antes de guardar o aleatorio)
                if (isset($item['text_content'])) {
                    return Str::limit(strip_tags($item['text_content']), 30);
                }
                // Si no, intentar decodificar 'informacion' (estado guardado)
                if (is_string($info) && Str::isJson($info)) {
                    $textData = json_decode($info, true);
                    return Str::limit(strip_tags($textData['content'] ?? ''), 30);
                }
                return 'Texto inválido';
            case 'Registro':
                 // Priorizar 'informacion' si es un nombre de campo directo (estado en vivo antes de guardar o aleatorio)
                 if (isset($item['informacion']) && is_string($item['informacion']) && !Str::isJson($item['informacion'])) {
                     return "Campo: " . ($item['informacion'] ?: 'No especificado');
                 }
                 // Si no, intentar decodificar 'informacion' (estado guardado)
                 if (is_string($info) && Str::isJson($info)) {
                    $regData = json_decode($info, true);
                    $fieldName = $regData['field'] ?? '';
                    return "Campo: " . ($fieldName ?: 'No especificado');
                 }
                 return 'Registro inválido';
            case 'Foto':
                return 'Foto de Persona';
            default:
                return 'Elemento desconocido';
        }
    }


    /**
     * Obtiene el icono correspondiente al tipo de elemento.
     */
    protected function getItemIcon(array $item): string
    {
        // Puedes usar iconos de Heroicons (https://heroicons.com/)
        return match ($this->inferElementType($item)) {
            'Imagen' => 'heroicon-o-photo',
            'Texto' => 'heroicon-o-chat-bubble-bottom-center-text',
            'Registro' => 'heroicon-o-rectangle-stack',
            'Foto' => 'heroicon-o-user-circle',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    // --- Método Render: Genera la vista ---
    public function render()
    {
            // --- DEBUGGING ---
            \Illuminate\Support\Facades\Log::info('MyCompList: render() - Estado de $this->items ANTES de filtrar:', $this->items);
            \Illuminate\Support\Facades\Log::info('MyCompList: render() - Filtrando para cara:', ['faceNumber' => $this->faceNumber]);

        // Filtramos los items a mostrar por la cara actual ANTES de pasarlos a la vista
        // Usamos $this->items que ya está sincronizado por Livewire/Filament
        $filteredItems = collect($this->items)
            ->filter(fn ($item) => ($item['cara'] ?? 1) == $this->faceNumber)
            // ->sortBy('z_index') // El orden ya lo mantiene $this->items
            ->values() // Reindexamos después de filtrar para la vista
            ->toArray();
        // --- DEBUGGING ---
        \Illuminate\Support\Facades\Log::info('MyCompList: render() - Items DESPUÉS de filtrar ($filteredItems):', $filteredItems);


        // Calculamos el índice MÁXIMO visible para deshabilitar botones "bajar"
        // Necesitamos el índice MÁXIMO en la lista COMPLETA ($this->items)
        $maxRealIndex = count($this->items) - 1;

        return view('livewire.my-comp-list', [
            'filteredItems' => $filteredItems,
            'maxRealIndex' => $maxRealIndex, // Pasamos el índice máximo real
        ]);
    }
}
