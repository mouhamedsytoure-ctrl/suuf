<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::with('roles:name')
            ->get(['id', 'name', 'email', 'telephone', 'region', 'created_at']);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'role' => ['required', 'string', 'in:chef_exploitation,producteur,agent_terrain'],
        ]);

        $user = User::findOrFail($id);
        $user->syncRoles($data['role']);

        return $user->load('roles:name');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé.']);
    }
}
