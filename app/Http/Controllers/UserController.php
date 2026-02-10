<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * Query param trashed: si trashed=true retorna solo usuarios eliminados (soft deleted).
     */
    public function index(Request $request)
    {
        $query = User::query()->orderBy('id');

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        $users = $query->get();
        return response()->json(UserResource::collection($users));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Str::random(8); // Le colocamos una contraseña por defecto

        $user = User::create($data);
        
        return response()->json(UserResource::make($user), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json(UserResource::make($user));
    }

    /**
     * Update the specified resource in storage.
     * PUT = actualización completa (todos los campos). PATCH = actualización parcial (solo los enviados).
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());
        return response()->json(UserResource::make($user->fresh()));
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(int $id)
    {
        $user = User::withTrashed()->find($id);

        if (!$user || !$user->trashed()) {
            return response()->json(['message' => 'Usuario no encontrado entre los eliminados.'], 404);
        }

        $user->restore();
        return response()->json(['message' => 'Usuario restaurado correctamente.'], 200);
    }
}
