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
        // Check if the authenticated user is an admin
        if (auth()->user()->isAdmin()) {
            $reports = Report::with('files')->paginate(10); // Admin sees all reports
        } else {
            $reports = Report::where('user_id', auth()->id())->with('files')->paginate(10); // Regular user sees their own reports
        }

        return ReportResource::collection($reports);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'description' => 'required|string|max:1000',
            'type' => 'required|string|in:bug,feature,other',
            'files.*' => 'file|mimes:jpg,png,pdf,docx|max:2048',
        ]);

        // Creating new report
        $report = Report::create([
            'user_id' => auth()->id(),
            'description' => $validatedData['description'],
            'type' => $validatedData['type'],
        ]);

        // Handling files if any
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('reports_files', 'public');
                $fileModel = \App\Models\File::create([
                    'path' => $filePath,
                    'name' => $file->getClientOriginalName(),
                ]);
                $report->files()->attach($fileModel->id);
            }
        }

        return new ReportResource($report);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $report = Report::findOrFail($id);

        // Check if the authenticated user is an admin or the owner of the report
        if (auth()->user()->isAdmin() || $report->user_id === auth()->id()) {
            return new ReportResource($report);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // This method can be handled similarly to `show` with necessary checks for editing.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $report = Report::findOrFail($id);

        // Only admin can delete the report
        if (auth()->user()->isAdmin()) {
            $report->delete();
            return response()->json(['message' => 'Report deleted successfully']);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
