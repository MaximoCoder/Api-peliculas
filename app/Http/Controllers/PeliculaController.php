<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use Illuminate\Http\Request;
use App\Http\Resources\PeliculaResource;

class PeliculaController extends Controller
{
    /**
     * Obtener todas las películas.
     *
     * @OA\Get(
     *     path="/api/peliculas",
     *     summary="Lista de películas",
     *     tags={"Películas"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de películas obtenida con éxito",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Pelicula")
     *         )
     *     )
     * )
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Obtener una película por ID.
     *
     * @OA\Get(
     *     path="/api/peliculas/{id}",
     *     summary="Obtener una película específica",
     *     tags={"Películas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la película",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Película obtenida con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Pelicula")
     *     )
     * )
     */
    public function show($id)
    {
        return new PeliculaResource(Pelicula::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
