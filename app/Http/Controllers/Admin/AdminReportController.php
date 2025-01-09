<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    {
        $reports = Report::with('user', 'files')->paginate(10);
        return ReportResource::collection($reports);
    }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'type' => 'required|in:illegal_content,spam,abuse,other',
            'files.*' => 'file|mimes:jpg,png,pdf|max:2048' // Obsługa wielu plików
        ]);

        $report = Report::create([
            'user_id' => auth()->id(),
            'description' => $validated['description'],
            'type' => $validated['type'],
        ]);

        // Obsługa plików
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('reports'); // Zapis plików w storage/app/reports
                $fileModel = File::create(['path' => $path]);
                $report->files()->attach($fileModel->id);
            }
        }

        return response()->json([
            'message' => 'Report created successfully',
            'report' => $report->load('files')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $report)
    {
        return response()->json($report->load(['user', 'files']));
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
        $report->delete();
        return response()->json(['message' => 'Report deleted successfully']);
    }
}
