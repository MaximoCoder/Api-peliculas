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
     *      @OA\Parameter(
     *          name="nombre",
     *          in="query",
     *          description="Nombre de la categoría",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
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
     * Actualizar parcialmente una categoría existente.
     *
     * @OA\Patch(
     *     path="/api/categorias/{id}",
     *     summary="Actualizar parcialmente una categoría",
     *     tags={"Categorías"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la categoría",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nombre", type="string", example="Nuevo Nombre")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría actualizada parcialmente con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Categoria")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function partialUpdate(Request $request, string $id)
    {
        // Buscar la categoría o devolver error 404 si no existe
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        // Validar solo los datos proporcionados (sin requerir todos los campos)
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255|unique:categorias,nombre,' . $categoria->id,
        ]);

        // Actualizar solo los campos enviados en la solicitud
        $categoria->update($validated);

        // Retornar respuesta con código 200 (OK)
        return response()->json([
            'message' => 'Categoría actualizada parcialmente con éxito',
            'data' => $categoria
        ], 200);
    }

    /**
     * Actualizar una categoría existente.
     *
     * @OA\PUT(
     *     path="/api/categorias/{id}",
     *     summary="Buscar y actualizar una categoría",
     *     tags={"Categorías"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la categoría",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nombre", type="string", example="Nueva Categoría")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Datos actuales de la categoría antes de editar y actualización exitosa",
     *         @OA\JsonContent(ref="#/components/schemas/Categoria")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        // Buscar la categoría o devolver error 404 si no existe
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        // Si la petición no tiene datos, devolver la categoría actual para edición
        if ($request->isMethod('get') || !$request->all()) {
            return response()->json([
                'message' => 'Datos actuales de la categoría',
                'data' => $categoria
            ], 200);
        }

        // Validar los datos de entrada
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
        ]);

        // Actualizar la categoría
        $categoria->update($validated);

        // Retornar respuesta con código 200 (OK)
        return response()->json([
            'message' => 'Categoría actualizada con éxito',
            'data' => $categoria
        ], 200);
    }

    /**
     * Eliminar la información de una categoría existente.
     * @OA\Delete (
     *     path="/api/categorias/{id}",
     *     tags={"Categorías"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="NO CONTENT"
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No se pudo realizar correctamente la operación"),
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        $categoria = Categoria::find($id);

        if (is_null($categoria)) {
            return response()->json(['message' => 'No se pudo realizar correctamente la operación'], 404);
        }

        $categoria->delete();

        // Retornar respuesta de exito
        $response = [
            'message' => 'Categoría eliminada con exito',
            'data' => $categoria
        ];

        return $response;
    }
}
