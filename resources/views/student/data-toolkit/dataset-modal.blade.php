<style>
  /* Full dataset browser dialog. Colours, type and radius come from partials.design-system. */
  [hidden]{display:none!important}
  .dataset-modal{position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;padding:16px;overflow-y:auto}
  .dataset-modal-backdrop{position:absolute;inset:0;background:var(--ds-overlay)}
  .dataset-modal-dialog{position:relative;width:min(1450px,100%);height:min(850px,calc(100vh - 32px));height:min(850px,calc(100dvh - 32px));
    display:flex;flex-direction:column;overflow:hidden;background:var(--ds-surface);border:1px solid var(--ds-border-strong);
    border-radius:var(--ds-radius-lg);box-shadow:var(--ds-shadow-lg);font-family:var(--ds-font-sans)}
  .dataset-modal-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:16px 20px;
    border-bottom:1px solid var(--ds-border);background:var(--ds-surface)}
  .dataset-modal-head > div{min-width:0;flex:1 1 auto}
  .dataset-modal-head h2{margin:0;color:var(--ds-text);font-size:1rem;font-weight:600;line-height:1.35;overflow-wrap:anywhere}
  .dataset-modal-head p{margin:4px 0 0;color:var(--ds-text-muted);font-size:.8125rem;line-height:1.5}
  .dataset-modal-close{display:inline-grid;place-items:center;flex:0 0 36px;width:36px;height:36px;padding:0;
    border:1px solid var(--ds-border-strong);border-radius:var(--ds-radius-sm);background:var(--ds-surface-2);color:var(--ds-text);
    font:400 1.25rem/1 var(--ds-font-sans);cursor:pointer;transition:background .12s ease}
  .dataset-modal-close:hover{background:var(--ds-surface-hover)}
  .dataset-modal-table-wrap{flex:1;min-height:0;overflow:auto}
  .dataset-modal table{width:max-content;min-width:100%;border-collapse:collapse;font-variant-numeric:tabular-nums}
  .dataset-modal th,.dataset-modal td{max-width:340px;padding:9px 14px;border-right:1px solid var(--ds-border);border-bottom:1px solid var(--ds-border);
    text-align:left;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .dataset-modal th:last-child,.dataset-modal td:last-child{border-right:0}
  .dataset-modal th{position:sticky;top:0;z-index:2;background:var(--ds-surface-3);color:var(--ds-text-muted);
    font-size:.75rem;font-weight:600;text-transform:none;letter-spacing:0}
  .dataset-modal td{color:var(--ds-text-secondary);font-size:.8125rem}
  .dataset-modal td:first-child{color:var(--ds-text-muted)}
  .dataset-modal tbody tr:hover td{background:rgba(255,255,255,.02)}
  .dataset-modal-footer{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px 16px;padding:12px 20px;
    border-top:1px solid var(--ds-border);background:var(--ds-surface)}
  .dataset-modal-status{min-width:0;color:var(--ds-text-muted);font-size:.8125rem;font-variant-numeric:tabular-nums}
  .dataset-modal-pages{display:flex;align-items:center;gap:8px}
  .dataset-modal-page{min-width:96px;min-height:32px;padding:0 12px;border:1px solid var(--ds-border-strong);border-radius:var(--ds-radius-sm);
    background:var(--ds-surface-2);color:var(--ds-text);font:500 .8125rem/1.2 var(--ds-font-sans);cursor:pointer;transition:background .12s ease}
  .dataset-modal-page:hover:not(:disabled){background:var(--ds-surface-hover)}
  .dataset-modal-page:disabled{opacity:.5;cursor:not-allowed}
  .dataset-modal-message{padding:32px 20px!important;color:var(--ds-text-muted)!important;font-size:.875rem!important;text-align:center!important;
    border-right:0!important;white-space:normal!important}
  .dataset-modal-trigger{cursor:pointer}
  @media(max-width:760px){
    .dataset-modal{padding:12px}
    .dataset-modal-dialog{height:calc(100vh - 24px);height:calc(100dvh - 24px)}
    .dataset-modal-head{padding:14px 16px}
    .dataset-modal-footer{align-items:stretch;flex-direction:column;padding:12px 16px}
    .dataset-modal-pages{width:100%}
    .dataset-modal-page{flex:1}
  }
  @media(prefers-reduced-motion:reduce){.dataset-modal-close,.dataset-modal-page{transition:none}}
</style>

<div id="datasetBrowserModal" class="dataset-modal" hidden aria-hidden="true">
  <div class="dataset-modal-backdrop" data-dataset-modal-close></div>
  <section class="dataset-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="datasetModalTitle">
    <header class="dataset-modal-head">
    @include('partials.page-head', ['pageDescription' => 'Clean, explore, and profile a dataset before you model it.'])
    <meta name="viewport" content="width=device-width, initial-scale=1">
      <div>
        <h2 id="datasetModalTitle">Dataset</h2>
        <p>Browse every row and column. Large datasets are displayed 250 rows at a time to keep the page responsive.</p>
      </div>
      <button class="dataset-modal-close" type="button" aria-label="Close dataset" data-dataset-modal-close>×</button>
    </header>
    <div class="dataset-modal-table-wrap" id="datasetModalTableWrap">
      <table>
        <thead id="datasetModalHead"></thead>
        <tbody id="datasetModalBody"></tbody>
      </table>
    </div>
    <footer class="dataset-modal-footer">
      <div class="dataset-modal-status" id="datasetModalStatus">Loading dataset…</div>
      <div class="dataset-modal-pages">
        <button class="dataset-modal-page" id="datasetModalPrevious" type="button">← Previous</button>
        <button class="dataset-modal-page" id="datasetModalNext" type="button">Next →</button>
      </div>
    </footer>
  </section>
</div>

<script>
(() => {
  const modal = document.getElementById('datasetBrowserModal');
  if (!modal) return;

  const title = document.getElementById('datasetModalTitle');
  const head = document.getElementById('datasetModalHead');
  const body = document.getElementById('datasetModalBody');
  const status = document.getElementById('datasetModalStatus');
  const tableWrap = document.getElementById('datasetModalTableWrap');
  const previous = document.getElementById('datasetModalPrevious');
  const next = document.getElementById('datasetModalNext');
  const closeButton = modal.querySelector('.dataset-modal-close');
  let baseUrl = '';
  let currentPage = 1;
  let lastPage = 1;
  let activeRequest = null;

  function cellText(value) {
    if (value === null || value === undefined || value === '') return 'Missing';
    if (typeof value === 'object') return JSON.stringify(value);
    return String(value);
  }

  function showMessage(message) {
    head.replaceChildren();
    body.replaceChildren();
    const row = document.createElement('tr');
    const cell = document.createElement('td');
    cell.className = 'dataset-modal-message';
    cell.textContent = message;
    row.appendChild(cell);
    body.appendChild(row);
  }

  function renderDataset(payload) {
    title.textContent = payload.dataset || 'Dataset';
    head.replaceChildren();
    body.replaceChildren();

    const headerRow = document.createElement('tr');
    const numberHeader = document.createElement('th');
    numberHeader.textContent = 'Row';
    headerRow.appendChild(numberHeader);
    payload.columns.forEach(column => {
      const header = document.createElement('th');
      header.textContent = column;
      headerRow.appendChild(header);
    });
    head.appendChild(headerRow);

    payload.rows.forEach((rowData, index) => {
      const row = document.createElement('tr');
      const numberCell = document.createElement('td');
      numberCell.textContent = String(payload.pagination.from + index);
      row.appendChild(numberCell);
      payload.columns.forEach(column => {
        const cell = document.createElement('td');
        const value = cellText(rowData[column]);
        cell.textContent = value;
        cell.title = value;
        row.appendChild(cell);
      });
      body.appendChild(row);
    });

    currentPage = payload.pagination.page;
    lastPage = payload.pagination.last_page;
    const from = Number(payload.pagination.from).toLocaleString();
    const to = Number(payload.pagination.to).toLocaleString();
    const total = Number(payload.pagination.total_rows).toLocaleString();
    status.textContent = `Showing rows ${from}–${to} of ${total}, page ${currentPage} of ${lastPage}`;
    previous.disabled = currentPage <= 1;
    next.disabled = currentPage >= lastPage;
    tableWrap.scrollTo({top: 0, left: 0});
  }

  async function loadPage(page) {
    if (!baseUrl) return;
    if (activeRequest) activeRequest.abort();
    const request = new AbortController();
    activeRequest = request;
    previous.disabled = true;
    next.disabled = true;
    status.textContent = 'Loading dataset…';
    showMessage('Loading rows…');

    try {
      const url = new URL(baseUrl, window.location.origin);
      url.searchParams.set('page', String(page));
      const response = await fetch(url, {
        headers: {'Accept': 'application/json'},
        signal: request.signal
      });
      if (!response.ok) throw new Error('Dataset request failed.');
      renderDataset(await response.json());
    } catch (error) {
      if (error.name === 'AbortError') return;
      status.textContent = 'The dataset could not be loaded.';
      showMessage('DataSensei could not display this dataset. Close the modal and try again.');
    } finally {
      if (activeRequest === request) activeRequest = null;
    }
  }

  function openModal(url, datasetTitle) {
    baseUrl = url;
    title.textContent = datasetTitle || 'Dataset';
    modal.hidden = false;
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    closeButton.focus();
    loadPage(1);
  }

  function closeModal() {
    if (activeRequest) activeRequest.abort();
    modal.hidden = true;
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  document.addEventListener('click', event => {
    const trigger = event.target.closest('[data-dataset-modal-url]');
    if (trigger) {
      event.preventDefault();
      openModal(trigger.dataset.datasetModalUrl, trigger.dataset.datasetTitle);
      return;
    }
    if (event.target.closest('[data-dataset-modal-close]')) closeModal();
  });

  previous.addEventListener('click', () => loadPage(Math.max(1, currentPage - 1)));
  next.addEventListener('click', () => loadPage(Math.min(lastPage, currentPage + 1)));
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && !modal.hidden) closeModal();
  });
})();
</script>
