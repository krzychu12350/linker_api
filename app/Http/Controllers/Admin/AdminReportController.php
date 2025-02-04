<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use App\Enums\ReportStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminReportController extends Controller
{
    /**
     * Fetch reports with pagination.
     */
    public function index(Request $request)
    {
        $reports = Report::with(['user', 'files']) // Load associated user data
        ->latest()                 // Order by latest reports
        ->paginate(10);            // Paginate results

        return response()->json([
            'data' => $reports,
        ]);
    }

    /**
     * Show a single report with user and files.
     */
    public function show($id)
    {
        // Fetch the report with related user and files
        $report = Report::with(['user', 'files'])->findOrFail($id);

        return response()->json([
            'message' => 'Report fetched successfully!',
            'data' => $report,
        ]);
    }

    /**
     * Update the status of a report.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', ReportStatus::values()),
        ]);

        $report = Report::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        return response()->json([
            'message' => 'Report status updated successfully!',
            'data' => $report,
        ]);
    }

    /**
     * Delete a report and its related files (if any).
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        // Detach related files before deleting the report
        $report->files()->detach();

        // Delete the report
        $report->delete();

        return response()->json([
            'message' => 'Report deleted successfully!',
        ], 204);
    }
}
