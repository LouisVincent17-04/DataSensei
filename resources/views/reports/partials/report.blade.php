{{--
    One report page, shared by Admin Reports and Instructor Reports
    (DataSensei Updates 8): the list of reports, the filters that apply to this
    report, print and export links, the headline figures, simple bars and the
    tables with their own pages.

    Expects:
      $catalog        report key => ['title' => ..., 'filters' => [...]]
      $current        this report's key
      $result         App\Support\Reports\ReportResult
      $filters        App\Support\Reports\ReportFilters
      $showRoute      route name of a report page (parameter "report")
      $exportRoute    route name of the export (parameters "report", "format")
      $choices        filter menus: roles, statuses, types, classes, modules,
                      students, actions (only those this report uses)
      $searchHint     placeholder of the search box
--}}
@php
  $fields = $catalog[$current]['filters'] ?? [];
  $query = $filters->query();
  $has = fn (string $field) => in_array($field, $fields, true);
  $select = function (string $name, string $label, array $options, $selected, string $all) {
      return compact('name', 'label', 'options', 'selected', 'all');
  };
  $menus = array_values(array_filter([
      $has('class') && ! empty($choices['classes']) ? $select('class_id', 'Class', $choices['classes'], $filters->classId, 'All classes') : null,
      $has('module') && ! empty($choices['modules']) ? $select('module_id', 'Module', $choices['modules'], $filters->moduleId, 'All modules') : null,
      $has('student') && ! empty($choices['students']) ? $select('student_id', 'Student', $choices['students'], $filters->studentId, 'All students') : null,
      $has('role') && ! empty($choices['roles']) ? $select('role', 'Role', $choices['roles'], $filters->role, 'All roles') : null,
      $has('type') && ! empty($choices['types']) ? $select('type', $choices['typeLabel'] ?? 'Type', $choices['types'], $filters->type, 'All') : null,
      $has('status') && ! empty($choices['statuses']) ? $select('status', 'Status', $choices['statuses'], $filters->status, 'All statuses') : null,
      $has('action') && ! empty($choices['actions']) ? $select('action', 'Action', $choices['actions'], $filters->action, 'All actions') : null,
  ]));
@endphp

@include('reports.partials.styles')

<div class="rp">
  <nav class="rp-nav" aria-label="Reports">
    @foreach($catalog as $key => $report)
      <a href="{{ route($showRoute, ['report' => $key]) }}" class="rp-nav-link {{ $key === $current ? 'is-current' : '' }}" @if($key === $current) aria-current="page" @endif>{{ $report['title'] }}</a>
    @endforeach
  </nav>

  <div class="rp-head">
    <div class="rp-head-text">
      <h2 class="rp-title">{{ $result->title }}</h2>
      <p class="rp-description">{{ $result->description }}</p>
    </div>
    <div class="rp-exports">
      <a class="rp-btn rp-btn-secondary" href="{{ route($exportRoute, ['report' => $current, 'format' => 'print'] + $query) }}" target="_blank" rel="noopener">Print</a>
      <a class="rp-btn rp-btn-secondary" href="{{ route($exportRoute, ['report' => $current, 'format' => 'pdf'] + $query) }}">Export PDF</a>
      <a class="rp-btn rp-btn-secondary" href="{{ route($exportRoute, ['report' => $current, 'format' => 'csv'] + $query) }}">Export CSV</a>
    </div>
  </div>

  @if($fields !== [])
    <form method="GET" action="{{ route($showRoute, ['report' => $current]) }}" class="rp-filters" role="search">
      @if($has('date'))
        <div class="rp-field">
          <label for="rp-from">From</label>
          <input id="rp-from" class="rp-input" type="date" name="from" value="{{ $filters->from?->format('Y-m-d') }}">
        </div>
        <div class="rp-field">
          <label for="rp-to">To</label>
          <input id="rp-to" class="rp-input" type="date" name="to" value="{{ $filters->to?->format('Y-m-d') }}">
        </div>
      @endif
      @if($has('search'))
        <div class="rp-field rp-field-wide">
          <label for="rp-search">Search</label>
          <input id="rp-search" class="rp-input" type="search" name="q" value="{{ $filters->search }}" maxlength="100" placeholder="{{ $searchHint ?? 'Search' }}">
        </div>
      @endif
      @foreach($menus as $menu)
        <div class="rp-field">
          <label for="rp-{{ $menu['name'] }}">{{ $menu['label'] }}</label>
          <select id="rp-{{ $menu['name'] }}" class="rp-input" name="{{ $menu['name'] }}">
            <option value="">{{ $menu['all'] }}</option>
            @foreach($menu['options'] as $value => $text)
              <option value="{{ $value }}" @selected((string) $menu['selected'] === (string) $value)>{{ $text }}</option>
            @endforeach
          </select>
        </div>
      @endforeach
      <div class="rp-filter-actions">
        <button type="submit" class="rp-btn">Apply</button>
        @if($query !== [])
          <a class="rp-btn rp-btn-secondary" href="{{ route($showRoute, ['report' => $current]) }}">Clear</a>
        @endif
      </div>
    </form>
  @endif

  @if($errors->any())
    <p class="rp-error" role="alert">{{ $errors->first() }}</p>
  @endif

  @if($result->summary !== [])
    <section class="rp-summary" aria-label="Summary">
      @foreach($result->summary as $item)
        <div class="rp-tile">
          <span class="rp-tile-label">{{ $item['label'] }}</span>
          <strong class="rp-tile-value">{{ $item['value'] }}</strong>
          @if(! empty($item['note']))<span class="rp-tile-note">{{ $item['note'] }}</span>@endif
        </div>
      @endforeach
    </section>
  @endif

  @foreach($result->bars as $group)
    @include('reports.partials.bars', ['group' => $group])
  @endforeach

  @foreach($result->tables as $table)
    @include('reports.partials.table', ['table' => $table])
  @endforeach
</div>
