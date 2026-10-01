{{--
    One report table with its heading, note, rows and pages (DataSensei
    Updates 8). Expects $table (App\Support\Reports\ReportTable).
--}}
  @php $paginator = $table->paginator(); $rows = $table->rowList(); @endphp
  <section class="rp-panel rp-table-panel" id="{{ $table->key }}">
    <div class="rp-panel-head">
      <h3 class="rp-panel-title">{{ $table->title }}</h3>
      <span class="rp-count">{{ number_format($table->total()) }} {{ $table->total() === 1 ? 'row' : 'rows' }}</span>
    </div>
    @if($table->note)
      <p class="rp-note">{{ $table->note }}</p>
    @endif
    <div class="rp-table-wrap">
      <table class="rp-table">
        <thead>
          <tr>
            @foreach($table->columns as $column => $heading)
              <th scope="col">{{ $heading }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $row)
            <tr>
              @foreach($table->columns as $column => $heading)
                @php $tone = $row['_tone'][$column] ?? null; @endphp
                <td data-label="{{ $heading }}" @class(['rp-good' => $tone === 'good', 'rp-warn' => $tone === 'warn', 'rp-bad' => $tone === 'bad'])>
                  @if($loop->first && ! empty($row['_url']))
                    <a href="{{ $row['_url'] }}" class="rp-link">{{ $row[$column] ?? '' }}</a>
                  @else
                    {{ $row[$column] ?? '' }}
                  @endif
                </td>
              @endforeach
            </tr>
          @empty
            <tr><td class="rp-empty" colspan="{{ count($table->columns) }}">{{ $table->empty }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($paginator && $paginator->hasPages())
      <div class="rp-pages">{{ $paginator->fragment($table->key)->links('vendor.pagination.admin') }}</div>
    @endif
  </section>
