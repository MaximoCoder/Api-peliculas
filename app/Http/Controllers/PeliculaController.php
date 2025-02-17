<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Pelicula;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PeliculaResource;
use Illuminate\Validation\ValidationException;

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
        return PeliculaResource::collection(Pelicula::all());
    }

    /**
     * @OA\Post(
     *     path="/api/peliculas",
     *     summary="Crear una nueva película",
     *     tags={"Películas"},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"nombre", "descripcion", "duracion", "rutaImagen", "clasificacion", "categoria_id"},
     *                 @OA\Property(property="nombre", type="string"),
     *                 @OA\Property(property="descripcion", type="string"),
     *                 @OA\Property(property="duracion", type="number", format="float"),
     *                 @OA\Property(property="rutaImagen", type="file"),
     *                 @OA\Property(property="clasificacion", type="integer"),
     *                 @OA\Property(property="categoria_id", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Película creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Película creada correctamente."),
     *             @OA\Property(property="pelicula", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Los datos proporcionados no son válidos.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string|max:1000',
                'duracion' => 'required|numeric|min:0',
                'rutaImagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'clasificacion' => 'required|integer|between:1,5',
                'categoria_id' => 'required|exists:categorias,id'
            ]);

            // Manejar la subida de la imagen
            if ($request->hasFile('rutaImagen')) {
                $imagen = $request->file('rutaImagen');
                $nombreImagen = time() . '_' . $imagen->getClientOriginalName();

                // Guardar la imagen en storage/app/public/peliculas
                $rutaLocalImagen = $imagen->storeAs('peliculas', $nombreImagen, 'public');

                // Generar la URL completa para acceder a la imagen
                $rutaImagen = asset('storage/' . $rutaLocalImagen);

                // Agregar las rutas al array de datos validados
                $validatedData['rutaImagen'] = $rutaImagen;
                $validatedData['rutaLocalImagen'] = $rutaLocalImagen;
            }
            if (!Storage::exists('public/' . $rutaLocalImagen)) {
                return response()->json(['error' => 'No se pudo guardar la imagen en el servidor.'], 500);
            }
            
            // Crear la película con los datos validados
            $pelicula = Pelicula::create($validatedData);

            return response()->json([
                'message' => 'Película creada correctamente.',
                'pelicula' => $pelicula
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Los datos proporcionados no son válidos.',
                'detalles' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Error al procesar la solicitud.',
                'message' => $e->getMessage()
            ], 500);
        }
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
     * Actualizar una película (PUT).
     *
     * @OA\Put(
     *     path="/api/peliculas/{id}",
     *     summary="Actualizar una película",
     *     tags={"Películas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "descripcion", "duracion", "rutaImagen", "rutaLocalImagen", "clasificacion", "categoria_id"},
     *             @OA\Property(property="nombre", type="string", example="Avatar"),
     *             @OA\Property(property="descripcion", type="string", example="Película de ciencia ficción."),
     *             @OA\Property(property="duracion", type="number", example=2.5),
     *             @OA\Property(property="rutaImagen", type="string", example="https://example.com/avatar.jpg"),
     *             @OA\Property(property="rutaLocalImagen", type="string", example="/images/avatar.jpg"),
     *             @OA\Property(property="clasificacion", type="integer", example=1),
     *             @OA\Property(property="categoria_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Película actualizada con éxito"),
     *     @OA\Response(response=404, description="Película no encontrada")
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $pelicula = Pelicula::findOrFail($id);
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'duracion' => 'required|numeric|min:0',
            'rutaImagen' => 'required|string|max:255',
            'rutaLocalImagen' => 'required|string|max:255',
            'clasificacion' => 'required|integer',
            'categoria_id' => 'required|exists:categorias,id'
        ]);

        $pelicula->update($validatedData);

        return response()->json([
            'message' => 'Película actualizada correctamente.',
            'pelicula' => $pelicula
        ], 200);
    }

    /**
     * Actualización parcial de una película (PATCH).
     *
     * @OA\Patch(
     *     path="/api/peliculas/{id}",
     *     summary="Actualizar parcialmente una película",
     *     tags={"Películas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Avatar"),
     *             @OA\Property(property="descripcion", type="string", example="Película de ciencia ficción."),
     *             @OA\Property(property="duracion", type="number", example=2.5),
     *             @OA\Property(property="rutaImagen", type="string", example="https://example.com/avatar.jpg"),
     *             @OA\Property(property="rutaLocalImagen", type="string", example="/images/avatar.jpg"),
     *             @OA\Property(property="clasificacion", type="integer", example=1),
     *             @OA\Property(property="categoria_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Película actualizada parcialmente con éxito"),
     *     @OA\Response(response=404, description="Película no encontrada")
     * )
     */
    public function patchUpdate(Request $request, string $id): JsonResponse
    {
        $pelicula = Pelicula::findOrFail($id);

        $pelicula->update($request->only([
            'nombre',
            'descripcion',
            'duracion',
            'rutaImagen',
            'rutaLocalImagen',
            'clasificacion',
            'categoria_id'
        ]));

        return response()->json([
            'message' => 'Película actualizada parcialmente correctamente.',
            'pelicula' => $pelicula
        ], 200);
    }

    /**
     * Eliminar una película.
     *
     * @OA\Delete(
     *     path="/api/peliculas/{id}",
     *     summary="Eliminar una película",
     *     tags={"Películas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Película eliminada con éxito"),
     *     @OA\Response(response=404, description="Película no encontrada")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $pelicula = Pelicula::findOrFail($id);
        $pelicula->delete();

        return response()->json(['message' => 'Película eliminada correctamente.'], 200);
    }
}
