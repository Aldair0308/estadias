<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:tutor', ['except' => ['classroom']]);
    }

    public function classroom()
    {
        return view('students.classroom');
    }

    public function index()
    {
        $students = Student::orderBy('name')->paginate(10);
        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'group' => 'required|string|max:50',
            'matricula' => 'required|integer|unique:students',
            'tel' => 'required|string|max:20',
            'email' => 'required|email|unique:students',
        ]);

        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Estudiante creado exitosamente.');
    }

    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'group' => 'required|string|max:50',
            'matricula' => ['required', 'integer', Rule::unique('students')->ignore($student->id)],
            'tel' => 'required|string|max:20',
            'email' => ['required', 'email', Rule::unique('students')->ignore($student->id)],
        ]);

        $student->update($validated);

        return redirect()->route('students.index')
            ->with('success', 'Estudiante actualizado exitosamente.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Estudiante eliminado exitosamente.');
    }
}