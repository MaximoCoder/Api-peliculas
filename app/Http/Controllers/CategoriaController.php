<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Http\Resources\CategoriaResource;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     title="API de Películas y Categorías",
 *     version="1.0",
 *     description="Documentación de la API para gestionar películas y categorías"
 * )
 */
class CategoriaController extends Controller
{
    /**
     * Obtener todas las categorías.
     *
     * @OA\Get(
     *     path="/api/categorias",
     *     summary="Lista de categorías",
     *     tags={"Categorías"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorías obtenida con éxito",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Categoria"))
     *     )
     * )
     */
    public function index()
    {
        // Mostrar todos los registros de categorias
        return CategoriaResource::collection(Categoria::all());
    }

    /**
     * Almacenar una nueva categoría.
     *
     * @OA\Post(
     *     path="/api/categorias",
     *     summary="Agregar una categoría",
     *     tags={"Categorías"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Categoria")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categoría agregada con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Categoria")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validated = $request->validate([
                'nombre' => 'required|string|max:255|unique:categorias',
            ]);
    
            // Crear la categoría
            $categoria = Categoria::create($validated);
    
            // Retornar respuesta con código 201 (Created)
            return response()->json([
                'message' => 'Categoría agregada con éxito',
                'data' => $categoria
            ], 201);
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una Categoria por ID.
     *
     * @OA\Get(
     *     path="/api/categorias/{id}",
     *     summary="Obtener una categoría especifica",
     *     tags={"Categorías"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la categoría",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría obtenida con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Categoria")
     *     )
     * )
     */
    public function show($id)
    {
        return new CategoriaResource(Categoria::findOrFail($id));
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
