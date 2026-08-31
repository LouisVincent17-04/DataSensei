<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $tos->title }} — Set Distribution</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  @include('instructor.tos.partials.styles')
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap">
      <div class="top">
        <div>
          <div class="kicker">Step 2 of 3</div>
          <h1 class="title ds-page-title">Set Distribution</h1>
          <p class="subtitle">{{ $tos->title }} · {{ $tos->classRoom->name ?? 'Template / no class' }} · {{ $tos->coverage_label }}{{ $module ? ' — '.$module->title : '' }}</p>
        </div>
        <div class="actions">
          <a class="btn secondary" href="{{ route('instructor.tos.index') }}">TOS Library</a>
          <a class="btn secondary" href="{{ route('instructor.tos.review', $tos) }}">Review</a>
        </div>
      </div>

      @include('instructor.tos.partials.wizard', ['currentStep' => 2])

      @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif
      @if(session('error'))<div class="alert error">{{ session('error') }}</div>@endif
      @if($errors->any())
        <div class="alert error">
          @foreach($errors->all() as $error){{ $error }}<br>@endforeach
        </div>
      @endif

      @php($statusClass = $status['code'] === 'complete' ? 'good' : ($status['code'] === 'invalid' ? 'bad' : 'warn'))
      <div class="status-panel {{ $statusClass }}" id="live-status">
        <div>
          <strong id="live-status-label">{{ $status['label'] }}</strong>
          <div class="muted" id="live-status-message">{{ $status['message'] }}</div>
        </div>
        <span class="badge {{ $statusClass }}" id="live-status-count">{{ $tos->rows->sum('item_count') }} / {{ $tos->total_items ?: $tos->rows->sum('item_count') }}</span>
      </div>

      @if($tos->assessments_count > 0)
        <div class="alert warn" style="margin-top:16px">This TOS is linked to an assessment. Its blueprint is now read-only so existing assessment items stay consistent.</div>
      @endif

      @if($isModernBlueprint)
        <div class="card" style="margin-top:16px">
          <div class="section-title">
            <div>
              <div class="kicker">Auto-Fill TOS</div>
              <h2>Generate Suggested Distribution</h2>
              <div class="muted">Use a simple cognitive balance, then customize the matrix if needed.</div>
            </div>
          </div>

          <form method="POST" action="{{ route('instructor.tos.suggested-distribution', $tos) }}">
            @csrf
            <div class="grid grid-2">
              @foreach($cognitiveLevels as $slug => $definition)
                <div class="field">
                  <label for="suggest-{{ $slug }}">{{ $definition['label'] }} %</label>
                  <input class="input suggested-percent" id="suggest-{{ $slug }}" type="number" min="0" max="100" name="{{ $slug }}" value="{{ old($slug, $suggestedDistribution[$slug]) }}" {{ $tos->assessments_count > 0 ? 'disabled' : '' }} required>
                </div>
              @endforeach
            </div>

            <div style="margin-top:16px">
              <div class="field"><label>Competency Weights</label></div>
              <div class="grid grid-2">
                @foreach($matrix['groups'] as $group)
                  <div class="field">
                    <label style="text-transform:none;letter-spacing:0;color:var(--muted)">{{ $group['title'] }}</label>
                    <input class="input" type="number" step="0.1" min="0" max="1000" name="weights[{{ $group['key'] }}]" value="{{ old('weights.'.$group['key'], $group['weight']) }}" {{ $tos->assessments_count > 0 ? 'disabled' : '' }}>
                  </div>
                @endforeach
              </div>
              <div class="muted small" style="margin-top:7px">Weights are used proportionally. They do not need to total exactly 100 because DataSensei normalizes them automatically.</div>
            </div>

            <div class="actions" style="margin-top:14px">
              @if($tos->assessments_count === 0)
                <button class="btn" type="submit">✨ Apply Suggested Distribution</button>
                <a class="btn secondary" href="#tos-matrix">Customize Matrix</a>
              @endif
              <span class="muted small">Percentages must total 100%.</span>
            </div>
          </form>

          <details class="help" style="margin-top:14px">
            <summary>What do these cognitive levels mean?</summary>
            <div class="definition-grid">
              @foreach($cognitiveLevels as $definition)
                <div class="definition"><strong>{{ $definition['label'] }}</strong><span>{{ $definition['explanation'] }}</span></div>
              @endforeach
            </div>
          </details>
        </div>

        <form method="POST" action="{{ route('instructor.tos.distribution.update', $tos) }}" class="card" id="tos-matrix">
          @csrf
          @method('PATCH')
          <div class="section-title">
            <div>
              <div class="kicker">TOS Matrix</div>
              <h2>Learning Competency Distribution</h2>
              <div class="muted">Weights and totals recalculate automatically as you edit item counts.</div>
            </div>
            <span class="badge">Target: {{ $matrix['target_items'] }} items</span>
          </div>

          <div class="table-wrap">
            <table class="table" data-target="{{ $matrix['target_items'] }}" id="distribution-table">
              <thead>
                <tr>
                  <th>Learning Competency</th>
                  <th>Weight</th>
                  @foreach($cognitiveLevels as $definition)<th>{{ $definition['label'] }}</th>@endforeach
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
              @foreach($matrix['groups'] as $groupIndex => $group)
                <tr data-competency-row>
                  <td style="min-width:260px">
                    <strong>{{ $group['title'] }}</strong>
                    @if($group['objective'])<div class="muted small" style="margin-top:5px">{{ $group['objective'] }}</div>@endif
                  </td>
                  <td class="weight-cell"><strong data-row-weight>{{ $group['weight'] }}%</strong></td>
                  @foreach($cognitiveLevels as $slug => $definition)
                    @php($cell = $group['cells'][$slug])
                    <td>
                      @if($cell)
                        <input
                          class="input number-input distribution-count"
                          type="number"
                          min="0"
                          max="500"
                          name="counts[{{ $cell->id }}]"
                          value="{{ old('counts.'.$cell->id, $cell->item_count) }}"
                          data-cognitive="{{ $slug }}"
                          {{ $tos->assessments_count > 0 ? 'disabled' : '' }}
                          required>
                      @else
                        <span class="muted">—</span>
                      @endif
                    </td>
                  @endforeach
                  <td><strong data-row-total>{{ $group['total'] }}</strong></td>
                </tr>
              @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <th>TOTAL</th>
                  <th><span id="live-weight-total">100%</span></th>
                  @foreach($cognitiveLevels as $slug => $definition)
                    <th><span data-column-total="{{ $slug }}">{{ $matrix['column_totals'][$slug] }}</span></th>
                  @endforeach
                  <th><span id="live-grand-total">{{ $matrix['assigned_items'] }}</span> / {{ $matrix['target_items'] }}</th>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="callout" style="margin-top:14px">
            The TOS is only the blueprint. Question type, question text, choices, answers, images, and rubrics are handled later in the existing Question Builder.
          </div>

          <div class="footer-actions">
            <a class="btn secondary" href="{{ route('instructor.tos.index') }}">← Back</a>
            @if($tos->assessments_count === 0)
              <div class="actions">
                <button class="btn secondary" type="submit" name="next" value="stay">Save</button>
                <button class="btn" type="submit" name="next" value="review">Save & Continue →</button>
              </div>
            @else
              <a class="btn" href="{{ route('instructor.tos.review', $tos) }}">Continue to Review →</a>
            @endif
          </div>
        </form>
      @else
        <div class="alert warn" style="margin-top:16px">
          This TOS was created with DataSensei's previous difficulty-based format. It is preserved so existing records are not silently rewritten. New TOS records use the simpler Remember / Understand / Apply / Analyze matrix.
        </div>

        <div class="card table-wrap">
          <div class="section-title">
            <div><h2>Legacy TOS Allocations</h2><div class="muted">You can still edit this older blueprint until it is linked to an assessment.</div></div>
          </div>
          <table class="table">
            <thead><tr><th>Topic / Objective</th><th>Difficulty</th><th>Cognitive Level</th><th>Items</th><th>Points/Item</th><th>Update</th></tr></thead>
            <tbody>
            @forelse($tos->rows as $row)
              <tr>
                <td>
                  <form id="row-{{ $row->id }}" method="POST" action="{{ route('instructor.tos.rows.update', [$tos, $row]) }}">
                    @csrf
                    @method('PATCH')
                    <div class="field"><label>Topic</label><input class="input" name="topic_title" value="{{ $row->topic_title }}" {{ $tos->assessments_count > 0 ? 'disabled' : '' }} required></div>
                    <div class="field" style="margin-top:8px"><label>Subtopic</label><input class="input" name="subtopic_title" value="{{ $row->subtopic_title }}" {{ $tos->assessments_count > 0 ? 'disabled' : '' }}></div>
                    <div class="field" style="margin-top:8px"><label>Learning Objective</label><textarea class="textarea" name="learning_objective" {{ $tos->assessments_count > 0 ? 'disabled' : '' }}>{{ $row->learning_objective }}</textarea></div>
                  </form>
                </td>
                <td><span class="badge">{{ ucwords(str_replace('-', ' ', $row->difficulty_slug)) }}</span></td>
                <td><input form="row-{{ $row->id }}" class="input" name="cognitive_level" value="{{ $row->cognitive_level }}" {{ $tos->assessments_count > 0 ? 'disabled' : '' }}></td>
                <td><input form="row-{{ $row->id }}" class="input number-input" type="number" min="0" max="200" name="item_count" value="{{ $row->item_count }}" {{ $tos->assessments_count > 0 ? 'disabled' : '' }}></td>
                <td><input form="row-{{ $row->id }}" class="input number-input" type="number" min="1" max="1000" name="default_points" value="{{ $row->default_points }}" {{ $tos->assessments_count > 0 ? 'disabled' : '' }}></td>
                <td>@if($tos->assessments_count === 0)<button form="row-{{ $row->id }}" class="btn secondary" type="submit">Save</button>@else<span class="muted">Read only</span>@endif</td>
              </tr>
            @empty
              <tr><td colspan="6" class="muted">No TOS rows found.</td></tr>
            @endforelse
            </tbody>
          </table>
          <div class="footer-actions"><span></span><a class="btn" href="{{ route('instructor.tos.review', $tos) }}">Continue to Review →</a></div>
        </div>
      @endif
    </div>
  </main>
</div>

@if($isModernBlueprint)
<script>
(() => {
  const table = document.getElementById('distribution-table');
  if (!table) return;
  const target = Number(table.dataset.target || 0);
  const inputs = [...table.querySelectorAll('.distribution-count')];
  const cognitiveKeys = @json(array_keys($cognitiveLevels));
  const statusPanel = document.getElementById('live-status');
  const statusLabel = document.getElementById('live-status-label');
  const statusMessage = document.getElementById('live-status-message');
  const statusCount = document.getElementById('live-status-count');

  const n = value => Math.max(0, Number.parseInt(value || '0', 10) || 0);

  function recalc() {
    let grand = 0;
    const columns = Object.fromEntries(cognitiveKeys.map(key => [key, 0]));

    table.querySelectorAll('[data-competency-row]').forEach(row => {
      let rowTotal = 0;
      row.querySelectorAll('.distribution-count').forEach(input => {
        const value = n(input.value);
        rowTotal += value;
        grand += value;
        columns[input.dataset.cognitive] += value;
      });
      row.querySelector('[data-row-total]').textContent = rowTotal;
      row.querySelector('[data-row-weight]').textContent = target > 0 ? `${((rowTotal / target) * 100).toFixed(1)}%` : '0%';
    });

    cognitiveKeys.forEach(key => {
      const el = table.querySelector(`[data-column-total="${key}"]`);
      if (el) el.textContent = columns[key];
    });

    document.getElementById('live-grand-total').textContent = grand;
    document.getElementById('live-weight-total').textContent = target > 0 ? `${((grand / target) * 100).toFixed(1)}%` : '0%';
    statusCount.textContent = `${grand} / ${target}`;

    statusPanel.classList.remove('good', 'warn', 'bad');
    statusCount.classList.remove('good', 'warn', 'bad');

    if (grand === target) {
      statusPanel.classList.add('good');
      statusCount.classList.add('good');
      statusLabel.textContent = 'Complete';
      statusMessage.textContent = `${grand} / ${target} items assigned. Your blueprint is ready.`;
    } else if (grand > target) {
      statusPanel.classList.add('bad');
      statusCount.classList.add('bad');
      statusLabel.textContent = 'Invalid';
      statusMessage.textContent = `${grand} / ${target} items assigned. Reduce the distribution by ${grand - target} item(s).`;
    } else {
      statusPanel.classList.add('warn');
      statusCount.classList.add('warn');
      statusLabel.textContent = 'Needs Attention';
      statusMessage.textContent = `${grand} / ${target} items assigned. ${target - grand} item(s) remaining.`;
    }
  }

  inputs.forEach(input => input.addEventListener('input', recalc));
  recalc();
})();
</script>
@endif
</body>
</html>
