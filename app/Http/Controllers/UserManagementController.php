<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    // Get all users
    public function index()
    {
        $users = User::all();

        return response()->json([
            'users' => $users
        ]);
    }


    // Get one user
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {

            return response()->json([
                'message' => 'User not found.'
            ], 404);

        }

        return response()->json([
            'user' => $user
        ]);
    }


    // Create user
    public function store(Request $request)
    {
        $validated = $request->validate([

            'name' => 'required|string|max:255',

            'email' => 'required|email|unique:users,email',

            'password' => 'required|string|min:8',

            'role' => 'required|in:admin,teacher,student',

        ]);


        $user = User::create([

            'name' => $validated['name'],

            'email' => $validated['email'],

            'password' => Hash::make(
                $validated['password']
            ),

            'role' => $validated['role'],

        ]);


        return response()->json([

            'message' => 'User created successfully.',

            'user' => $user

        ], 201);
    }


    // Update user
    public function update(Request $request, $id)
    {
        $user = User::find($id);


        if (!$user) {

            return response()->json([
                'message' => 'User not found.'
            ], 404);

        }


        $validated = $request->validate([

            'name' => 'required|string|max:255',

            'email' =>
                'required|email|unique:users,email,' . $id,

            'role' =>
                'required|in:admin,teacher,student',

        ]);


        $user->update($validated);


        return response()->json([

            'message' => 'User updated successfully.',

            'user' => $user

        ]);
    }


    // Delete user
    public function destroy($id)
    {
        $user = User::find($id);


        if (!$user) {

            return response()->json([
                'message' => 'User not found.'
            ], 404);

        }


        $user->delete();


        return response()->json([

            'message' => 'User deleted successfully.'

        ]);
    }

    public function toggleStatus($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        $user->is_active = !$user->is_active;

        $user->save();

        return response()->json([
            'message' => $user->is_active
                ? 'User activated successfully.'
                : 'User deactivated successfully.',
            'user' => $user
        ]);
    }

     public function changePassword(Request $request, $id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user->password = Hash::make($validated['password']);

        $user->save();

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }
}