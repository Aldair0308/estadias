<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentImportController extends Controller
{
    public function showImportForm()
    {
        return view('students.import');
    }
    public function classroom()
    {
        return view('students.classroom');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        try {
            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getPathname()));
            
            // Verificar los encabezados
            $headers = array_shift($csvData);
            $requiredHeaders = ['name', 'email', 'matricula'];
            $optionalHeaders = ['tel'];
            
            if (count(array_intersect($requiredHeaders, $headers)) !== count($requiredHeaders)) {
                return redirect()->back()->with('error', 'El archivo CSV no contiene todas las columnas requeridas.');
            }

            // Preparar datos para vista previa
            $previewData = [];
            foreach ($csvData as $row) {
                $data = array_combine($headers, $row);
                $password = substr($data['matricula'], 0, 4) . substr($data['email'], 0, 4);
                $previewData[] = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'matricula' => $data['matricula'],
                    'tel' => $data['tel'] ?? null,
                    'password' => $password
                ];
            }

            // Guardar datos en sesión para uso posterior
            session(['preview_data' => $previewData]);

            return view('students.preview', ['students' => $previewData]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    public function confirmImport(Request $request)
    {
        try {
            $previewData = session('preview_data');
            
            if (!$previewData) {
                return redirect()->route('students.import')->with('error', 'No hay datos para importar.');
            }

            $group = $request->input('group');

            foreach ($previewData as $data) {
                // Crear usuario
                \App\Models\User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password'])
                ]);
                
                // Crear estudiante con grupo y teléfono
                Student::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'matricula' => $data['matricula'],
                    'tel' => $data['tel'] ?? null,
                    'group' => $group
                ]);
            }

            // Limpiar datos de sesión
            session()->forget('preview_data');

            return redirect()->route('students.index')->with('success', 'Estudiantes importados correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('students.import')->with('error', 'Error al importar estudiantes: ' . $e->getMessage());
        }
    }
    }
