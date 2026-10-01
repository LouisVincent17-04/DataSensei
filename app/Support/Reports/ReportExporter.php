<?php

namespace App\Support\Reports;

use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CSV and PDF downloads of a report (DataSensei Updates 8). Both carry the
 * report title, when and by whom it was generated, the filters used, the
 * headline figures and every table (all rows, not just one page).
 */
final class ReportExporter
{
    /** The most rows one table puts into a download or print. */
    public const MAX_ROWS = 5000;

    /** @param  list<string>  $filterLines */
    public function csv(ReportResult $report, array $filterLines, string $generatedBy, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($report, $filterLines, $generatedBy): void {
            $out = fopen('php://output', 'w');
            // Lets spreadsheet programs read the file as UTF-8.
            fwrite($out, "\xEF\xBB\xBF");

            $this->put($out, ['DataSensei report', $report->title]);
            $this->put($out, ['Generated', now()->format('Y-m-d H:i').' by '.$generatedBy]);
            foreach ($filterLines as $line) {
                $this->put($out, ['Filter', $line]);
            }

            if ($report->summary !== []) {
                $this->put($out, []);
                $this->put($out, ['Summary']);
                foreach ($report->summary as $item) {
                    $this->put($out, [$item['label'], $item['value'], $item['note'] ?? '']);
                }
            }

            foreach ($report->tables as $table) {
                $this->put($out, []);
                $this->put($out, [$table->title]);
                $this->put($out, array_values($table->columns));
                foreach ($table->textRows() as $row) {
                    $this->put($out, $row);
                }
                if ($table->textRows() === []) {
                    $this->put($out, [$table->empty]);
                }
                if ($table->truncated) {
                    $this->put($out, ['Only the first '.self::MAX_ROWS.' rows are included. Narrow the filters to see the rest.']);
                }
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @param  list<string>  $filterLines */
    public function pdf(ReportResult $report, array $filterLines, string $generatedBy, string $filename): Response
    {
        $pdf = new PdfDocument($report->title.' Report', 'DataSensei, generated '.now()->format('M j, Y g:i A').' by '.$generatedBy);
        $pdf->line($report->description, 9, false, true);
        foreach ($filterLines as $line) {
            $pdf->line($line, 9, false, true);
        }

        if ($report->summary !== []) {
            $pdf->heading('Summary');
            $pdf->table(['Measure', 'Value', 'Note'], array_map(
                fn (array $item) => [$item['label'], $item['value'], (string) ($item['note'] ?? '')],
                $report->summary
            ));
        }

        foreach ($report->bars as $group) {
            $pdf->heading($group['title']);
            $pdf->table(['Item', 'Figure'], array_map(fn (array $bar) => [$bar['label'], $bar['text']], $group['items']));
        }

        foreach ($report->tables as $table) {
            $pdf->heading($table->title);
            if ($table->note) {
                $pdf->line($table->note, 8, false, true);
            }
            $rows = $table->textRows();
            if ($rows === []) {
                $pdf->line($table->empty, 9, false, true);

                continue;
            }
            $pdf->table(array_values($table->columns), $rows);
            if ($table->truncated) {
                $pdf->line('Only the first '.self::MAX_ROWS.' rows are included. Narrow the filters to see the rest.', 8, false, true);
            }
        }

        return response($pdf->render(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public static function filename(string $scope, string $key, string $extension): string
    {
        return 'datasensei-'.Str::slug($scope.'-'.$key).'-report-'.now()->format('Y-m-d').'.'.$extension;
    }

    /**
     * One CSV line. Cells that a spreadsheet would run as a formula are
     * written as text.
     *
     * @param  resource  $out
     * @param  list<mixed>  $cells
     */
    private function put($out, array $cells): void
    {
        fputcsv($out, array_map(function ($cell): string {
            $text = (string) $cell;

            return $text !== '' && in_array($text[0], ['=', '+', '-', '@', "\t", "\r"], true) && ! is_numeric($text)
                ? "'".$text
                : $text;
        }, $cells));
    }
}
