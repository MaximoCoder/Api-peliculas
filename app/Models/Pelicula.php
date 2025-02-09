<?php

namespace App\Models;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * @OA\Schema(
 *     schema="Pelicula",
 *     title="Pelicula",
 *     description="Modelo de una película",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="titulo", type="string", example="Titanic"),
 *     @OA\Property(property="descripcion", type="string", example="Película romántica sobre el Titanic"),
 *     @OA\Property(property="categoria_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class Pelicula extends Model
{
    use HasFactory;

    // Relacionamos con la tabla categorias
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
