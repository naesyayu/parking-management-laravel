<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger;

class RoleController extends Controller
{
     use ActivityLogger;

    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_user' => 'required|unique:roles,role_user',
        ]);

        $role = Role::create([
            'role_user' => $request->role_user,
        ]);

        $this->logCreate($role, 'role');

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan');
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'role_user' => 'required|unique:roles,role_user,' 
                . $role->id_role . ',id_role',
        ]);

        $originalData = $role->toArray();

        $role->update([
            'role_user' => $request->role_user,
        ]);

        $this->logUpdate($role, 'role', $originalData);

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil diperbarui');
    }


    // 🔹 BACKUP / TRASH
    public function trash()
    {
        $roles = Role::onlyTrashed()
        ->orderBy('id_role', 'desc')
        ->get();


        return view('roles.trash', compact('roles'));
    }


    // 🔹 RESTORE
    public function restore($id)
    {
        $role = Role::onlyTrashed()->findOrFail($id);
        $role->restore();

        $this->logRestore($role, 'role');

        return redirect()
        ->route('roles.trash')
        ->with('success', 'Role berhasil dikembalikan');
    }

    public function destroy(Role $role)
    {
        $this->logDelete($role, 'role');
    
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dihapus');
    }
}