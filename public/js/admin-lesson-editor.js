/*
 * Lesson block editor (Module Content Manager).
 *
 * Keeps the lesson as an array of plain block objects, draws one card per
 * block on the left, and posts the array to the preview endpoint so the
 * iframe on the right shows exactly what the learning room will render.
 * On submit the array is written to the hidden blocks_json input.
 *
 * Vanilla JS on purpose: no build step, no framework.
 */
(function () {
  'use strict';

  var editor = document.querySelector('[data-lesson-editor]');
  var form = document.querySelector('[data-lesson-editor-form]');
  if (!editor || !form) return;

  var config = readJson('lesson-editor-config') || {};
  var blocks = readJson('lesson-editor-blocks') || [];
  if (!Array.isArray(blocks)) blocks = [];

  // A lesson written before the editor opens as one "html" block holding its
  // original markup; that block is labelled so the admin knows what it is.
  if (config.legacy && blocks.length === 1 && blocks[0] && blocks[0].type === 'html') {
    blocks[0]._legacy = true;
  }

  var list = editor.querySelector('[data-block-list]');
  var empty = editor.querySelector('[data-block-empty]');
  var frame = editor.querySelector('[data-preview-frame]');
  var status = editor.querySelector('[data-preview-status]');
  var hidden = document.getElementById('lesson-blocks-json');
  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

  var TYPE_LABELS = { code: 'Code', text: 'Text', table: 'Table', image: 'Photo', html: 'HTML' };

  function readJson(id) {
    var node = document.getElementById(id);
    if (!node) return null;
    try { return JSON.parse(node.textContent || 'null'); } catch (e) { return null; }
  }

  function defaults(type) {
    switch (type) {
      case 'code': return { type: 'code', label: '', language: 'python', code: '', output: '', try_in_compiler: true };
      case 'text': return { type: 'text', font: 'sans', size: 'md', body: '' };
      case 'table': return { type: 'table', label: '', columns: ['Column 1', 'Column 2'], rows: [['', '']], note: '' };
      case 'image': return { type: 'image', src: '', alt: '', caption: '', width: 100 };
      case 'html': return { type: 'html', html: '' };
      default: return null;
    }
  }

  // ── Serialisation ────────────────────────────────────────────────

  function serialise() {
    return blocks.map(function (block) {
      var copy = {};
      Object.keys(block).forEach(function (key) {
        if (key.charAt(0) !== '_') copy[key] = block[key];
      });
      return copy;
    });
  }

  function writeHidden() {
    if (hidden) hidden.value = JSON.stringify(serialise());
  }

  // ── Preview ──────────────────────────────────────────────────────

  var previewTimer = null;
  var previewRequest = 0;

  function schedulePreview() {
    writeHidden();
    if (status) status.textContent = 'Updating…';
    clearTimeout(previewTimer);
    previewTimer = setTimeout(refreshPreview, 400);
  }

  function refreshPreview() {
    if (!frame || !config.previewUrl) return;
    var request = ++previewRequest;
    var body = new FormData();
    body.append('blocks_json', JSON.stringify(serialise()));

    fetch(config.previewUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
      body: body
    }).then(function (response) {
      if (!response.ok) throw new Error('Preview failed (' + response.status + ')');
      return response.text();
    }).then(function (html) {
      if (request !== previewRequest) return;
      var scrollY = 0;
      try { scrollY = frame.contentWindow ? frame.contentWindow.scrollY : 0; } catch (e) { scrollY = 0; }
      frame.srcdoc = html;
      frame.onload = function () {
        try { if (scrollY) frame.contentWindow.scrollTo(0, scrollY); } catch (e) { /* cross-origin guard */ }
      };
      if (status) status.textContent = '';
    }).catch(function (error) {
      if (request !== previewRequest) return;
      if (status) status.textContent = error.message || 'Preview failed';
    });
  }

  // ── Rendering ────────────────────────────────────────────────────

  function el(tag, attrs, children) {
    var node = document.createElement(tag);
    if (attrs) {
      Object.keys(attrs).forEach(function (key) {
        if (attrs[key] === undefined || attrs[key] === null) return;
        if (key === 'text') node.textContent = attrs[key];
        else if (key === 'html') node.innerHTML = attrs[key];
        else if (key === 'checked' || key === 'hidden' || key === 'disabled') node[key] = !!attrs[key];
        else if (key === 'value') node.value = attrs[key];
        else node.setAttribute(key, attrs[key]);
      });
    }
    (children || []).forEach(function (child) {
      if (child) node.appendChild(typeof child === 'string' ? document.createTextNode(child) : child);
    });
    return node;
  }

  function field(labelText, control, help) {
    var wrapper = el('div', { 'class': 'field' });
    var label = el('label', { text: labelText });
    if (control.id) label.setAttribute('for', control.id);
    wrapper.appendChild(label);
    wrapper.appendChild(control);
    if (help) wrapper.appendChild(el('div', { 'class': 'lesson-block-help', html: help }));
    return wrapper;
  }

  function select(options, value, onChange, id) {
    var node = el('select', { 'class': 'select', id: id });
    options.forEach(function (option) {
      var opt = el('option', { value: option[0], text: option[1] });
      if (option[0] === value) opt.selected = true;
      node.appendChild(opt);
    });
    node.addEventListener('change', function () { onChange(node.value); schedulePreview(); });
    return node;
  }

  function input(value, onInput, attrs) {
    var node = el('input', Object.assign({ 'class': 'input', type: 'text', value: value || '' }, attrs || {}));
    node.addEventListener('input', function () { onInput(node.value); schedulePreview(); });
    return node;
  }

  function textarea(value, onInput, attrs) {
    var node = el('textarea', Object.assign({ 'class': 'textarea' }, attrs || {}));
    node.value = value || '';
    node.addEventListener('input', function () { onInput(node.value); schedulePreview(); });
    return node;
  }

  function render() {
    list.innerHTML = '';
    blocks.forEach(function (block, index) {
      list.appendChild(renderBlock(block, index));
    });
    if (empty) empty.hidden = blocks.length > 0;
    writeHidden();
  }

  function renderBlock(block, index) {
    var uid = 'block-' + index + '-';
    var card = el('article', { 'class': 'lesson-block', 'data-block-index': String(index) });

    var title = TYPE_LABELS[block.type] || block.type;
    if (block._legacy) title = 'Original lesson HTML';

    var controls = el('div', { 'class': 'action-row' }, [
      button('Move up', 'secondary', function () { move(index, -1); }, index === 0),
      button('Move down', 'secondary', function () { move(index, 1); }, index === blocks.length - 1),
      button('Delete', 'danger', function () { remove(index); })
    ]);

    card.appendChild(el('div', { 'class': 'lesson-block-head' }, [
      el('strong', { text: (index + 1) + '. ' + title }),
      controls
    ]));

    var body = el('div', { 'class': 'lesson-block-body' });
    card.appendChild(body);

    switch (block.type) {
      case 'code': renderCode(block, body, uid); break;
      case 'text': renderText(block, body, uid); break;
      case 'table': renderTable(block, body, uid, index); break;
      case 'image': renderImage(block, body, uid); break;
      case 'html': renderHtml(block, body, uid); break;
    }

    return card;
  }

  function button(label, variant, onClick, disabled) {
    var node = el('button', { 'class': 'btn small ' + (variant || 'secondary'), type: 'button', text: label, disabled: !!disabled });
    node.addEventListener('click', onClick);
    return node;
  }

  function renderCode(block, body, uid) {
    var grid = el('div', { 'class': 'form-grid' });
    grid.appendChild(field('Label', input(block.label, function (v) { block.label = v; }, { id: uid + 'label', placeholder: 'e.g. Connect to SQLite' }),
      'Shown in the window header. The language is added in front automatically.'));
    grid.appendChild(field('Language', select([['python', 'Python'], ['sql', 'SQL'], ['text', 'Plain text']], block.language, function (v) { block.language = v; }, uid + 'language')));
    body.appendChild(grid);
    body.appendChild(field('Code', textarea(block.code, function (v) { block.code = v; }, { id: uid + 'code', 'class': 'textarea mono', spellcheck: 'false' })));
    body.appendChild(field('Console output (optional)', textarea(block.output, function (v) { block.output = v; }, { id: uid + 'output', 'class': 'textarea mono', spellcheck: 'false' })));

    var check = el('input', { type: 'checkbox', id: uid + 'try', checked: block.try_in_compiler !== false });
    check.addEventListener('change', function () { block.try_in_compiler = check.checked; schedulePreview(); });
    body.appendChild(el('label', { 'class': 'lesson-block-check', 'for': uid + 'try' }, [check, el('span', { text: 'Show the "Try in Compiler" button' })]));
  }

  function renderText(block, body, uid) {
    var grid = el('div', { 'class': 'form-grid' });
    grid.appendChild(field('Font', select([['sans', 'Sans (Inter)'], ['mono', 'Monospace'], ['serif', 'Serif']], block.font, function (v) { block.font = v; }, uid + 'font')));
    grid.appendChild(field('Size', select([['sm', 'Small'], ['md', 'Normal'], ['lg', 'Large'], ['xl', 'Extra large']], block.size, function (v) { block.size = v; }, uid + 'size')));
    body.appendChild(grid);

    var area = textarea(block.body, function (v) { block.body = v; }, { id: uid + 'body', 'class': 'textarea body' });

    var toolbar = el('div', { 'class': 'lesson-text-toolbar' }, [
      button('Bold', 'secondary', function () { wrap(area, '**', '**', 'bold text'); }),
      button('Italic', 'secondary', function () { wrap(area, '*', '*', 'italic text'); }),
      button('Code', 'secondary', function () { wrap(area, '`', '`', 'code'); }),
      button('Heading', 'secondary', function () { linePrefix(area, '## ', 'Heading'); }),
      button('Subheading', 'secondary', function () { linePrefix(area, '### ', 'Subheading'); }),
      button('List item', 'secondary', function () { linePrefix(area, '- ', 'List item'); })
    ]);
    toolbar.querySelectorAll('button').forEach(function (b) {
      b.addEventListener('click', function () { block.body = area.value; schedulePreview(); });
    });

    var wrapper = el('div', { 'class': 'field' }, [
      el('label', { 'for': uid + 'body', text: 'Body' }),
      toolbar,
      area,
      el('div', { 'class': 'lesson-block-help', html: 'Markup: <code>## Heading</code>, <code>### Subheading</code>, <code>- list item</code>, <code>**bold**</code>, <code>*italic*</code>, <code>`code`</code>. Leave a blank line between paragraphs.' })
    ]);
    area.style.marginTop = '8px';
    body.appendChild(wrapper);
  }

  function wrap(area, before, after, placeholder) {
    var start = area.selectionStart, end = area.selectionEnd;
    var selected = area.value.substring(start, end) || placeholder;
    var text = before + selected + after;
    area.setRangeText(text, start, end, 'select');
    area.setSelectionRange(start + before.length, start + before.length + selected.length);
    area.focus();
  }

  function linePrefix(area, prefix, placeholder) {
    var start = area.selectionStart, end = area.selectionEnd;
    var value = area.value;
    var lineStart = value.lastIndexOf('\n', start - 1) + 1;
    if (start === end && lineStart === start && (start === 0 || value.charAt(start - 1) === '\n')) {
      // Empty line (or start of text): insert the marker plus a placeholder.
      area.setRangeText(prefix + placeholder, start, end, 'end');
      area.setSelectionRange(start + prefix.length, start + prefix.length + placeholder.length);
    } else if (start === end) {
      area.setRangeText(prefix, lineStart, lineStart, 'end');
      area.setSelectionRange(start + prefix.length, start + prefix.length);
    } else {
      // Prefix every selected line.
      var selEnd = end;
      var chunk = value.substring(lineStart, selEnd);
      var replaced = chunk.split('\n').map(function (line) { return prefix + line; }).join('\n');
      area.setRangeText(replaced, lineStart, selEnd, 'select');
    }
    area.focus();
  }

  function renderTable(block, body, uid, blockIndex) {
    if (!Array.isArray(block.columns)) block.columns = [];
    if (!Array.isArray(block.rows)) block.rows = [];

    body.appendChild(field('Label (optional)', input(block.label, function (v) { block.label = v; }, { id: uid + 'label', placeholder: 'e.g. Sample rows' })));

    var grid = el('div', { 'class': 'lesson-table-grid' });

    // Header row: one input per column plus a remove button under it.
    var head = el('div', { 'class': 'lesson-table-row' });
    block.columns.forEach(function (column, c) {
      head.appendChild(input(column, function (v) { block.columns[c] = v; }, { 'class': 'input head', placeholder: 'Column ' + (c + 1), 'aria-label': 'Column ' + (c + 1) + ' heading' }));
    });
    head.appendChild(el('div', { 'class': 'btn-cell' }, [el('span', { 'class': 'lesson-block-help', text: 'Columns' })]));
    grid.appendChild(head);

    var removeRow = el('div', { 'class': 'lesson-table-row' });
    block.columns.forEach(function (column, c) {
      removeRow.appendChild(el('div', { 'class': 'btn-cell' }, [button('Remove column', 'secondary', function () {
        block.columns.splice(c, 1);
        block.rows.forEach(function (row) { row.splice(c, 1); });
        render(); schedulePreview();
      }, block.columns.length <= 1)]));
    });
    removeRow.appendChild(el('div'));
    grid.appendChild(removeRow);

    block.rows.forEach(function (row, r) {
      while (row.length < block.columns.length) row.push('');
      var line = el('div', { 'class': 'lesson-table-row' });
      block.columns.forEach(function (column, c) {
        line.appendChild(input(row[c], function (v) { row[c] = v; }, { placeholder: 'Row ' + (r + 1), 'aria-label': 'Row ' + (r + 1) + ', column ' + (c + 1) }));
      });
      line.appendChild(el('div', { 'class': 'btn-cell' }, [button('Remove row', 'secondary', function () {
        block.rows.splice(r, 1);
        render(); schedulePreview();
      })]));
      grid.appendChild(line);
    });

    body.appendChild(grid);

    body.appendChild(el('div', { 'class': 'lesson-table-actions' }, [
      button('Add row', 'secondary', function () {
        block.rows.push(block.columns.map(function () { return ''; }));
        render(); schedulePreview();
        focusCell(blockIndex, block.rows.length - 1, 0);
      }),
      button('Add column', 'secondary', function () {
        if (block.columns.length >= 12) return;
        block.columns.push('Column ' + (block.columns.length + 1));
        block.rows.forEach(function (row) { row.push(''); });
        render(); schedulePreview();
      })
    ]));

    body.appendChild(field('Note under the table (optional)', textarea(block.note, function (v) { block.note = v; }, { id: uid + 'note' }),
      'Inline markup works here: <code>**bold**</code>, <code>*italic*</code>, <code>`code`</code>.'));
  }

  function focusCell(blockIndex, r, c) {
    var card = list.querySelector('[data-block-index="' + blockIndex + '"]');
    if (!card) return;
    var target = card.querySelector('input[aria-label="Row ' + (r + 1) + ', column ' + (c + 1) + '"]');
    if (target) target.focus();
  }

  function renderImage(block, body, uid) {
    var preview = el('img', { 'class': 'lesson-image-preview', alt: '', hidden: !block.src });
    if (block.src) preview.src = block.src;

    var fileInput = el('input', { type: 'file', id: uid + 'file', accept: '.jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif' });
    var fileStatus = el('div', { 'class': 'lesson-block-help', text: block.src ? block.src : 'JPG, PNG, WebP or GIF up to 5 MB.' });

    fileInput.addEventListener('change', function () {
      var file = fileInput.files && fileInput.files[0];
      if (!file) return;
      fileStatus.textContent = 'Uploading ' + file.name + '…';
      upload(file).then(function (url) {
        block.src = url;
        preview.src = url;
        preview.hidden = false;
        fileStatus.textContent = url;
        fileInput.value = '';
        schedulePreview();
      }).catch(function (error) {
        fileStatus.textContent = error.message || 'Upload failed.';
        fileInput.value = '';
      });
    });

    body.appendChild(el('div', { 'class': 'field' }, [
      el('label', { 'for': uid + 'file', text: block.src ? 'Replace photo' : 'Photo' }),
      el('div', { 'class': 'lesson-image-file' }, [fileInput]),
      fileStatus
    ]));
    body.appendChild(preview);

    var grid = el('div', { 'class': 'form-grid' });
    grid.appendChild(field('Alt text', input(block.alt, function (v) { block.alt = v; }, { id: uid + 'alt', placeholder: 'What the image shows' })));
    grid.appendChild(field('Caption (optional)', input(block.caption, function (v) { block.caption = v; }, { id: uid + 'caption' })));
    body.appendChild(grid);

    var range = el('input', { type: 'range', id: uid + 'width', min: '25', max: '100', step: '5', value: String(block.width || 100) });
    var out = el('output', { text: (block.width || 100) + '%' });
    range.addEventListener('input', function () {
      block.width = parseInt(range.value, 10) || 100;
      out.textContent = block.width + '%';
      schedulePreview();
    });
    body.appendChild(el('div', { 'class': 'field' }, [
      el('label', { 'for': uid + 'width', text: 'Width' }),
      el('div', { 'class': 'lesson-image-width' }, [range, out])
    ]));
  }

  function upload(file) {
    var body = new FormData();
    body.append('image', file);
    if (config.moduleId) body.append('module_id', String(config.moduleId));

    return fetch(config.uploadUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      body: body
    }).then(function (response) {
      return response.json().catch(function () { return {}; }).then(function (data) {
        if (!response.ok || !data.url) {
          var message = data.message || (data.errors && data.errors.image && data.errors.image[0]) || ('Upload failed (' + response.status + ')');
          throw new Error(message);
        }
        return data.url;
      });
    });
  }

  function renderHtml(block, body, uid) {
    var label = block._legacy ? 'Original lesson HTML' : 'HTML';
    body.appendChild(field(label, textarea(block.html, function (v) { block.html = v; }, { id: uid + 'html', 'class': 'textarea html', spellcheck: 'false' }),
      block._legacy
        ? 'This is the lesson exactly as it was written. Leave it as it is and the lesson stays byte for byte the same, or edit it here and add new blocks around it.'
        : 'Raw HTML, shown as is. Use this only for markup the other blocks cannot express.'));
  }

  // ── Structural changes ───────────────────────────────────────────

  function add(type) {
    var block = defaults(type);
    if (!block) return;
    if (blocks.length >= 200) return;
    blocks.push(block);
    render();
    schedulePreview();
    var card = list.querySelector('[data-block-index="' + (blocks.length - 1) + '"]');
    if (card) {
      card.scrollIntoView({ block: 'nearest' });
      var first = card.querySelector('input:not([type="file"]), textarea, select');
      if (first) first.focus();
    }
  }

  function move(index, delta) {
    var target = index + delta;
    if (target < 0 || target >= blocks.length) return;
    var tmp = blocks[index];
    blocks[index] = blocks[target];
    blocks[target] = tmp;
    render();
    schedulePreview();
    var card = list.querySelector('[data-block-index="' + target + '"]');
    if (card) card.scrollIntoView({ block: 'nearest' });
  }

  function remove(index) {
    var block = blocks[index];
    if (!block) return;
    var hasContent = (block.code || block.body || block.html || block.src || (block.rows && block.rows.length > 1));
    if (hasContent && !window.confirm('Delete this block?')) return;
    blocks.splice(index, 1);
    render();
    schedulePreview();
  }

  // ── Wiring ───────────────────────────────────────────────────────

  editor.querySelectorAll('[data-add-block]').forEach(function (node) {
    node.addEventListener('click', function () { add(node.getAttribute('data-add-block')); });
  });

  form.addEventListener('submit', function () {
    writeHidden();
  });

  // Enter in a single-line input should not submit the whole lesson.
  editor.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' && event.target && event.target.tagName === 'INPUT' && event.target.type !== 'file') {
      event.preventDefault();
    }
  });

  render();
  refreshPreview();
})();
