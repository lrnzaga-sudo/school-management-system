<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = Teacher::all();

        return response()->json([
            'teachers' => $teachers
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',

            'employee_number' => 'required|string|max:50|unique:teachers,employee_number',

            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',

            'birth_date' => 'nullable|date',

            'gender' => 'required|string|in:Male,Female',

            'address' => 'required|string',

            'phone' => 'required|string|max:20',
        ]);

        $teacher = Teacher::create([
            'user_id' => $validated['user_id'],
            'employee_number' => $validated['employee_number'],
            
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],

            'birth_date' => $validated['birth_date'],

            'gender' => $validated['gender'],

            'address' => $validated['address'],

            'phone' => $validated['phone']
        ]);

        return response()->json([
            'message' => 'Teacher created successfully',
            'teacher' => $teacher
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $teacher = Teacher::findOrFail($id);

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher not found'
            ], 404);
        }

        return response()->json([
            'teacher' => $teacher
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $teacher = Teacher::findOrFail($id);

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher not found'
            ], 404);
        }

        $validated = $request->validate([
            'employee_number' => 'required|string|max:50|unique:teachers,employee_number,' . $teacher->id,

            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',

            'birth_date' => 'nullable|date',

            'gender' => 'required|string|in:Male,Female',

            'address' => 'required|string',

            'phone' => 'required|string|max:20',
        ]);

        $teacher->update($validated);

        return response()->json([
            'message' => 'Teacher updated successfully',
            'teacher' => $teacher
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = Teacher::findOrFail($id);

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher not found'
            ], 404);
        }

        $teacher->delete();

        return response()->json([
            'message' => 'Teacher deleted successfully'
        ], 200);
    }
}
