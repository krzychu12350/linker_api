<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = Report::where('user_id', auth()->id())->with('files')->paginate(10);

        return ReportResource::collection($reports);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Walidacja danych wejściowych
        $validatedData = $request->validate([
            'description' => 'required|string|max:1000', // Opis jest wymagany
            'type' => 'required|string|in:bug,feature,other', // Typ zgłoszenia, np. bug/feature/other
            'files.*' => 'file|mimes:jpg,png,pdf,docx|max:2048', // Opcjonalne pliki
        ]);

        // Tworzenie nowego zgłoszenia
        $report = Report::create([
            'user_id' => auth()->id(),
            'description' => $validatedData['description'],
            'type' => $validatedData['type'],
        ]);

        // Obsługa plików, jeśli zostały przesłane
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('reports_files', 'public'); // Zapis pliku

                // Tworzenie rekordu w tabeli 'files'
                $fileModel = \App\Models\File::create([
                    'path' => $filePath,
                    'name' => $file->getClientOriginalName(),
                ]);

                // Powiązanie raportu z plikami w tabeli 'file_report'
                $report->files()->attach($fileModel->id);
            }
        }

        // Zwracanie odpowiedzi
        return new ReportResource($report);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
