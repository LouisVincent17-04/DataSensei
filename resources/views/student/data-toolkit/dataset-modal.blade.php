<style>
  [hidden]{display:none!important}.dataset-modal{position:fixed;inset:0;z-index:10000;display:grid;place-items:center;padding:22px}.dataset-modal-backdrop{position:absolute;inset:0;background:rgba(3,8,17,.82);backdrop-filter:blur(5px)}.dataset-modal-dialog{position:relative;width:min(1450px,96vw);height:min(850px,92vh);display:flex;flex-direction:column;background:#0f1a2b;border:1px solid var(--border,#263a56);border-radius:18px;box-shadow:0 30px 90px rgba(0,0,0,.55);overflow:hidden}.dataset-modal-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:20px 22px;border-bottom:1px solid var(--border,#263a56);background:#132036}.dataset-modal-eyebrow{color:var(--accent2,#60a5fa);font-size:.7rem;font-weight:900;letter-spacing:.11em;text-transform:uppercase}.dataset-modal-head h2{margin:5px 0 4px;font-size:1.25rem}.dataset-modal-head p{margin:0;color:var(--muted,#9bacc4);font-size:.8rem}.dataset-modal-close{display:grid;place-items:center;flex:0 0 38px;height:38px;border:1px solid var(--border,#263a56);border-radius:10px;background:#182840;color:#dbeafe;font-size:1.3rem;cursor:pointer}.dataset-modal-table-wrap{flex:1;min-height:0;overflow:auto}.dataset-modal table{width:max-content;min-width:100%;border-collapse:collapse}.dataset-modal th,.dataset-modal td{padding:10px 12px;border-right:1px solid var(--border,#263a56);border-bottom:1px solid var(--border,#263a56);text-align:left;font-size:.78rem;white-space:nowrap;max-width:340px;overflow:hidden;text-overflow:ellipsis}.dataset-modal th{position:sticky;top:0;z-index:2;background:#172740;color:#c5d4e8;text-transform:none;letter-spacing:0}.dataset-modal td{color:#d7e2f0;background:#0f1a2b}.dataset-modal tbody tr:nth-child(even) td{background:#111f33}.dataset-modal-footer{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 18px;border-top:1px solid var(--border,#263a56);background:#132036}.dataset-modal-status{color:var(--muted,#9bacc4);font-size:.8rem}.dataset-modal-pages{display:flex;align-items:center;gap:8px}.dataset-modal-page{min-width:88px;border:1px solid var(--border,#263a56);border-radius:9px;background:#1a2a43;color:#e5edf8;padding:9px 11px;font:800 .78rem Inter,Arial,sans-serif;cursor:pointer}.dataset-modal-page:disabled{opacity:.42;cursor:not-allowed}.dataset-modal-message{padding:28px!important;color:var(--muted,#9bacc4)!important;text-align:center!important}.dataset-modal-trigger{cursor:pointer}@media(max-width:760px){.dataset-modal{padding:8px}.dataset-modal-dialog{width:100%;height:96vh}.dataset-modal-head{padding:16px}.dataset-modal-footer{align-items:stretch;flex-direction:column}.dataset-modal-pages{width:100%}.dataset-modal-page{flex:1}}
</style>

<div id="datasetBrowserModal" class="dataset-modal" hidden aria-hidden="true">
  <div class="dataset-modal-backdrop" data-dataset-modal-close></div>
  <section class="dataset-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="datasetModalTitle">
    <header class="dataset-modal-head">
      <div>
        <div class="dataset-modal-eyebrow">Complete dataset</div>
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
    status.textContent = `Showing rows ${from}–${to} of ${total} · Page ${currentPage} of ${lastPage}`;
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
