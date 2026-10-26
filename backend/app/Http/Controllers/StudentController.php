<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    // GET /api/students : list all students
    public function index()
    {
        return response()->json(Student::all(), 200);
    }

    // POST /api/students : create a student
    public function store(Request $request)
    {
        // Check the incoming data. On failure Laravel returns 422 + errors.
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:students,email',
            'course' => 'required|string|max:100',
        ]);

        // Insert a new row using only the validated data
        $student = Student::create($validated);

        // 201 = Created
        return response()->json($student, 201);
    }

    // GET /api/students/{student} : view one student
    public function show(Student $student)
    {
        return response()->json($student, 200);
    }

    // PUT /api/students/{student} : update a student
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            // email must be unique, but ignore THIS student's own row
            'email'  => ['required', 'email', Rule::unique('students', 'email')->ignore($student->id)],
            'course' => 'required|string|max:100',
        ]);

        $student->update($validated);

        return response()->json($student, 200);
    }

    // DELETE /api/students/{student} : delete a student
    public function destroy(Student $student)
    {
        $student->delete();

        // 204 = No Content (success, nothing to send back)
        return response()->noContent();
    }
}