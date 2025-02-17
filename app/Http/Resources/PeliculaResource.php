<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PeliculaResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'duracion' => $this->duracion,
            'rutaImagen' => $this->rutaImagen,
            'rutaLocalImagen' => $this->rutaLocalImagen,
            'clasificacion' => $this->clasificacion,
            'categoria_id' => $this->categoria_id,
        ];
    }
}
