<?php

namespace App\Models;

use App\Models\Pelicula;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * @OA\Schema(
 *     schema="Categoria",
 *     title="Categoría",
 *     description="Modelo de una categoría",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nombre", type="string", example="Acción"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Categoria extends Model
{
    use HasFactory;
    // Permitir que datos pueden ser llenados
    protected $fillable = ['nombre'];
    // Relacionamos con la tabla peliculas
    public function peliculas()
    {
        return $this->hasMany(Pelicula::class);
    }
}
