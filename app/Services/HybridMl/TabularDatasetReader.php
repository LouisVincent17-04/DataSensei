<?php

namespace App\Services\HybridMl;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use SplFileObject;
use ZipArchive;

class TabularDatasetReader
{
    /**
     * @return array{headers: array<int,string>, rows: array<int,array<string,mixed>>, metadata: array<string,mixed>}
     */
    public function readUploadedFile(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        if (! is_string($path) || ! is_file($path)) {
            throw ValidationException::withMessages(['dataset_file' => 'The uploaded dataset could not be read.']);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        return match ($extension) {
            'csv', 'txt' => $this->readCsv($path),
            'xlsx' => $this->readXlsx($path),
            default => throw ValidationException::withMessages([
                'dataset_file' => 'Only CSV and XLSX files are supported.',
            ]),
        };
    }

    /**
     * @return array{headers: array<int,string>, rows: array<int,array<string,mixed>>, metadata: array<string,mixed>}
     */
    public function readCsv(string $path): array
    {
        $normalizedPath = $this->normalizeUtf8File($path);
        $delimiter = $this->detectDelimiter($normalizedPath);
        $csv = new SplFileObject($normalizedPath, 'r');
        $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $csv->setCsvControl($delimiter);

        $headers = null;
        $rows = [];
        $formulaCellsSanitized = 0;
        $maxRows = max(20, (int) config('hybrid_ml.max_rows', 50000));
        $maxColumns = max(2, (int) config('hybrid_ml.max_columns', 200));

        foreach ($csv as $record) {
            if (! is_array($record) || $this->rowIsEmpty($record)) {
                continue;
            }

            if ($headers === null) {
                $headers = $this->normalizeHeaders($record, $maxColumns);
                continue;
            }

            if (count($record) > count($headers) && ! $this->rowIsEmpty(array_slice($record, count($headers)))) {
                throw ValidationException::withMessages([
                    'dataset_file' => 'A CSV row contains more values than the header row.',
                ]);
            }

            $record = array_pad(array_slice($record, 0, count($headers)), count($headers), null);
            $row = [];
            foreach ($headers as $index => $header) {
                $value = $this->normalizeCell($record[$index] ?? null, $formulaCellsSanitized);
                $row[$header] = $value;
            }

            if (! $this->rowIsEmpty(array_values($row))) {
                $rows[] = $row;
            }

            if (count($rows) > $maxRows) {
                throw ValidationException::withMessages([
                    'dataset_file' => "The dataset may contain at most {$maxRows} data rows.",
                ]);
            }
        }

        if ($normalizedPath !== $path && is_file($normalizedPath)) {
            @unlink($normalizedPath);
        }

        return $this->finalize($headers, $rows, [
            'format' => 'csv',
            'delimiter' => $delimiter === "\t" ? 'tab' : $delimiter,
            'formula_cells_sanitized' => $formulaCellsSanitized,
        ]);
    }

    /**
     * Minimal, dependency-free XLSX reader for the first worksheet.
     * Formulas are rejected instead of being evaluated.
     *
     * @return array{headers: array<int,string>, rows: array<int,array<string,mixed>>, metadata: array<string,mixed>}
     */
    public function readXlsx(string $path): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw ValidationException::withMessages([
                'dataset_file' => 'XLSX support requires the PHP zip extension. Enable extension=zip in php.ini.',
            ]);
        }
        if (! function_exists('simplexml_load_string')) {
            throw ValidationException::withMessages([
                'dataset_file' => 'XLSX support requires the PHP SimpleXML extension.',
            ]);
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw ValidationException::withMessages(['dataset_file' => 'The XLSX workbook is invalid or damaged.']);
        }

        try {
            $this->assertSafeXlsxArchive($zip);
            $sharedStrings = $this->xlsxSharedStrings($zip);
            $worksheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
            if (! is_string($worksheetXml) || $worksheetXml === '') {
                throw ValidationException::withMessages([
                    'dataset_file' => 'The XLSX workbook does not contain a readable first worksheet.',
                ]);
            }

            if (stripos($worksheetXml, '<!DOCTYPE') !== false || stripos($worksheetXml, '<!ENTITY') !== false) {
                throw ValidationException::withMessages(['dataset_file' => 'The XLSX worksheet contains unsafe XML declarations.']);
            }
            $xml = @simplexml_load_string($worksheetXml, 'SimpleXMLElement', LIBXML_NONET | LIBXML_COMPACT);
            if ($xml === false) {
                throw ValidationException::withMessages(['dataset_file' => 'The first XLSX worksheet contains invalid XML.']);
            }

            $matrix = [];
            $formulaCount = 0;
            $maxRows = max(20, (int) config('hybrid_ml.max_rows', 50000)) + 1;
            $maxColumns = max(2, (int) config('hybrid_ml.max_columns', 200));

            $rowNodes = $xml->xpath('//*[local-name()="sheetData"]/*[local-name()="row"]') ?: [];
            foreach ($rowNodes as $rowNode) {
                $values = [];
                $cells = $rowNode->xpath('./*[local-name()="c"]') ?: [];
                foreach ($cells as $cell) {
                    if (($cell->xpath('./*[local-name()="f"]') ?: []) !== []) {
                        $formulaCount++;
                        continue;
                    }

                    $reference = (string) $cell['r'];
                    $index = $this->columnIndexFromReference($reference);
                    if ($index >= $maxColumns) {
                        throw ValidationException::withMessages([
                            'dataset_file' => "The XLSX worksheet may contain at most {$maxColumns} columns.",
                        ]);
                    }

                    $type = (string) $cell['t'];
                    $valueNodes = $cell->xpath('./*[local-name()="v"]') ?: [];
                    $raw = isset($valueNodes[0]) ? (string) $valueNodes[0] : null;
                    $inlineNodes = $cell->xpath('./*[local-name()="is"]//*[local-name()="t"]') ?: [];
                    $inline = implode('', array_map(static fn ($node): string => (string) $node, $inlineNodes));
                    $value = match ($type) {
                        's' => $sharedStrings[(int) ($raw ?? 0)] ?? null,
                        'inlineStr' => $inline !== '' ? $inline : null,
                        'b' => $raw === '1' ? 'true' : 'false',
                        default => $raw,
                    };
                    $values[$index] = $value;
                }

                if ($values !== []) {
                    $last = max(array_keys($values));
                    $dense = array_fill(0, $last + 1, null);
                    foreach ($values as $index => $value) {
                        $dense[$index] = $value;
                    }
                    $matrix[] = $dense;
                }

                if (count($matrix) > $maxRows) {
                    throw ValidationException::withMessages([
                        'dataset_file' => 'The XLSX worksheet exceeds the configured row limit.',
                    ]);
                }
            }

            if ($formulaCount > 0) {
                throw ValidationException::withMessages([
                    'dataset_file' => 'XLSX formulas are not accepted. Replace formulas with their calculated values before uploading.',
                ]);
            }
            if ($matrix === []) {
                throw ValidationException::withMessages(['dataset_file' => 'The XLSX worksheet is empty.']);
            }

            $headers = $this->normalizeHeaders(array_shift($matrix), $maxColumns);
            $rows = [];
            $sanitized = 0;
            foreach ($matrix as $record) {
                $record = array_pad(array_slice($record, 0, count($headers)), count($headers), null);
                $row = [];
                foreach ($headers as $index => $header) {
                    $row[$header] = $this->normalizeCell($record[$index] ?? null, $sanitized);
                }
                if (! $this->rowIsEmpty(array_values($row))) {
                    $rows[] = $row;
                }
            }

            return $this->finalize($headers, $rows, [
                'format' => 'xlsx',
                'worksheet' => 'sheet1',
                'formula_cells_sanitized' => $sanitized,
            ]);
        } finally {
            $zip->close();
        }
    }


    private function assertSafeXlsxArchive(ZipArchive $zip): void
    {
        $maximumEntries = 1500;
        $maximumUncompressed = max(
            50 * 1024 * 1024,
            (int) config('hybrid_ml.max_upload_kilobytes', 10240) * 1024 * 20
        );
        if ($zip->numFiles > $maximumEntries) {
            throw ValidationException::withMessages([
                'dataset_file' => 'The XLSX workbook contains too many internal files.',
            ]);
        }

        $uncompressed = 0;
        $compressed = 0;
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $stat = $zip->statIndex($index);
            if (! is_array($stat)) {
                continue;
            }
            $name = str_replace('\\', '/', (string) ($stat['name'] ?? ''));
            if (str_starts_with($name, '/') || str_contains($name, '../')) {
                throw ValidationException::withMessages([
                    'dataset_file' => 'The XLSX workbook contains an unsafe archive path.',
                ]);
            }
            $uncompressed += max(0, (int) ($stat['size'] ?? 0));
            $compressed += max(0, (int) ($stat['comp_size'] ?? 0));
            if ($uncompressed > $maximumUncompressed) {
                throw ValidationException::withMessages([
                    'dataset_file' => 'The XLSX workbook expands beyond the safe processing limit.',
                ]);
            }
        }

        if ($compressed > 0 && ($uncompressed / $compressed) > 150) {
            throw ValidationException::withMessages([
                'dataset_file' => 'The XLSX workbook has an unsafe compression ratio.',
            ]);
        }
    }

    /** @return array<int,string> */
    private function xlsxSharedStrings(ZipArchive $zip): array
    {
        $contents = $zip->getFromName('xl/sharedStrings.xml');
        if (! is_string($contents) || $contents === '') {
            return [];
        }

        if (stripos($contents, '<!DOCTYPE') !== false || stripos($contents, '<!ENTITY') !== false) {
            return [];
        }
        $xml = @simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NONET | LIBXML_COMPACT);
        if ($xml === false) {
            return [];
        }

        $strings = [];
        $items = $xml->xpath('/*[local-name()="sst"]/*[local-name()="si"]') ?: [];
        foreach ($items as $item) {
            $textNodes = $item->xpath('.//*[local-name()="t"]') ?: [];
            $strings[] = implode('', array_map(static fn ($node): string => (string) $node, $textNodes));
        }

        return $strings;
    }

    private function columnIndexFromReference(string $reference): int
    {
        if (preg_match('/^([A-Z]+)/i', $reference, $match) !== 1) {
            return 0;
        }

        $index = 0;
        foreach (str_split(strtoupper($match[1])) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return max(0, $index - 1);
    }

    /**
     * @param array<int,mixed>|null $headers
     * @param array<int,array<string,mixed>> $rows
     * @param array<string,mixed> $metadata
     * @return array{headers: array<int,string>, rows: array<int,array<string,mixed>>, metadata: array<string,mixed>}
     */
    private function finalize(?array $headers, array $rows, array $metadata): array
    {
        if ($headers === null || $headers === []) {
            throw ValidationException::withMessages(['dataset_file' => 'The dataset is empty.']);
        }
        if (count($rows) < 20) {
            throw ValidationException::withMessages([
                'dataset_file' => 'The dataset must contain at least 20 data rows for machine-learning experiments.',
            ]);
        }

        return ['headers' => $headers, 'rows' => $rows, 'metadata' => $metadata];
    }

    /** @param array<int,mixed> $record @return array<int,string> */
    private function normalizeHeaders(array $record, int $maxColumns): array
    {
        if (count($record) < 2) {
            throw ValidationException::withMessages(['dataset_file' => 'The dataset must contain at least two columns.']);
        }
        if (count($record) > $maxColumns) {
            throw ValidationException::withMessages([
                'dataset_file' => "The dataset may contain at most {$maxColumns} columns.",
            ]);
        }

        $headers = [];
        foreach ($record as $index => $value) {
            $header = trim((string) $value);
            if ($index === 0) {
                $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;
            }
            $header = preg_replace('/\s+/', ' ', $header) ?? $header;
            $header = substr($header, 0, 120);
            if ($header === '') {
                throw ValidationException::withMessages(['dataset_file' => 'Every dataset column must have a header.']);
            }
            $headers[] = $header;
        }

        $normalized = array_map(static fn (string $header): string => strtolower($header), $headers);
        if (count(array_unique($normalized)) !== count($normalized)) {
            throw ValidationException::withMessages(['dataset_file' => 'Dataset column headers must be unique.']);
        }

        return $headers;
    }

    private function normalizeCell(mixed $value, int &$formulaCellsSanitized): mixed
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '' || in_array(strtolower($value), ['null', 'n/a', 'na', 'nan', 'none'], true)) {
            return null;
        }
        if (strlen($value) > 20000) {
            throw ValidationException::withMessages(['dataset_file' => 'A dataset value exceeds the 20,000-character safety limit.']);
        }

        // Prevent spreadsheet formula injection when a stored dataset is later downloaded.
        // Negative numeric values remain numeric and are not modified.
        if (preg_match('/^[=+@]/', $value) === 1 || preg_match('/^-(?!\d+(?:\.\d+)?$)/', $value) === 1) {
            $value = "'".$value;
            $formulaCellsSanitized++;
        }

        if (is_numeric($value) && preg_match('/^0\d+$/', $value) !== 1) {
            return str_contains($value, '.') || stripos($value, 'e') !== false ? (float) $value : (int) $value;
        }

        return $value;
    }

    private function normalizeUtf8File(string $path): string
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw ValidationException::withMessages(['dataset_file' => 'The CSV file could not be read.']);
        }

        $validUtf8 = function_exists('mb_check_encoding')
            ? mb_check_encoding($contents, 'UTF-8')
            : preg_match('//u', $contents) === 1;
        if ($validUtf8) {
            return $path;
        }

        foreach (['Windows-1252', 'ISO-8859-1'] as $encoding) {
            $converted = @iconv($encoding, 'UTF-8//IGNORE', $contents);
            if (is_string($converted) && preg_match('//u', $converted) === 1) {
                $temporary = tempnam(sys_get_temp_dir(), 'datasensei_csv_');
                if ($temporary === false || file_put_contents($temporary, $converted) === false) {
                    break;
                }
                return $temporary;
            }
        }

        throw ValidationException::withMessages([
            'dataset_file' => 'The CSV encoding is invalid. Save the file as UTF-8 and upload it again.',
        ]);
    }

    private function detectDelimiter(string $path): string
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return ',';
        }

        $lines = [];
        while (! feof($handle) && count($lines) < 5) {
            $line = fgets($handle);
            if (is_string($line) && trim($line) !== '') {
                $lines[] = $line;
            }
        }
        fclose($handle);

        $best = ',';
        $bestScore = 0;
        foreach ([',', ';', "\t", '|'] as $delimiter) {
            $counts = array_map(static fn (string $line): int => count(str_getcsv($line, $delimiter)), $lines);
            $score = $counts === [] ? 0 : min($counts);
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $delimiter;
            }
        }

        if ($bestScore < 2) {
            throw ValidationException::withMessages([
                'dataset_file' => 'The CSV delimiter could not be detected. Use comma, semicolon, tab, or pipe-separated columns.',
            ]);
        }

        return $best;
    }

    /** @param array<int,mixed> $row */
    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }
        return true;
    }
}
