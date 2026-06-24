<?php

namespace App\Http\Controllers;

use App\Services\SuperAdminAnalyticsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuperAdminAnalyticsController extends Controller
{
    public function __construct(private readonly SuperAdminAnalyticsService $analytics)
    {
    }

    public function index(Request $request)
    {
        $data = $this->analytics->build(
            $request->query('from'),
            $request->query('to')
        );

        return view('superadmin.analytics.index', $data);
    }

    public function export(Request $request, string $section): StreamedResponse
    {
        $data = $this->analytics->build(
            $request->query('from'),
            $request->query('to')
        );

        $rows = $this->analytics->exportRows($section, $data);
        $filename = $this->analytics->csvFilename($section);

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            if (empty($rows)) {
                fputcsv($handle, ['No records found for this analytics section.']);
            } else {
                foreach ($rows as $row) {
                    fputcsv($handle, $row);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
