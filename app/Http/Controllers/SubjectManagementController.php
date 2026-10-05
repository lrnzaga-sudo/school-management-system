<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::all();

        return response()->json([
            'subjects' => $subjects
        ]);
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
            'subject_code' => 'required|string|max:50|unique:subjects,subject_code',
            'subject_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $subject = Subject::create([
            'subject_code' => $validated['subject_code'],
            'subject_name' => $validated['subject_name'],
            'description' => $validated['description']
        ]);

        return response()->json([
            'message' => 'Subject created successfully',
            'subject' => $subject
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subject = Subject::findOrFail($id);

        if (!$subject) {
            return response()->json([
                'message' => 'Subject not found'
            ], 404);
        }

        return response()->json([
            'subject' => $subject
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
        $subject = Subject::findOrFail($id);

        if (!$subject) {
            return response()->json([
                'message' => 'Subject not found'
            ], 404);
        }

        $validated = $request->validate([
            'subject_code' => 'required|string|max:50|unique:subjects,subject_code,' . $subject->id,
            'subject_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $subject->update($validated);

        return response()->json([
            'message' => 'Subject updated successfully',
            'subject' => $subject
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subject = Subject::findOrFail($id);

        if (!$subject) {
            return response()->json([
                'message' => 'Subject not found'
            ], 404);
        }

        $subject->delete();

        return response()->json([
            'message' => 'Subject deleted successfully'
        ], 200);
    }
}
