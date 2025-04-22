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
            
            if (count(array_intersect($requiredHeaders, $headers)) !== count($requiredHeaders)) {
                return redirect()->back()->with('error', 'El archivo CSV no contiene todas las columnas requeridas.');
            }

            // Procesar cada fila
            foreach ($csvData as $row) {
                $data = array_combine($headers, $row);
                
                Student::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'matricula' => $data['matricula']
                ]);
            }

            return redirect()->back()->with('success', 'Estudiantes importados correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }
}