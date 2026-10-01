{{--
    Printable report (DataSensei Updates 8): every row of every table on
    plain white pages. Opens the browser's print dialog when it loads.

    Expects: $result (ReportResult), $filterLines (list of text),
    $generatedBy (name), $backUrl.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $result->title }} Report — DataSensei</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; padding: 28px 32px; background: #fff; color: #111827; font: 13px/1.5 Arial, Helvetica, sans-serif; }
    h1 { margin: 0 0 4px; font-size: 20px; }
    h2 { margin: 24px 0 8px; font-size: 15px; }
    p { margin: 0 0 4px; }
    .muted { color: #4b5563; }
    .bar { display: flex; gap: 8px; margin-bottom: 16px; }
    .bar button, .bar a { padding: 6px 12px; border: 1px solid #9ca3af; border-radius: 6px; background: #fff; color: #111827; font: inherit; text-decoration: none; cursor: pointer; }
    table { width: 100%; margin-top: 6px; border-collapse: collapse; }
    th, td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; vertical-align: top; }
    th { background: #f3f4f6; font-size: 12px; }
    .summary td:first-child { width: 40%; }
    @media print { .bar { display: none; } body { padding: 0; } h2 { break-after: avoid; } tr { break-inside: avoid; } }
  </style>
</head>
<body>
  <div class="bar">
    <button type="button" onclick="window.print()">Print</button>
    <a href="{{ $backUrl }}">Back to the report</a>
  </div>

  <h1>{{ $result->title }} Report</h1>
  <p class="muted">DataSensei, generated {{ now()->format('M j, Y g:i A') }} by {{ $generatedBy }}</p>
  <p class="muted">{{ $result->description }}</p>
  @foreach($filterLines as $line)
    <p class="muted">{{ $line }}</p>
  @endforeach

  @if($result->summary !== [])
    <h2>Summary</h2>
    <table class="summary">
      @foreach($result->summary as $item)
        <tr><td>{{ $item['label'] }}</td><td><strong>{{ $item['value'] }}</strong>@if(! empty($item['note'])) <span class="muted">({{ $item['note'] }})</span>@endif</td></tr>
      @endforeach
    </table>
  @endif

  @foreach($result->bars as $group)
    <h2>{{ $group['title'] }}</h2>
    <table class="summary">
      @foreach($group['items'] as $bar)
        <tr><td>{{ $bar['label'] }}</td><td>{{ $bar['text'] }}</td></tr>
      @endforeach
    </table>
  @endforeach

  @foreach($result->tables as $table)
    <h2>{{ $table->title }}</h2>
    @if($table->note)<p class="muted">{{ $table->note }}</p>@endif
    <table>
      <thead><tr>@foreach($table->columns as $heading)<th>{{ $heading }}</th>@endforeach</tr></thead>
      <tbody>
        @forelse($table->textRows() as $row)
          <tr>@foreach($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
        @empty
          <tr><td colspan="{{ count($table->columns) }}" class="muted">{{ $table->empty }}</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($table->truncated)<p class="muted">Only the first {{ \App\Support\Reports\ReportExporter::MAX_ROWS }} rows are shown. Narrow the filters to see the rest.</p>@endif
  @endforeach

  <script>window.addEventListener('load', function () { window.setTimeout(function () { window.print(); }, 300); });</script>
</body>
</html>
