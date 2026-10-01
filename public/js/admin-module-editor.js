/*
 * DataSensei module builder (Updates 5 and 6).
 *
 * Used by Admin > DataSensei Modules and Admin > Instructor Module Library
 * (resources/views/admin/partials/module-editor.blade.php).
 *
 * Learning Content works like a form builder: each section holds content
 * blocks (heading, paragraph, lists, notes, Python/SQL code, walkthroughs,
 * activities, key points, knowledge checks, ...), each block is its own card,
 * and "+ Add Content" adds one. Existing content arrives already converted to
 * blocks by the server (ModuleBlockConverter). On save the sections go back
 * as JSON in a hidden field ({id or source, title, blocks}); the server keeps
 * any section whose blocks were not changed exactly as it was stored.
 *
 * Embedded Review Questions are edited as question cards, as before.
 *
 * Saving (Updates 7) happens in the background: Save Changes, Save Draft,
 * Publish and Unpublish send the form with fetch and the page never reloads,
 * so the admin stays on the same tab, open section and scroll position. The
 * action bar shows "Saving...", then "Saved" or "Failed to save"; the save
 * buttons are disabled while a request is running; server validation errors
 * are shown beside the fields they belong to; nothing typed is lost when a
 * save fails. Browsers without fetch post the form as before.
 */
(function () {
  'use strict';

  var root = document.querySelector('[data-module-editor]');
  if (!root) return;

  var form = root.closest('form');
  var config = readJson('module-editor-config') || {};
  var initial = readJson('module-editor-data') || {};
  var isPublic = config.mode === 'public';
  var locked = !!config.contentLocked;
  var difficulties = Array.isArray(config.difficulties) ? config.difficulties : ['Easy', 'Moderate', 'Difficult'];
  var csrf = (form.querySelector('input[name="_token"]') || {}).value || '';

  var sectionsInput = form.querySelector('[data-sections-input]');
  var questionsInput = form.querySelector('[data-questions-input]');
  var sectionList = root.querySelector('[data-section-list]');
  var questionList = root.querySelector('[data-question-list]');
  var outcomeList = root.querySelector('[data-outcome-list]');
  var problemsBox = root.querySelector('[data-editor-problems]');
  var stateLabel = root.querySelector('[data-editor-state]');

  var nextUid = 0;
  var dirty = false;
  var submitting = false;
  var saving = false;
  var changeCount = 0;
  var noticeBox = root.querySelector('[data-editor-notice]');
  var STATE_CLASSES = ['is-dirty', 'is-saving', 'is-saved', 'is-error'];

  // ── Content block types ──────────────────────────────────────────────

  var BLOCK_TYPES = {
    heading: { label: 'Heading', hint: 'A large title inside the lesson.', make: function () { return { text: '' }; } },
    subheading: { label: 'Subheading', hint: 'A smaller title for a part of the lesson.', make: function () { return { text: '' }; } },
    paragraph: { label: 'Paragraph', hint: 'Lesson text. Separate paragraphs with a blank line.', make: function () { return { text: '' }; } },
    bulleted_list: { label: 'Bulleted List', hint: 'Points in no particular order.', make: function () { return { items: [''] }; } },
    numbered_list: { label: 'Numbered List', hint: 'Points in order.', make: function () { return { items: [''] }; } },
    note: { label: 'Important Note', hint: 'Something learners must not miss.', make: function () { return { title: '', text: '' }; } },
    example: { label: 'Example', hint: 'A worked example in words.', make: function () { return { title: '', text: '' }; } },
    image: { label: 'Image', hint: 'A picture or diagram with a caption.', make: function () { return { src: '', alt: '', caption: '', width: 100 }; } },
    python_code: { label: 'Python Code', hint: 'Code learners can open in the compiler.', make: function () { return { title: '', code: '', explanation: '', output: '' }; } },
    sql_code: { label: 'SQL Code', hint: 'A query learners can open in the SQL sandbox.', make: function () { return { title: '', code: '', explanation: '', result: '' }; } },
    code_snippet: { label: 'General Code', hint: 'Any other code or command.', make: function () { return { title: '', code: '', explanation: '', output: '' }; } },
    walkthrough: { label: 'Step-by-Step Walkthrough', hint: 'Numbered steps through an example.', make: function () { return { items: [''] }; } },
    activity: { label: 'Practice Activity', hint: 'A short task learners do on their own.', make: function () { return { text: '' }; } },
    mistakes: { label: 'Common Mistakes', hint: 'Errors learners often make.', make: function () { return { items: [''] }; } },
    key_points: { label: 'Key Points', hint: 'The points to remember.', make: function () { return { items: [''] }; } },
    check: { label: 'Check Your Understanding', hint: 'Questions learners ask themselves.', make: function () { return { items: [''] }; } },
    table: { label: 'Table', hint: 'Rows and columns of text.', make: function () { return { label: '', columns: ['', ''], rows: [['', '']], note: '' }; } },
    quiz: { label: 'Knowledge Check', hint: 'Multiple-choice questions with instant feedback.', make: function () { return { title: '', prefix: 'kc' + Math.random().toString(36).slice(2, 10), questions: [{ question: '', choices: ['', '', '', ''], answer: -1, explanation: '' }] }; } },
    preserved: { label: 'Original Formatting', hint: '', make: function () { return { label: '', html: '' }; } }
  };
  var MENU_TYPES = ['heading', 'subheading', 'paragraph', 'bulleted_list', 'numbered_list', 'note', 'example', 'image', 'python_code', 'sql_code', 'code_snippet', 'walkthrough', 'activity', 'mistakes', 'key_points', 'check', 'table', 'quiz'];
  var ITEM_TYPES = ['bulleted_list', 'numbered_list', 'walkthrough', 'mistakes', 'key_points', 'check'];

  // ── Helpers ──────────────────────────────────────────────────────────

  function readJson(id) {
    var node = document.getElementById(id);
    if (!node) return null;
    try { return JSON.parse(node.textContent || 'null'); } catch (e) { return null; }
  }

  function isObject(value) {
    return value !== null && typeof value === 'object' && !Array.isArray(value);
  }

  function text(value) {
    return value === null || value === undefined ? '' : String(value);
  }

  function clone(value) {
    return JSON.parse(JSON.stringify(value));
  }

  function el(tag, className, content) {
    var node = document.createElement(tag);
    if (className) node.className = className;
    if (content !== undefined && content !== null) node.textContent = content;
    return node;
  }

  function button(label, className, attrs) {
    var node = el('button', className || 'btn small secondary', label);
    node.type = 'button';
    Object.keys(attrs || {}).forEach(function (name) { node.setAttribute(name, attrs[name]); });
    return node;
  }

  function autoSize(area) {
    if (!area || !area.isConnected) return;
    area.style.height = 'auto';
    area.style.height = Math.min(area.scrollHeight + 2, 640) + 'px';
  }

  function setState(label, kind) {
    if (!stateLabel) return;
    stateLabel.textContent = label;
    STATE_CLASSES.forEach(function (name) { stateLabel.classList.remove(name); });
    if (kind) stateLabel.classList.add(kind);
  }

  function markDirty() {
    changeCount++;
    if (dirty) return;
    dirty = true;
    setState('Unsaved changes', 'is-dirty');
  }

  function setCount(name, value) {
    root.querySelectorAll('[data-count="' + name + '"]').forEach(function (node) { node.textContent = String(value); });
  }

  function nonEmpty(value) {
    return typeof value === 'string' ? value.trim() !== '' : value !== null && value !== undefined;
  }

  function shorten(value, length) {
    var flat = text(value).replace(/\s+/g, ' ').trim();
    return flat.length > length ? flat.slice(0, length - 3) + '...' : flat;
  }

  function difficultyKeyOf(data) {
    if (Object.prototype.hasOwnProperty.call(data, 'difficulty_level')) return 'difficulty_level';
    if (Object.prototype.hasOwnProperty.call(data, 'difficulty')) return 'difficulty';
    return 'difficulty_level';
  }

  function swap(list, a, b) {
    if (b < 0 || b >= list.length) return;
    var tmp = list[a];
    list[a] = list[b];
    list[b] = tmp;
  }

  function postForHtml(url, fields) {
    var body = new FormData();
    body.append('_token', csrf);
    Object.keys(fields).forEach(function (key) { body.append(key, fields[key]); });
    return fetch(url, { method: 'POST', body: body, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (response) { if (!response.ok) throw new Error('HTTP ' + response.status); return response.text(); });
  }

  /** Shows a server-rendered page in a same-origin frame sized to its content. */
  function previewFrame(target, promise, title) {
    target.innerHTML = '';
    var frame = el('iframe');
    frame.title = title || 'Preview';
    frame.setAttribute('sandbox', 'allow-same-origin allow-scripts');
    target.appendChild(frame);
    promise.then(function (html) {
      frame.addEventListener('load', function () {
        try { frame.style.height = Math.max(200, frame.contentDocument.documentElement.scrollHeight + 4) + 'px'; } catch (e) { /* not reachable */ }
      });
      frame.srcdoc = html;
    }).catch(function () {
      target.innerHTML = '';
      target.appendChild(el('p', 'field-help', 'The preview could not be loaded. Check your connection and try again.'));
    });
  }

  // ── Fields ───────────────────────────────────────────────────────────

  function fieldWrap(label, hint, control) {
    var wrap = el('div', 'field');
    var id = 'me-field-' + (++nextUid);
    var lab = el('label', null, label);
    lab.htmlFor = id;
    control.id = id;
    wrap.appendChild(lab);
    wrap.appendChild(control);
    if (hint) wrap.appendChild(el('p', 'field-help', hint));
    return wrap;
  }

  function inputField(label, value, onChange, opts) {
    opts = opts || {};
    var input = el('input', 'input');
    input.type = 'text';
    input.value = text(value);
    if (opts.maxlength) input.maxLength = opts.maxlength;
    if (opts.placeholder) input.placeholder = opts.placeholder;
    if (opts.dataField) input.setAttribute('data-field', opts.dataField);
    input.readOnly = locked;
    input.addEventListener('input', function () { onChange(input.value); markDirty(); });
    return fieldWrap(label, opts.hint, input);
  }

  function areaField(label, value, onChange, opts) {
    opts = opts || {};
    var area = el('textarea', 'textarea' + (opts.mono ? ' me-mono' : ''));
    area.rows = opts.rows || 3;
    area.value = text(value);
    area.spellcheck = !opts.mono;
    if (opts.placeholder) area.placeholder = opts.placeholder;
    if (opts.dataField) area.setAttribute('data-field', opts.dataField);
    area.readOnly = locked;
    area.addEventListener('input', function () { onChange(area.value); autoSize(area); markDirty(); });
    var wrap = fieldWrap(label, opts.hint, area);
    if (opts.format && !locked) wrap.insertBefore(formatBar(area), area);
    return wrap;
  }

  /** Bold, italic and code buttons that mark the selected words. */
  function formatBar(area) {
    var bar = el('div', 'me-format');
    bar.setAttribute('role', 'toolbar');
    bar.setAttribute('aria-label', 'Text formatting');
    [['B', '**', 'Bold'], ['I', '*', 'Italic'], ['Code', '`', 'Code']].forEach(function (spec) {
      var control = button(spec[0], 'me-format-btn', { title: spec[2], 'aria-label': spec[2] });
      if (spec[0] === 'I') control.style.fontStyle = 'italic';
      control.addEventListener('mousedown', function (event) { event.preventDefault(); });
      control.addEventListener('click', function () {
        var start = area.selectionStart;
        var end = area.selectionEnd;
        var selected = area.value.slice(start, end) || spec[2].toLowerCase();
        area.setRangeText(spec[1] + selected + spec[1], start, end, 'end');
        area.setSelectionRange(start + spec[1].length, start + spec[1].length + selected.length);
        area.focus();
        area.dispatchEvent(new Event('input', { bubbles: true }));
      });
      bar.appendChild(control);
    });
    return bar;
  }

  /** Text as learners read it: the formatting marks left out. */
  function plain(value) {
    return text(value).replace(/\*\*(.+?)\*\*/g, '$1').replace(/`([^`]+)`/g, '$1').replace(/(^|[^*\w])\*(?!\s)(.+?)\*(?![*\w])/g, '$1$2');
  }

  /** A code editor: CodeMirror with syntax colours when it is available. */
  function codeField(label, value, mode, onChange, hint) {
    var area = el('textarea', 'textarea me-mono me-code');
    area.rows = 8;
    area.value = text(value);
    area.spellcheck = false;
    area.readOnly = locked;
    area.setAttribute('data-code-mode', mode);
    area.addEventListener('input', function () { onChange(area.value); markDirty(); });
    area.__onCode = onChange;
    return fieldWrap(label, hint, area);
  }

  function mountCodeEditors(scope) {
    if (!window.CodeMirror) return;
    scope.querySelectorAll('textarea[data-code-mode]').forEach(function (area) {
      if (area.__cm || !area.isConnected) return;
      var mode = area.getAttribute('data-code-mode');
      var cm = window.CodeMirror.fromTextArea(area, {
        mode: mode === 'sql' ? 'ds-sql' : (mode === 'python' ? 'python' : null),
        theme: 'dracula',
        lineNumbers: true,
        lineWrapping: false,
        tabSize: 4,
        indentUnit: 4,
        indentWithTabs: false,
        matchBrackets: true,
        autoCloseBrackets: true,
        readOnly: locked,
        viewportMargin: Infinity,
        extraKeys: { Tab: function (editor) { editor.replaceSelection('    '); } }
      });
      area.__cm = cm;
      cm.on('change', function () {
        area.__onCode(cm.getValue());
        markDirty();
      });
      setTimeout(function () { cm.refresh(); }, 0);
    });
  }

  // A small SQL colouring mode for the code editor (the bundle has Python only).
  if (window.CodeMirror && !window.CodeMirror.modes['ds-sql']) {
    window.CodeMirror.defineMode('ds-sql', function () {
      var keywords = /^(?:select|from|where|and|or|not|in|is|null|as|join|left|right|inner|outer|full|cross|on|group|by|order|having|limit|offset|insert|into|values|update|set|delete|create|table|drop|alter|add|distinct|count|sum|avg|min|max|case|when|then|else|end|with|union|all|asc|desc|like|between|exists|primary|key|foreign|references|int|integer|varchar|char|text|date|datetime|float|real|decimal|boolean|default|index|view|round|coalesce|cast)\b/i;
      return {
        token: function (stream) {
          if (stream.eatSpace()) return null;
          if (stream.match('--')) { stream.skipToEnd(); return 'comment'; }
          var ch = stream.peek();
          if (ch === '\'' || ch === '"') {
            var quote = stream.next();
            var next;
            while ((next = stream.next()) != null) { if (next === quote) break; }
            return 'string';
          }
          if (stream.match(/^\d+(?:\.\d+)?/)) return 'number';
          if (stream.match(keywords)) return 'keyword';
          if (stream.match(/^[A-Za-z_][A-Za-z0-9_]*/)) return 'variable';
          stream.next();
          return 'operator';
        }
      };
    });
  }

  /** An editable list of items (steps, points, ...) inside `owner[key]`. */
  function listField(label, owner, key, itemName, hint) {
    var wrap = el('div', 'field me-list');
    wrap.appendChild(el('span', 'me-list-label', label));
    var items = el('ol', 'me-list-items');
    wrap.appendChild(items);

    function values() {
      return Array.isArray(owner[key]) ? owner[key] : [];
    }

    function focusItem(index) {
      var area = items.querySelectorAll('textarea')[index];
      if (area) area.focus();
    }

    function render() {
      items.innerHTML = '';
      var list = values();
      list.forEach(function (value, index) {
        var li = el('li', 'me-list-item');
        var area = el('textarea', 'textarea');
        area.rows = 1;
        area.value = text(value);
        area.setAttribute('aria-label', label + ', ' + itemName + ' ' + (index + 1));
        area.readOnly = locked;
        area.addEventListener('input', function () {
          owner[key][index] = area.value;
          autoSize(area);
          markDirty();
          if (owner.__onChange) owner.__onChange();
        });
        area.addEventListener('keydown', function (event) {
          // Enter on the last item adds the next one, like a form builder.
          if (event.key === 'Enter' && !event.shiftKey && !locked && index === values().length - 1 && area.value.trim() !== '') {
            event.preventDefault();
            owner[key].push('');
            render();
            markDirty();
            focusItem(owner[key].length - 1);
          }
        });
        li.appendChild(area);
        if (!locked) {
          var actions = el('div', 'me-row-actions');
          var up = button('↑', 'btn small secondary', { title: 'Move up', 'aria-label': 'Move ' + itemName + ' ' + (index + 1) + ' up' });
          var down = button('↓', 'btn small secondary', { title: 'Move down', 'aria-label': 'Move ' + itemName + ' ' + (index + 1) + ' down' });
          var remove = button('Remove', 'btn small danger', { 'aria-label': 'Remove ' + itemName + ' ' + (index + 1) });
          up.disabled = index === 0;
          down.disabled = index === list.length - 1;
          up.addEventListener('click', function () { swap(owner[key], index, index - 1); render(); markDirty(); focusItem(index - 1); });
          down.addEventListener('click', function () { swap(owner[key], index, index + 1); render(); markDirty(); focusItem(index + 1); });
          remove.addEventListener('click', function () { owner[key].splice(index, 1); render(); markDirty(); if (owner.__onChange) owner.__onChange(); });
          actions.appendChild(up);
          actions.appendChild(down);
          actions.appendChild(remove);
          li.appendChild(actions);
        }
        items.appendChild(li);
      });
      if (!list.length) items.appendChild(el('li', 'me-empty', locked ? 'None.' : 'None yet.'));
      requestAnimationFrame(function () { items.querySelectorAll('textarea').forEach(autoSize); });
    }

    render();

    if (!locked) {
      var add = button('+ Add item', 'btn small secondary');
      add.addEventListener('click', function () {
        if (!Array.isArray(owner[key])) owner[key] = [];
        owner[key].push('');
        render();
        markDirty();
        focusItem(owner[key].length - 1);
      });
      wrap.appendChild(add);
    }
    if (hint) wrap.appendChild(el('p', 'field-help', hint));

    return wrap;
  }

  // ── Card lists (sections, blocks, questions) ─────────────────────────

  var gripSvg = '<svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor" aria-hidden="true"><circle cx="3" cy="3" r="1.4"/><circle cx="9" cy="3" r="1.4"/><circle cx="3" cy="8" r="1.4"/><circle cx="9" cy="8" r="1.4"/><circle cx="3" cy="13" r="1.4"/><circle cx="9" cy="13" r="1.4"/></svg>';

  function CardList(options) {
    this.items = options.items;
    this.container = options.container;
    this.noun = options.noun;
    this.cardClass = options.cardClass || 'me-card';
    this.summary = options.summary;
    this.buildBody = options.buildBody;
    this.duplicate = options.duplicate;
    this.canDelete = options.canDelete || function () { return true; };
    this.onChange = options.onChange;
    this.emptyNode = options.emptyNode;
    this.headExtra = options.headExtra;
    this.nodes = {};
    this.renderAll();
    if (!locked) this.enableDrag();
  }

  CardList.prototype.renderAll = function () {
    var self = this;
    var fragment = document.createDocumentFragment();
    this.nodes = {};
    this.items.forEach(function (item) { fragment.appendChild(self.buildCard(item)); });
    this.container.innerHTML = '';
    this.container.appendChild(fragment);
    this.refreshNumbers();
  };

  CardList.prototype.indexOf = function (uid) {
    for (var i = 0; i < this.items.length; i++) if (this.items[i].uid === uid) return i;
    return -1;
  };

  /** The card of this list an element sits in (not a card of a nested list). */
  CardList.prototype.ownCard = function (node) {
    var card = node && node.closest ? node.closest('.' + this.cardClass) : null;
    return card && card.parentElement === this.container ? card : null;
  };

  CardList.prototype.buildCard = function (item) {
    var self = this;
    var card = el('article', this.cardClass);
    card.setAttribute('data-uid', String(item.uid));

    var head = el('div', 'me-card-head');
    if (!locked) {
      var grip = el('span', 'me-drag');
      grip.innerHTML = gripSvg;
      grip.title = 'Drag to reorder';
      head.appendChild(grip);
    }

    var toggle = el('button', 'me-card-toggle');
    toggle.type = 'button';
    toggle.setAttribute('aria-expanded', 'false');
    var number = el('span', 'me-card-no');
    var textWrap = el('span', 'me-card-text');
    textWrap.appendChild(el('strong'));
    textWrap.appendChild(el('span'));
    toggle.appendChild(number);
    toggle.appendChild(textWrap);
    toggle.addEventListener('click', function () { self.setOpen(item, !item.open); });
    head.appendChild(toggle);

    var actions = el('div', 'me-card-actions');
    if (!locked) {
      var up = button('↑', 'btn small secondary', { title: 'Move up', 'data-act': 'up' });
      var down = button('↓', 'btn small secondary', { title: 'Move down', 'data-act': 'down' });
      var copy = button('Duplicate', 'btn small secondary', { 'data-act': 'duplicate' });
      var remove = button('Delete', 'btn small danger', { 'data-act': 'delete' });
      up.addEventListener('click', function () { self.move(item.uid, -1); });
      down.addEventListener('click', function () { self.move(item.uid, 1); });
      copy.addEventListener('click', function () { self.copy(item.uid); });
      remove.addEventListener('click', function () { self.remove(item.uid); });
      var reason = self.canDelete(item);
      if (reason !== true) {
        remove.disabled = true;
        remove.title = reason;
      }
      actions.appendChild(up);
      actions.appendChild(down);
      actions.appendChild(copy);
      actions.appendChild(remove);
    }
    head.appendChild(actions);
    card.appendChild(head);

    var body = el('div', 'me-card-body');
    body.hidden = true;
    card.appendChild(body);

    this.nodes[item.uid] = card;
    this.refreshSummary(item);
    if (item.open) this.setOpen(item, true);

    return card;
  };

  CardList.prototype.refreshSummary = function (item) {
    var card = this.nodes[item.uid];
    if (!card) return;
    var summary = this.summary(item);
    var strong = card.querySelector('.me-card-head .me-card-text strong');
    var small = card.querySelector('.me-card-head .me-card-text span');
    strong.textContent = summary.title || 'Untitled ' + this.noun;
    small.textContent = summary.detail || '';
    small.hidden = !summary.detail;
    var toggle = card.querySelector('.me-card-head .me-card-toggle');
    toggle.setAttribute('aria-label', (item.open ? 'Close ' : 'Edit ') + this.noun + ': ' + (summary.title || 'Untitled'));
    var remove = card.querySelector('.me-card-head [data-act="delete"]');
    if (remove) remove.setAttribute('aria-label', 'Delete ' + this.noun + ' ' + (summary.title || ''));
  };

  CardList.prototype.setOpen = function (item, open) {
    var card = this.nodes[item.uid];
    if (!card) return;
    item.open = open;
    var body = card.querySelector(':scope > .me-card-body');
    card.classList.toggle('is-open', open);
    card.querySelector(':scope > .me-card-head .me-card-toggle').setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) {
      body.innerHTML = '';
      body.appendChild(this.buildBody(item, this));
      body.hidden = false;
      mountCodeEditors(body);
      requestAnimationFrame(function () { body.querySelectorAll('textarea').forEach(autoSize); });
    } else {
      body.hidden = true;
      body.innerHTML = '';
    }
    this.refreshSummary(item);
  };

  CardList.prototype.collapseAll = function () {
    var self = this;
    this.items.forEach(function (item) { if (item.open) self.setOpen(item, false); });
  };

  CardList.prototype.expandAll = function () {
    var self = this;
    this.items.forEach(function (item) { if (!item.open) self.setOpen(item, true); });
  };

  CardList.prototype.refreshNumbers = function () {
    var self = this;
    this.items.forEach(function (item, index) {
      var card = self.nodes[item.uid];
      if (!card) return;
      card.querySelector(':scope > .me-card-head .me-card-no').textContent = String(index + 1);
      var up = card.querySelector(':scope > .me-card-head [data-act="up"]');
      var down = card.querySelector(':scope > .me-card-head [data-act="down"]');
      if (up) {
        up.disabled = index === 0;
        up.setAttribute('aria-label', 'Move ' + self.noun + ' ' + (index + 1) + ' up');
      }
      if (down) {
        down.disabled = index === self.items.length - 1;
        down.setAttribute('aria-label', 'Move ' + self.noun + ' ' + (index + 1) + ' down');
      }
    });
    if (this.emptyNode) this.emptyNode.hidden = this.items.length > 0;
    if (this.onChange) this.onChange();
  };

  CardList.prototype.moveTo = function (from, to) {
    if (from === to || from < 0 || to < 0 || from >= this.items.length || to >= this.items.length) return;
    var item = this.items.splice(from, 1)[0];
    this.items.splice(to, 0, item);
    var card = this.nodes[item.uid];
    var after = this.items[to + 1];
    this.container.insertBefore(card, after ? this.nodes[after.uid] : null);
    this.refreshNumbers();
    markDirty();
  };

  CardList.prototype.move = function (uid, delta) {
    var from = this.indexOf(uid);
    this.moveTo(from, from + delta);
    var card = this.nodes[uid];
    var control = card && card.querySelector(':scope > .me-card-head ' + (delta < 0 ? '[data-act="up"]' : '[data-act="down"]'));
    if (control && !control.disabled) control.focus();
    else if (card) card.querySelector(':scope > .me-card-head .me-card-toggle').focus();
  };

  CardList.prototype.add = function (item, index) {
    if (index === undefined || index < 0 || index > this.items.length) index = this.items.length;
    this.items.splice(index, 0, item);
    var card = this.buildCard(item);
    var after = this.items[index + 1];
    this.container.insertBefore(card, after ? this.nodes[after.uid] : null);
    this.refreshNumbers();
    markDirty();
    this.setOpen(item, true);
    card.scrollIntoView({ block: 'nearest' });
    var first = card.querySelector(':scope > .me-card-body input, :scope > .me-card-body textarea');
    if (first) first.focus({ preventScroll: true });
    return card;
  };

  CardList.prototype.copy = function (uid) {
    var index = this.indexOf(uid);
    if (index < 0) return;
    this.add(this.duplicate(this.items[index]), index + 1);
  };

  CardList.prototype.remove = function (uid) {
    var index = this.indexOf(uid);
    if (index < 0) return;
    var title = this.summary(this.items[index]).title || 'this ' + this.noun;
    if (!window.confirm('Delete ' + this.noun + ' "' + shorten(title, 80) + '"? This takes effect when you save.')) return;
    this.items.splice(index, 1);
    var card = this.nodes[uid];
    delete this.nodes[uid];
    if (card) card.remove();
    this.refreshNumbers();
    markDirty();
    var next = this.items[index] || this.items[index - 1];
    if (next && this.nodes[next.uid]) this.nodes[next.uid].querySelector(':scope > .me-card-head .me-card-toggle').focus();
  };

  CardList.prototype.enableDrag = function () {
    var self = this;
    var dragUid = null;
    var target = null;
    var placeAfter = false;

    function clearMarks() {
      Array.prototype.forEach.call(self.container.children, function (node) {
        node.classList.remove('drop-before', 'drop-after');
      });
    }

    this.container.addEventListener('mousedown', function (event) {
      var grip = event.target.closest('.me-drag');
      if (!grip) return;
      var card = self.ownCard(grip);
      // Only this list's own grip (a nested list has its own cards).
      if (card && grip.closest('.me-card, .me-block') === card) card.draggable = true;
    });

    this.container.addEventListener('mouseup', function () {
      Array.prototype.forEach.call(self.container.children, function (node) { node.draggable = false; });
    });

    this.container.addEventListener('dragstart', function (event) {
      var card = self.ownCard(event.target);
      if (!card || !card.draggable || event.target !== card) return;
      event.stopPropagation();
      dragUid = Number(card.getAttribute('data-uid'));
      card.classList.add('is-dragging');
      event.dataTransfer.effectAllowed = 'move';
      try { event.dataTransfer.setData('text/plain', String(dragUid)); } catch (e) { /* older browsers */ }
    });

    this.container.addEventListener('dragover', function (event) {
      if (dragUid === null) return;
      var card = self.ownCard(event.target);
      if (!card || Number(card.getAttribute('data-uid')) === dragUid) return;
      event.preventDefault();
      event.stopPropagation();
      event.dataTransfer.dropEffect = 'move';
      var rect = card.getBoundingClientRect();
      var after = event.clientY > rect.top + rect.height / 2;
      if (card !== target || after !== placeAfter) {
        clearMarks();
        card.classList.add(after ? 'drop-after' : 'drop-before');
        target = card;
        placeAfter = after;
      }
    });

    this.container.addEventListener('drop', function (event) {
      if (dragUid === null || !target) return;
      event.preventDefault();
      event.stopPropagation();
      var from = self.indexOf(dragUid);
      var to = self.indexOf(Number(target.getAttribute('data-uid')));
      if (placeAfter) to += 1;
      if (from < to) to -= 1;
      clearMarks();
      self.moveTo(from, to);
    });

    this.container.addEventListener('dragend', function () {
      clearMarks();
      Array.prototype.forEach.call(self.container.children, function (node) {
        node.classList.remove('is-dragging');
        node.draggable = false;
      });
      dragUid = null;
      target = null;
    });
  };

  // ── Blocks ───────────────────────────────────────────────────────────

  function wrapBlock(data) {
    var block = isObject(data) ? data : { type: 'paragraph', text: '' };
    if (!BLOCK_TYPES[block.type]) block = { type: 'preserved', label: 'Original formatting', html: '' };
    return { uid: ++nextUid, data: block, open: false };
  }

  function newBlock(type) {
    var data = BLOCK_TYPES[type].make();
    data.type = type;
    return wrapBlock(data);
  }

  function blockSummary(item) {
    var d = item.data;
    var meta = BLOCK_TYPES[d.type] || { label: 'Content' };
    var detail = '';
    if (d.type === 'heading' || d.type === 'subheading' || d.type === 'paragraph' || d.type === 'activity') {
      detail = shorten(plain(d.text), 140);
    } else if (d.type === 'note' || d.type === 'example') {
      detail = shorten((text(d.title).trim() ? d.title + ': ' : '') + plain(d.text), 140);
    } else if (ITEM_TYPES.indexOf(d.type) !== -1) {
      var items = (d.items || []).filter(nonEmpty);
      detail = items.length ? shorten(plain(items[0]), 100) + (items.length > 1 ? ' (' + items.length + ' items)' : '') : '';
    } else if (d.type === 'python_code' || d.type === 'sql_code' || d.type === 'code_snippet') {
      var firstLine = text(d.code).split('\n').filter(function (line) { return line.trim() !== ''; })[0] || '';
      detail = shorten(text(d.title).trim() || firstLine, 120);
    } else if (d.type === 'image') {
      detail = shorten(text(d.caption) || text(d.alt) || text(d.src), 120);
    } else if (d.type === 'table') {
      detail = shorten((text(d.label).trim() ? d.label + ', ' : '') + (d.rows || []).length + ' rows', 120);
    } else if (d.type === 'quiz') {
      var count = (d.questions || []).length;
      detail = count + (count === 1 ? ' question' : ' questions');
    } else if (d.type === 'preserved') {
      detail = text(d.label) || 'Kept exactly as it was';
    }
    return { title: meta.label, detail: detail };
  }

  function buildBlockBody(item, list) {
    var d = item.data;
    var fields = el('div', 'me-fields');
    var refresh = function () { list.refreshSummary(item); };
    d.__onChange = refresh;

    switch (d.type) {
      case 'heading':
      case 'subheading':
        fields.appendChild(inputField(d.type === 'heading' ? 'Heading text' : 'Subheading text', d.text, function (v) { d.text = v; refresh(); }, { maxlength: 500, dataField: 'text' }));
        break;
      case 'paragraph':
        fields.appendChild(areaField('Lesson text', d.text, function (v) { d.text = v; refresh(); }, { rows: 5, dataField: 'text', format: true, hint: 'Separate paragraphs with a blank line. Select words and use B, I or Code to format them.' }));
        break;
      case 'activity':
        fields.appendChild(areaField('Practice activity', d.text, function (v) { d.text = v; refresh(); }, { rows: 3, dataField: 'text', format: true, hint: 'A short task learners do on their own after reading.' }));
        break;
      case 'note':
      case 'example':
        fields.appendChild(inputField('Title (optional)', d.title, function (v) { d.title = v; refresh(); }, { maxlength: 189, placeholder: d.type === 'note' ? 'Important Note' : 'Example' }));
        fields.appendChild(areaField(d.type === 'note' ? 'Note' : 'Example', d.text, function (v) { d.text = v; refresh(); }, { rows: 4, dataField: 'text', format: true }));
        break;
      case 'bulleted_list':
      case 'numbered_list':
      case 'walkthrough':
      case 'mistakes':
      case 'key_points':
      case 'check':
        fields.appendChild(listField(BLOCK_TYPES[d.type].label, d, 'items', d.type === 'walkthrough' ? 'step' : 'item', 'Press Enter at the end of the last item to add another.'));
        break;
      case 'python_code':
      case 'sql_code':
      case 'code_snippet':
        var mode = d.type === 'sql_code' ? 'sql' : (d.type === 'python_code' ? 'python' : 'text');
        fields.appendChild(inputField('Title', d.title, function (v) { d.title = v; refresh(); }, { maxlength: 189, placeholder: d.type === 'sql_code' ? 'Top five customers by revenue' : 'Reading user input' }));
        fields.appendChild(codeField(d.type === 'sql_code' ? 'SQL query' : (d.type === 'python_code' ? 'Python code' : 'Code'), d.code, mode, function (v) { d.code = v; refresh(); }, d.type === 'code_snippet' ? null : (d.type === 'sql_code' ? 'Learners can open this query in the SQL sandbox.' : 'Learners can open this code in the compiler.')));
        fields.appendChild(areaField('Explanation', d.explanation, function (v) { d.explanation = v; }, { rows: 3, format: true, hint: 'What the code does, shown below it.' }));
        if (d.type === 'sql_code') {
          fields.appendChild(areaField('Expected result or description', d.result, function (v) { d.result = v; }, { rows: 3, mono: true }));
        } else {
          fields.appendChild(areaField('Expected output', d.output, function (v) { d.output = v; }, { rows: 3, mono: true }));
        }
        break;
      case 'image':
        fields.appendChild(imageFields(d, refresh));
        break;
      case 'table':
        fields.appendChild(tableFields(d, refresh));
        break;
      case 'quiz':
        fields.appendChild(quizFields(d, refresh));
        break;
      case 'preserved':
        fields.appendChild(preservedFields(d));
        break;
    }

    return fields;
  }

  function imageFields(d, refresh) {
    var wrap = el('div', 'me-fields');
    var thumb = el('img', 'me-image-thumb');
    thumb.alt = '';
    function showThumb() {
      thumb.hidden = !text(d.src).trim();
      if (!thumb.hidden) thumb.src = d.src;
    }
    var urlField = inputField('Image address', d.src, function (v) { d.src = v.trim(); showThumb(); refresh(); }, { placeholder: 'https://... or upload one below', hint: 'An uploaded image, or a full https:// address.' });
    wrap.appendChild(urlField);
    if (!locked && config.imageUploadUrl) {
      var row = el('div', 'action-row');
      var file = el('input');
      file.type = 'file';
      file.accept = 'image/png,image/jpeg,image/webp,image/gif';
      file.hidden = true;
      var pick = button('Upload image', 'btn small secondary');
      var status = el('span', 'field-help');
      pick.addEventListener('click', function () { file.click(); });
      file.addEventListener('change', function () {
        if (!file.files || !file.files[0]) return;
        var body = new FormData();
        body.append('_token', csrf);
        body.append('image', file.files[0]);
        if (config.moduleId) body.append('module_id', String(config.moduleId));
        status.textContent = 'Uploading...';
        fetch(config.imageUploadUrl, { method: 'POST', body: body, credentials: 'same-origin', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
          .then(function (response) { return response.json().then(function (json) { if (!response.ok) throw json; return json; }); })
          .then(function (json) {
            d.src = json.url;
            urlField.querySelector('input').value = json.url;
            status.textContent = 'Uploaded.';
            showThumb();
            refresh();
            markDirty();
          })
          .catch(function (error) {
            status.textContent = (error && error.errors && error.errors.image && error.errors.image[0]) || 'The image could not be uploaded.';
          });
      });
      row.appendChild(pick);
      row.appendChild(status);
      row.appendChild(file);
      wrap.appendChild(row);
    }
    wrap.appendChild(thumb);
    showThumb();
    var two = el('div', 'me-two');
    two.appendChild(inputField('Description for screen readers', d.alt, function (v) { d.alt = v; refresh(); }, { maxlength: 300 }));
    two.appendChild(inputField('Caption (optional)', d.caption, function (v) { d.caption = v; refresh(); }, { maxlength: 1000 }));
    wrap.appendChild(two);
    var width = el('select', 'select');
    [100, 75, 50, 25].forEach(function (value) {
      var option = el('option', null, value + '% of the page width');
      option.value = String(value);
      option.selected = Number(d.width || 100) === value;
      width.appendChild(option);
    });
    width.disabled = locked;
    width.addEventListener('change', function () { d.width = Number(width.value); markDirty(); });
    wrap.appendChild(fieldWrap('Size', null, width));
    return wrap;
  }

  function tableFields(d, refresh) {
    var wrap = el('div', 'me-fields');
    if (!Array.isArray(d.columns)) d.columns = [];
    if (!Array.isArray(d.rows)) d.rows = [];
    wrap.appendChild(inputField('Title (optional)', d.label, function (v) { d.label = v; refresh(); }, { maxlength: 160 }));
    var grid = el('div', 'me-table-grid');
    wrap.appendChild(el('span', 'me-list-label', 'Columns and rows'));
    wrap.appendChild(grid);

    function width() {
      var max = d.columns.length;
      d.rows.forEach(function (row) { if (Array.isArray(row) && row.length > max) max = row.length; });
      return Math.max(1, max);
    }

    function cell(value, onInput, label, header) {
      var input = el('input', 'input' + (header ? ' me-table-head' : ''));
      input.type = 'text';
      input.value = text(value);
      input.readOnly = locked;
      input.setAttribute('aria-label', label);
      input.addEventListener('input', function () { onInput(input.value); markDirty(); refresh(); });
      return input;
    }

    function render() {
      grid.innerHTML = '';
      var cols = width();
      var table = el('table');
      var head = el('tr');
      for (var c = 0; c < cols; c++) {
        (function (c) {
          var th = el('th');
          th.appendChild(cell(d.columns[c], function (v) { while (d.columns.length <= c) d.columns.push(''); d.columns[c] = v; }, 'Column ' + (c + 1) + ' heading', true));
          if (!locked && cols > 1) {
            var removeCol = button('Remove column', 'btn small danger me-table-remove');
            removeCol.addEventListener('click', function () {
              d.columns.splice(c, 1);
              d.rows.forEach(function (row) { if (Array.isArray(row)) row.splice(c, 1); });
              render();
              markDirty();
            });
            th.appendChild(removeCol);
          }
          head.appendChild(th);
        })(c);
      }
      if (!locked) head.appendChild(el('th'));
      table.appendChild(head);
      d.rows.forEach(function (row, r) {
        if (!Array.isArray(row)) d.rows[r] = row = [];
        var tr = el('tr');
        for (var c = 0; c < cols; c++) {
          (function (c) {
            var td = el('td');
            td.appendChild(cell(row[c], function (v) { while (row.length <= c) row.push(''); row[c] = v; }, 'Row ' + (r + 1) + ', column ' + (c + 1), false));
            tr.appendChild(td);
          })(c);
        }
        if (!locked) {
          var tdRemove = el('td');
          var removeRow = button('Remove', 'btn small danger', { 'aria-label': 'Remove row ' + (r + 1) });
          removeRow.addEventListener('click', function () { d.rows.splice(r, 1); render(); markDirty(); refresh(); });
          tdRemove.appendChild(removeRow);
          tr.appendChild(tdRemove);
        }
        table.appendChild(tr);
      });
      grid.appendChild(table);
    }

    render();
    if (!locked) {
      var actions = el('div', 'action-row');
      var addRow = button('+ Add row', 'btn small secondary');
      var addCol = button('+ Add column', 'btn small secondary');
      addRow.addEventListener('click', function () { var row = []; for (var i = 0; i < width(); i++) row.push(''); d.rows.push(row); render(); markDirty(); refresh(); });
      addCol.addEventListener('click', function () { var cols = width(); while (d.columns.length < cols) d.columns.push(''); d.columns.push(''); d.rows.forEach(function (row) { while (row.length < cols) row.push(''); row.push(''); }); render(); markDirty(); });
      actions.appendChild(addRow);
      actions.appendChild(addCol);
      wrap.appendChild(actions);
    }
    wrap.appendChild(areaField('Note below the table (optional)', d.note, function (v) { d.note = v; }, { rows: 2 }));
    return wrap;
  }

  function quizFields(d, refresh) {
    var wrap = el('div', 'me-fields');
    if (!Array.isArray(d.questions)) d.questions = [];
    wrap.appendChild(inputField('Title (optional)', d.title, function (v) { d.title = v; }, { maxlength: 189, placeholder: 'Knowledge Check' }));
    var listNode = el('div', 'me-quiz');
    wrap.appendChild(listNode);

    function render() {
      listNode.innerHTML = '';
      d.questions.forEach(function (q, qi) {
        if (!Array.isArray(q.choices)) q.choices = [];
        var box = el('fieldset', 'me-quiz-question');
        box.appendChild(el('legend', null, 'Question ' + (qi + 1)));
        box.appendChild(areaField('Question', q.question, function (v) { q.question = v; }, { rows: 2, dataField: 'quiz-question' }));
        var choices = el('div', 'me-choices');
        var radioName = 'me-quiz-' + (++nextUid);
        q.choices.forEach(function (choice, ci) {
          var row = el('div', 'me-choice' + (Number(q.answer) === ci ? ' is-correct' : ''));
          var correct = el('label', 'me-choice-correct');
          var radio = el('input');
          radio.type = 'radio';
          radio.name = radioName;
          radio.setAttribute('form', 'module-editor-detached');
          radio.checked = Number(q.answer) === ci;
          radio.disabled = locked;
          radio.addEventListener('change', function () { if (radio.checked) { q.answer = ci; render(); markDirty(); } });
          correct.appendChild(radio);
          correct.appendChild(document.createTextNode('Correct'));
          row.appendChild(correct);
          row.appendChild(el('span', 'me-choice-letter', String.fromCharCode(65 + ci)));
          var input = el('input', 'input');
          input.type = 'text';
          input.value = text(choice);
          input.readOnly = locked;
          input.setAttribute('aria-label', 'Question ' + (qi + 1) + ', choice ' + String.fromCharCode(65 + ci));
          input.setAttribute('data-quiz-choice', String(ci));
          input.addEventListener('input', function () { q.choices[ci] = input.value; markDirty(); });
          row.appendChild(input);
          if (!locked && q.choices.length > 2) {
            var removeChoice = button('Remove', 'btn small danger', { 'aria-label': 'Remove choice ' + String.fromCharCode(65 + ci) });
            removeChoice.addEventListener('click', function () {
              q.choices.splice(ci, 1);
              if (Number(q.answer) === ci) q.answer = -1;
              else if (Number(q.answer) > ci) q.answer = Number(q.answer) - 1;
              render();
              markDirty();
            });
            row.appendChild(removeChoice);
          }
          choices.appendChild(row);
        });
        box.appendChild(choices);
        if (!locked && q.choices.length < 6) {
          var addChoice = button('+ Add choice', 'btn small secondary');
          addChoice.addEventListener('click', function () { q.choices.push(''); render(); markDirty(); });
          box.appendChild(addChoice);
        }
        box.appendChild(areaField('Explanation', q.explanation, function (v) { q.explanation = v; }, { rows: 2, hint: 'Shown after the learner answers.' }));
        if (!locked) {
          var actions = el('div', 'action-row');
          var up = button('↑', 'btn small secondary', { title: 'Move up', 'aria-label': 'Move question ' + (qi + 1) + ' up' });
          var down = button('↓', 'btn small secondary', { title: 'Move down', 'aria-label': 'Move question ' + (qi + 1) + ' down' });
          var remove = button('Remove question', 'btn small danger');
          up.disabled = qi === 0;
          down.disabled = qi === d.questions.length - 1;
          up.addEventListener('click', function () { swap(d.questions, qi, qi - 1); render(); markDirty(); });
          down.addEventListener('click', function () { swap(d.questions, qi, qi + 1); render(); markDirty(); });
          remove.addEventListener('click', function () {
            if (!window.confirm('Remove question ' + (qi + 1) + '?')) return;
            d.questions.splice(qi, 1);
            render();
            markDirty();
            refresh();
          });
          actions.appendChild(up);
          actions.appendChild(down);
          actions.appendChild(remove);
          box.appendChild(actions);
        }
        listNode.appendChild(box);
      });
      requestAnimationFrame(function () { listNode.querySelectorAll('textarea').forEach(autoSize); });
    }

    render();
    if (!locked) {
      var add = button('+ Add question', 'btn small secondary');
      add.addEventListener('click', function () {
        d.questions.push({ question: '', choices: ['', '', '', ''], answer: -1, explanation: '' });
        render();
        markDirty();
        refresh();
      });
      wrap.appendChild(add);
    }
    return wrap;
  }

  function preservedFields(d) {
    var wrap = el('div', 'me-fields');
    wrap.appendChild(el('p', 'me-legacy-note', 'This part of the lesson keeps its original formatting, for example a custom layout or an interactive part with its own script, so nothing in it is lost. Learners see it exactly as before. You can move, duplicate or delete it; to change its content, add new blocks and delete this one.'));
    var summary = '';
    try {
      summary = new DOMParser().parseFromString(text(d.html), 'text/html').body.textContent || '';
    } catch (e) { summary = ''; }
    if (summary.trim()) wrap.appendChild(el('p', 'field-help', 'Starts with: ' + shorten(summary, 240)));
    var previewButton = button('Show how it looks', 'btn small secondary');
    var target = el('div', 'me-section-preview');
    target.hidden = true;
    previewButton.addEventListener('click', function () {
      if (!target.hidden) { target.hidden = true; target.innerHTML = ''; previewButton.textContent = 'Show how it looks'; return; }
      target.hidden = false;
      previewButton.textContent = 'Hide';
      previewFrame(target, postForHtml(config.blockPreviewUrl, { blocks_json: JSON.stringify([cleanBlock(d)]) }), 'Original formatting preview');
    });
    wrap.appendChild(previewButton);
    wrap.appendChild(target);
    return wrap;
  }

  function cleanBlock(data) {
    var out = {};
    Object.keys(data).forEach(function (key) { if (key.indexOf('__') !== 0) out[key] = data[key]; });
    if (ITEM_TYPES.indexOf(out.type) !== -1 && Array.isArray(out.items)) out.items = out.items.filter(nonEmpty);
    return out;
  }

  /** The "+ Add Content" menu of one section. */
  function addContentMenu(onPick) {
    var wrap = el('div', 'me-add-content');
    var toggle = button('+ Add Content', 'btn small', { 'aria-expanded': 'false', 'aria-haspopup': 'true' });
    var menu = el('div', 'me-add-menu');
    menu.hidden = true;
    menu.setAttribute('role', 'menu');
    MENU_TYPES.forEach(function (type) {
      var option = el('button', 'me-add-option');
      option.type = 'button';
      option.setAttribute('role', 'menuitem');
      option.appendChild(el('strong', null, BLOCK_TYPES[type].label));
      option.appendChild(el('span', null, BLOCK_TYPES[type].hint));
      option.addEventListener('click', function () { close(); onPick(type); });
      menu.appendChild(option);
    });
    function close() {
      menu.hidden = true;
      toggle.setAttribute('aria-expanded', 'false');
      document.removeEventListener('click', outside, true);
      document.removeEventListener('keydown', escape, true);
    }
    function outside(event) { if (!wrap.contains(event.target)) close(); }
    function escape(event) { if (event.key === 'Escape') { close(); toggle.focus(); } }
    toggle.addEventListener('click', function () {
      if (!menu.hidden) { close(); return; }
      menu.hidden = false;
      toggle.setAttribute('aria-expanded', 'true');
      document.addEventListener('click', outside, true);
      document.addEventListener('keydown', escape, true);
      var first = menu.querySelector('button');
      if (first) first.focus();
    });
    wrap.appendChild(toggle);
    wrap.appendChild(menu);
    return wrap;
  }

  // ── Sections ─────────────────────────────────────────────────────────

  function wrapSection(data) {
    var s = isObject(data) ? data : {};
    return {
      uid: ++nextUid,
      open: false,
      id: s.id || null,
      source: s.source === undefined || s.source === null ? null : s.source,
      progress: Number(s.progress || 0),
      title: text(s.title !== undefined ? s.title : s.heading),
      blocks: (Array.isArray(s.blocks) ? s.blocks : []).map(wrapBlock)
    };
  }

  function sectionSummary(item) {
    var count = item.blocks.length;
    return { title: item.title.trim(), detail: count + (count === 1 ? ' content block' : ' content blocks') };
  }

  function buildSectionBody(item, list) {
    var fields = el('div', 'me-fields');
    var refresh = function () { list.refreshSummary(item); };

    if (isPublic && item.progress > 0) {
      fields.appendChild(el('p', 'field-help', item.progress + (item.progress === 1 ? ' student has' : ' students have') + ' started this section, so it cannot be deleted. Your edits reach them once you save.'));
    }

    fields.appendChild(inputField('Section title', item.title, function (v) { item.title = v; refresh(); }, {
      maxlength: 189,
      dataField: 'section-title',
      hint: isPublic
        ? 'Shown in the lesson list. It is also the lesson heading, unless the section starts with a Heading block.'
        : 'Shown as the heading of this section.'
    }));

    var toolbar = el('div', 'me-block-toolbar');
    toolbar.appendChild(el('span', 'me-list-label', 'Content'));
    var toolbarActions = el('div', 'action-row');
    var expand = button('Expand all', 'btn small secondary');
    var collapse = button('Collapse all', 'btn small secondary');
    toolbarActions.appendChild(expand);
    toolbarActions.appendChild(collapse);
    toolbar.appendChild(toolbarActions);
    fields.appendChild(toolbar);

    var blockContainer = el('div', 'me-blocks');
    var empty = el('p', 'me-empty', 'No content yet. Use + Add Content to add the first block.');
    fields.appendChild(blockContainer);
    fields.appendChild(empty);

    var blocks = new CardList({
      items: item.blocks,
      container: blockContainer,
      noun: 'block',
      cardClass: 'me-block',
      summary: blockSummary,
      buildBody: buildBlockBody,
      emptyNode: empty,
      duplicate: function (blockItem) {
        var copy = clone(cleanBlock(blockItem.data));
        // A copied knowledge check needs its own element ids on the page.
        if (copy.type === 'quiz') copy.prefix = 'kc' + Math.random().toString(36).slice(2, 10);
        return wrapBlock(copy);
      },
      onChange: refresh
    });
    item.blockList = blocks;
    expand.addEventListener('click', function () { blocks.expandAll(); });
    collapse.addEventListener('click', function () { blocks.collapseAll(); });

    var footer = el('div', 'me-section-footer');
    if (!locked) {
      footer.appendChild(addContentMenu(function (type) { blocks.add(newBlock(type)); }));
    }
    var previewButton = button('Preview section', 'btn small secondary');
    var previewArea = el('div', 'me-section-preview');
    previewArea.hidden = true;
    previewButton.addEventListener('click', function () {
      if (!previewArea.hidden) {
        previewArea.hidden = true;
        previewArea.innerHTML = '';
        previewButton.textContent = 'Preview section';
        return;
      }
      previewButton.textContent = 'Hide preview';
      previewArea.hidden = false;
      var payload = serializeSection(item);
      var request = isPublic
        ? postForHtml(config.sectionPreviewUrl, { title: payload.title || ' ', blocks_json: JSON.stringify(payload.blocks) })
        : postForHtml(config.previewUrl, { section_only: '1', title: text((form.querySelector('[name="title"]') || {}).value), content_sections_json: JSON.stringify([payload]) });
      previewFrame(previewArea, request, 'Section preview');
    });
    footer.appendChild(previewButton);
    fields.appendChild(footer);
    fields.appendChild(previewArea);

    return fields;
  }

  function serializeSection(item) {
    var out = { title: item.title.trim(), blocks: item.blocks.map(function (block) { return cleanBlock(block.data); }) };
    if (isPublic) out.id = item.id;
    else if (item.source !== null) out.source = item.source;
    return out;
  }

  function newSection() {
    var section = wrapSection({ title: '', blocks: [] });
    section.blocks.push(newBlock('paragraph'));
    return section;
  }

  // ── Review questions ─────────────────────────────────────────────────

  function wrapQuestion(data) {
    var q = isObject(data) ? data : {};
    var choices = Array.isArray(q.choices) ? q.choices.map(text) : [];
    var answer = text(q.answer);
    // A new question has no correct answer until one is chosen.
    return { uid: ++nextUid, data: q, open: false, correct: answer === '' ? -1 : choices.indexOf(answer) };
  }

  function questionSummary(item) {
    var d = item.data;
    var question = text(d.question).trim().replace(/\s+/g, ' ');
    var detail = [];
    if (text(d.topic).trim()) detail.push(text(d.topic).trim());
    var difficulty = text(d[difficultyKeyOf(d)]).trim();
    if (difficulty) detail.push(difficulty);
    if (item.correct < 0) detail.push('no correct answer chosen');
    return { title: question.length > 150 ? question.slice(0, 147) + '...' : question, detail: detail.join(', ') };
  }

  function buildQuestionBody(item, list) {
    var d = item.data;
    var fields = el('div', 'me-fields');
    var refresh = function () { list.refreshSummary(item); };

    var choiceValues = Array.isArray(d.choices) ? d.choices.map(text) : [];
    if (!locked) {
      while (choiceValues.length < 4) choiceValues.push('');
      d.choices = choiceValues;
    }

    fields.appendChild(areaField('Question', d.question, function (v) { d.question = v; refresh(); }, { rows: 3, dataField: 'question' }));
    fields.appendChild(areaField('Scenario (optional)', d.scenario, function (v) { d.scenario = v; }, { rows: 3, hint: 'A short situation the question is about.' }));

    var choices = el('fieldset', 'me-choices');
    choices.appendChild(el('legend', null, 'Choices and correct answer'));
    var radioName = 'me-correct-' + item.uid;
    choiceValues.forEach(function (choice, index) {
      var row = el('div', 'me-choice' + (item.correct === index ? ' is-correct' : ''));
      var correctLabel = el('label', 'me-choice-correct');
      var radio = el('input');
      radio.type = 'radio';
      radio.name = radioName;
      radio.setAttribute('form', 'module-editor-detached');
      radio.checked = item.correct === index;
      radio.disabled = locked;
      radio.addEventListener('change', function () {
        if (!radio.checked) return;
        item.correct = index;
        choices.querySelectorAll('.me-choice').forEach(function (node, i) { node.classList.toggle('is-correct', i === index); });
        refresh();
        markDirty();
      });
      correctLabel.appendChild(radio);
      correctLabel.appendChild(document.createTextNode('Correct'));
      row.appendChild(correctLabel);
      var letter = String.fromCharCode(65 + index);
      row.appendChild(el('span', 'me-choice-letter', letter));
      var input = el('input', 'input');
      input.type = 'text';
      input.value = choice;
      input.readOnly = locked;
      input.setAttribute('aria-label', 'Choice ' + letter);
      input.setAttribute('data-choice', String(index));
      input.addEventListener('input', function () { d.choices[index] = input.value; markDirty(); });
      row.appendChild(input);
      choices.appendChild(row);
    });
    fields.appendChild(choices);

    fields.appendChild(areaField('Explanation', d.explanation, function (v) { d.explanation = v; }, { rows: 3, hint: 'Why the correct answer is right. Students see it after they check their answer.' }));
    fields.appendChild(listField('Why the other choices are wrong (optional)', d, 'why_other_choices_are_wrong', 'reason'));
    fields.appendChild(areaField('Learning Tip (optional)', d.learning_tip, function (v) { d.learning_tip = v; }, { rows: 2 }));

    var two = el('div', 'me-two');
    var diffKey = difficultyKeyOf(d);
    var select = el('select', 'select');
    var current = text(d[diffKey]);
    var options = [''].concat(difficulties);
    if (current && options.indexOf(current) === -1) options.push(current);
    options.forEach(function (value) {
      var option = el('option', null, value === '' ? 'Not set' : value);
      option.value = value;
      option.selected = value === current;
      select.appendChild(option);
    });
    select.disabled = locked;
    select.addEventListener('change', function () { d[diffKey] = select.value; refresh(); markDirty(); });
    two.appendChild(fieldWrap('Difficulty', null, select));
    two.appendChild(inputField('Topic', d.topic, function (v) { d.topic = v; refresh(); }, { maxlength: 189 }));
    fields.appendChild(two);

    return fields;
  }

  function newQuestion() {
    return wrapQuestion({ question: '', scenario: '', choices: ['', '', '', ''], answer: '', explanation: '', why_other_choices_are_wrong: [], learning_tip: '', difficulty_level: '', topic: '' });
  }

  function cleanQuestion(item) {
    var out = {};
    Object.keys(item.data).forEach(function (key) { if (key.indexOf('__') !== 0) out[key] = item.data[key]; });
    if (Array.isArray(out.choices) && item.correct >= 0 && item.correct < out.choices.length) {
      out.answer = out.choices[item.correct];
    }
    if (Array.isArray(out.why_other_choices_are_wrong)) {
      out.why_other_choices_are_wrong = out.why_other_choices_are_wrong.filter(nonEmpty);
    }
    return out;
  }

  // ── Lists on the page ────────────────────────────────────────────────

  var sections = (Array.isArray(initial.sections) ? initial.sections : []).map(wrapSection);
  var questions = (Array.isArray(initial.questions) ? initial.questions : []).map(wrapQuestion);
  var filterInput = null;

  var sectionCards = new CardList({
    items: sections,
    container: sectionList,
    noun: 'section',
    summary: sectionSummary,
    buildBody: buildSectionBody,
    emptyNode: root.querySelector('[data-section-empty]'),
    duplicate: function (item) {
      var copy = wrapSection(clone(serializeSection(item)));
      copy.id = null;
      copy.progress = 0;
      copy.title = item.title + ' (copy)';
      return copy;
    },
    canDelete: function (item) {
      return isPublic && item.progress > 0 ? 'Students have started this section, so it cannot be deleted.' : true;
    },
    onChange: function () { setCount('sections', sections.length); }
  });

  var questionCards = new CardList({
    items: questions,
    container: questionList,
    noun: 'question',
    summary: questionSummary,
    buildBody: buildQuestionBody,
    emptyNode: root.querySelector('[data-question-empty]'),
    duplicate: function (item) { return wrapQuestion(clone(cleanQuestion(item))); },
    onChange: function () { setCount('questions', questions.length); applyFilter(); }
  });

  root.querySelectorAll('[data-add-section]').forEach(function (node) {
    node.addEventListener('click', function () { showTab('content'); sectionCards.add(newSection()); });
  });
  root.querySelectorAll('[data-add-question]').forEach(function (node) {
    node.addEventListener('click', function () {
      showTab('questions');
      if (filterInput && filterInput.value) { filterInput.value = ''; applyFilter(); }
      questionCards.add(newQuestion());
    });
  });
  root.querySelectorAll('[data-collapse-all]').forEach(function (node) {
    node.addEventListener('click', function () {
      (node.getAttribute('data-collapse-all') === 'questions' ? questionCards : sectionCards).collapseAll();
    });
  });

  // Find a question by its text or topic.
  filterInput = root.querySelector('[data-question-filter]');
  var filterStatus = root.querySelector('[data-question-filter-status]');
  function applyFilter() {
    if (!filterInput) return;
    var term = filterInput.value.trim().toLowerCase();
    var shown = 0;
    questions.forEach(function (item) {
      var card = questionCards.nodes[item.uid];
      if (!card) return;
      var haystack = (text(item.data.question) + ' ' + text(item.data.topic) + ' ' + text(item.data.scenario)).toLowerCase();
      var match = !term || haystack.indexOf(term) !== -1;
      card.hidden = !match;
      if (match) shown++;
    });
    if (filterStatus) {
      filterStatus.hidden = !term;
      filterStatus.textContent = term ? shown + ' of ' + questions.length + ' questions match.' : '';
    }
  }
  if (filterInput) filterInput.addEventListener('input', applyFilter);

  // ── Learning outcomes ────────────────────────────────────────────────

  var outcomeTemplate = root.querySelector('[data-outcome-template]');
  var outcomeEmpty = root.querySelector('[data-outcome-empty]');

  function refreshOutcomes() {
    var rows = outcomeList.querySelectorAll('[data-outcome]');
    rows.forEach(function (row, index) {
      row.querySelector('[data-outcome-act="up"]').disabled = index === 0;
      row.querySelector('[data-outcome-act="down"]').disabled = index === rows.length - 1;
      row.querySelector('textarea').setAttribute('aria-label', 'Learning outcome ' + (index + 1));
    });
    var filled = Array.prototype.filter.call(outcomeList.querySelectorAll('textarea'), function (area) { return area.value.trim() !== ''; }).length;
    setCount('outcomes', filled);
    if (outcomeEmpty) outcomeEmpty.hidden = rows.length > 0;
    root.querySelectorAll('[data-outcome-warning]').forEach(function (node) { node.hidden = filled > 0; });
  }

  root.querySelector('[data-add-outcome]').addEventListener('click', function () {
    var row = outcomeTemplate.content.firstElementChild.cloneNode(true);
    outcomeList.appendChild(row);
    refreshOutcomes();
    markDirty();
    row.querySelector('textarea').focus();
  });

  outcomeList.addEventListener('click', function (event) {
    var action = event.target.closest('[data-outcome-act]');
    if (!action) return;
    var row = action.closest('[data-outcome]');
    var act = action.getAttribute('data-outcome-act');
    if (act === 'remove') {
      var next = row.nextElementSibling || row.previousElementSibling;
      row.remove();
      if (next) next.querySelector('textarea').focus();
    } else if (act === 'up' && row.previousElementSibling) {
      outcomeList.insertBefore(row, row.previousElementSibling);
      row.querySelector('[data-outcome-act="up"]').focus();
    } else if (act === 'down' && row.nextElementSibling) {
      outcomeList.insertBefore(row.nextElementSibling, row);
      row.querySelector('[data-outcome-act="down"]').focus();
    }
    refreshOutcomes();
    markDirty();
  });
  outcomeList.addEventListener('input', refreshOutcomes);
  refreshOutcomes();

  // ── Tabs ─────────────────────────────────────────────────────────────

  var tabs = Array.prototype.slice.call(root.querySelectorAll('[data-tab]'));
  var TAB_NAMES = tabs.map(function (tab) { return tab.getAttribute('data-tab'); });

  function showTab(name, focus) {
    if (TAB_NAMES.indexOf(name) === -1) name = 'basic';
    tabs.forEach(function (tab) {
      var active = tab.getAttribute('data-tab') === name;
      tab.setAttribute('aria-selected', active ? 'true' : 'false');
      tab.tabIndex = active ? 0 : -1;
      if (active && focus) tab.focus();
    });
    root.querySelectorAll('[data-tab-panel]').forEach(function (panel) {
      panel.hidden = panel.getAttribute('data-tab-panel') !== name;
    });
    if (window.history && window.history.replaceState) {
      window.history.replaceState(null, '', '#' + name);
    }
    if (name === 'outcomes' || name === 'content' || name === 'questions') {
      requestAnimationFrame(function () {
        var panel = root.querySelector('[data-tab-panel="' + name + '"]');
        panel.querySelectorAll('textarea').forEach(autoSize);
        panel.querySelectorAll('.CodeMirror').forEach(function (node) { if (node.CodeMirror) node.CodeMirror.refresh(); });
      });
    }
  }

  tabs.forEach(function (tab, index) {
    tab.addEventListener('click', function () { showTab(tab.getAttribute('data-tab')); });
    tab.addEventListener('keydown', function (event) {
      var next = null;
      if (event.key === 'ArrowRight') next = tabs[(index + 1) % tabs.length];
      if (event.key === 'ArrowLeft') next = tabs[(index - 1 + tabs.length) % tabs.length];
      if (event.key === 'Home') next = tabs[0];
      if (event.key === 'End') next = tabs[tabs.length - 1];
      if (next) {
        event.preventDefault();
        showTab(next.getAttribute('data-tab'), true);
      }
    });
  });

  // After a save the editor opens on the tab it was saved from.
  var savedTab = null;
  try {
    savedTab = window.sessionStorage.getItem('module-editor-tab');
    window.sessionStorage.removeItem('module-editor-tab');
  } catch (e) { savedTab = null; }
  var startTab = config.errorTab || (window.location.hash || '').replace('#', '') || savedTab || 'basic';
  showTab(startTab);

  function currentTab() {
    var active = tabs.filter(function (tab) { return tab.getAttribute('aria-selected') === 'true'; })[0];
    return active ? active.getAttribute('data-tab') : 'basic';
  }

  // ── Saving ───────────────────────────────────────────────────────────

  function serialize() {
    sectionsInput.value = JSON.stringify(sections.map(serializeSection));
    questionsInput.value = JSON.stringify(locked ? questions.map(function (item) { return item.data; }) : questions.map(cleanQuestion));
    sectionsInput.disabled = false;
    questionsInput.disabled = false;
  }

  function problemsFor(intent) {
    var problems = [];
    if (!locked) {
      sections.forEach(function (item, index) {
        if (!item.title.trim()) {
          problems.push({ tab: 'content', list: sectionCards, item: item, field: '[data-field="section-title"]', message: 'Section ' + (index + 1) + ' needs a section title.' });
        }
        item.blocks.forEach(function (block) {
          if (block.data.type !== 'quiz') return;
          (block.data.questions || []).forEach(function (q, qi) {
            var choices = (q.choices || []).map(text).filter(function (c) { return c.trim() !== ''; });
            if (!text(q.question).trim() || choices.length < 2 || !(Number(q.answer) >= 0 && Number(q.answer) < (q.choices || []).length && text(q.choices[Number(q.answer)]).trim())) {
              problems.push({ tab: 'content', list: sectionCards, item: item, block: block, message: 'Section ' + (index + 1) + ', knowledge check question ' + (qi + 1) + ': write the question, at least two choices, and choose the correct one.' });
            }
          });
        });
      });
      questions.forEach(function (item, index) {
        var d = item.data;
        var number = 'Review question ' + (index + 1);
        var choices = Array.isArray(d.choices) ? d.choices.map(text) : [];
        if (!text(d.question).trim()) {
          problems.push({ tab: 'questions', list: questionCards, item: item, field: '[data-field="question"]', message: number + ' needs the question text.' });
        }
        if (choices.length < 2 || choices.some(function (c) { return !c.trim(); })) {
          problems.push({ tab: 'questions', list: questionCards, item: item, field: '[data-choice]', message: number + ': fill in every choice (A to D).' });
        } else if (choices.map(function (c) { return c.trim(); }).filter(function (c, i, all) { return all.indexOf(c) !== i; }).length) {
          problems.push({ tab: 'questions', list: questionCards, item: item, field: '[data-choice]', message: number + ' has two choices with the same text.' });
        } else if (item.correct < 0 || item.correct >= choices.length) {
          problems.push({ tab: 'questions', list: questionCards, item: item, field: 'input[type="radio"]', message: number + ': choose its correct answer.' });
        }
      });
    }
    if (intent === 'publish' || (intent === 'save' && config.isPublished)) {
      var outcomes = Array.prototype.filter.call(outcomeList.querySelectorAll('textarea'), function (area) { return area.value.trim() !== ''; });
      if (!outcomes.length) {
        problems.unshift({ tab: 'outcomes', message: 'Add at least one learning outcome under "What Students Will Learn" before publishing this module.' });
      }
      if (isPublic && !sections.length) {
        problems.push({ tab: 'content', message: 'Add at least one section under "Learning Content" before publishing this module.' });
      }
    }
    return problems;
  }

  function showProblems(problems, headingText) {
    root.querySelectorAll('.has-problem').forEach(function (node) { node.classList.remove('has-problem'); });
    problemsBox.innerHTML = '';
    var heading = el('strong', null, headingText || (problems.length === 1 ? 'Fix this before saving:' : 'Fix these ' + problems.length + ' things before saving:'));
    var list = el('ul');
    problems.slice(0, 12).forEach(function (problem) { list.appendChild(el('li', null, problem.message)); });
    if (problems.length > 12) list.appendChild(el('li', null, 'and ' + (problems.length - 12) + ' more.'));
    problemsBox.appendChild(heading);
    problemsBox.appendChild(list);
    problemsBox.hidden = false;

    var first = problems[0];
    showTab(first.tab);
    problems.forEach(function (problem) {
      if (problem.list && problem.item && problem.list.nodes[problem.item.uid]) {
        problem.list.nodes[problem.item.uid].classList.add('has-problem');
      }
    });
    if (first.list && first.item) {
      if (filterInput && filterInput.value) { filterInput.value = ''; applyFilter(); }
      if (!first.item.open) first.list.setOpen(first.item, true);
      var card = first.list.nodes[first.item.uid];
      if (first.block && first.item.blockList) {
        if (!first.block.open) first.item.blockList.setOpen(first.block, true);
        card = first.item.blockList.nodes[first.block.uid] || card;
        card.classList.add('has-problem');
      }
      var field = card && card.querySelector(first.field || 'input, textarea');
      card.scrollIntoView({ block: 'center' });
      if (field) field.focus({ preventScroll: true });
    } else if (first.element) {
      first.element.scrollIntoView({ block: 'center' });
      first.element.focus({ preventScroll: true });
    } else {
      problemsBox.scrollIntoView({ block: 'nearest' });
    }
  }

  var canSaveInBackground = typeof window.fetch === 'function' && typeof window.FormData === 'function';
  var intentButtons = Array.prototype.slice.call(root.querySelectorAll('button[name="intent"]'));

  form.addEventListener('submit', function (event) {
    var submitter = event.submitter || document.activeElement;
    var intent = submitter && submitter.name === 'intent' ? submitter.value : (config.isPublished ? 'save' : 'draft');
    if (saving) {
      event.preventDefault();
      return;
    }
    var problems = problemsFor(intent);
    if (problems.length) {
      event.preventDefault();
      clearServerErrors();
      setState('Failed to save', 'is-error');
      showProblems(problems);
      return;
    }
    var question = submitter && submitter.getAttribute && submitter.getAttribute('data-confirm');
    if (question && !window.confirm(question)) {
      event.preventDefault();
      return;
    }
    if (canSaveInBackground) {
      event.preventDefault();
      saveInBackground(intent);
      return;
    }
    serialize();
    submitting = true;
    try { window.sessionStorage.setItem('module-editor-tab', currentTab()); } catch (e) { /* storage unavailable */ }
  });

  // ── Saving in the background ─────────────────────────────────────────

  var SAVED_LABELS = { save: 'Saved', draft: 'Saved as draft', publish: 'Saved and published', unpublish: 'Saved and unpublished' };

  function setBusy(busy) {
    intentButtons.forEach(function (node) {
      node.disabled = busy || node.hidden;
    });
    root.querySelector('.me-actionbar').setAttribute('aria-busy', busy ? 'true' : 'false');
  }

  function showStatusButtons() {
    var status = config.isPublished ? 'published' : 'draft';
    intentButtons.forEach(function (node) {
      var show = node.getAttribute('data-when') === status;
      node.hidden = !show;
      node.disabled = !show || saving;
    });
    root.querySelectorAll('[data-status-text]').forEach(function (node) {
      node.textContent = config.isPublished ? 'Published' : 'Draft, not published';
    });
  }

  function showNotice(message) {
    if (!noticeBox) return;
    noticeBox.textContent = message || '';
    noticeBox.hidden = !message;
  }

  function readResponse(response) {
    return response.text().then(function (body) {
      var json = null;
      try { json = body ? JSON.parse(body) : null; } catch (e) { json = null; }
      return { response: response, json: isObject(json) ? json : null };
    });
  }

  function saveInBackground(intent) {
    saving = true;
    clearServerErrors();
    showNotice('');
    problemsBox.hidden = true;
    root.querySelectorAll('.has-problem').forEach(function (node) { node.classList.remove('has-problem'); });

    serialize();
    var data = new FormData(form);
    sectionsInput.disabled = true;
    questionsInput.disabled = true;
    data.set('intent', intent);

    // What this request saves, so edits made while it runs stay unsaved.
    var postedSections = sections.slice();
    var postedChanges = changeCount;

    setBusy(true);
    setState('Saving...', 'is-saving');

    fetch(form.getAttribute('action'), {
      method: 'POST',
      body: data,
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf }
    })
      .then(readResponse)
      .then(function (result) {
        if (result.response.ok && result.json && typeof result.json.message === 'string') {
          applySaved(result.json, intent, postedSections, postedChanges);
        } else {
          saveFailed(result.response.ok ? -1 : result.response.status, result.json || {});
        }
      })
      .catch(function () { saveFailed(0, {}); })
      .then(function () {
        saving = false;
        setBusy(false);
      });
  }

  function applySaved(json, intent, postedSections, postedChanges) {
    // Sections added in the editor are stored now: remember which lesson
    // (DataSensei Modules) or stored position (library) each one is.
    if (Array.isArray(json.sections)) {
      postedSections.forEach(function (item, index) {
        var info = json.sections[index];
        if (!isObject(info)) return;
        if (isPublic) {
          if (info.id) item.id = info.id;
          item.progress = Number(info.progress || 0);
        } else {
          item.source = info.source === undefined ? index : info.source;
        }
      });
    }

    var unchangedSince = changeCount === postedChanges;

    // Details fields as stored (for example module codes in capitals).
    if (unchangedSince && isObject(json.fields)) {
      Object.keys(json.fields).forEach(function (name) {
        var input = form.querySelector('[name="' + name + '"]');
        if (input && typeof json.fields[name] === 'string' && input.value !== json.fields[name]) input.value = json.fields[name];
      });
    }

    // A new module is now saved: later saves update it.
    if (json.created && json.update_url) {
      form.setAttribute('action', json.update_url);
      var method = form.querySelector('input[name="_method"]');
      if (!method) {
        method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        form.insertBefore(method, form.firstChild);
      }
      method.value = 'PUT';
      if (json.module_id && isPublic) config.moduleId = json.module_id;
      var cancel = root.querySelector('[data-editor-cancel]');
      if (cancel && json.cancel_url) cancel.setAttribute('href', json.cancel_url);
      if (json.page_title) {
        var heading = document.querySelector('.ds-page-title');
        if (heading) heading.textContent = json.page_title;
        document.title = json.page_title + ' — DataSensei';
      }
      if (json.edit_url && window.history && window.history.replaceState) {
        window.history.replaceState(null, '', json.edit_url + '#' + currentTab());
      }
    }

    if (typeof json.is_published === 'boolean') config.isPublished = json.is_published;
    showStatusButtons();

    if (json.created || intent === 'publish' || intent === 'unpublish') showNotice(json.message);

    if (unchangedSince) {
      dirty = false;
      setState(SAVED_LABELS[intent] || 'Saved', 'is-saved');
    } else {
      setState('Saved. Newer changes are not saved yet.', 'is-dirty');
    }
  }

  function saveFailed(status, json) {
    setState('Failed to save', 'is-error');

    if (status === 422 && isObject(json.errors)) {
      showServerErrors(json.errors);
      return;
    }

    var message;
    if (status === 419) {
      message = 'Your session has expired, so the changes were not saved. They are still in the editor: copy anything you need, then reload the page and sign in again.';
    } else if (status === 401) {
      message = 'You are signed out, so the changes were not saved. They are still in the editor: copy anything you need, then reload the page and sign in again.';
    } else if (status === 403) {
      message = 'You are not allowed to save this module. The changes were not saved.';
    } else if (status === 413) {
      message = 'The module is too large to send in one save. The changes were not saved; they are still in the editor.';
    } else if (status === 0) {
      message = 'Could not reach the server, so the changes were not saved. They are still in the editor; check your connection and save again.';
    } else if (status === -1) {
      message = 'The server did not confirm the save. Your changes are still in the editor; save again, or reload the page to check what was stored.';
    } else {
      message = (typeof json.message === 'string' && json.message && status < 500 ? json.message + ' ' : '')
        + 'The changes were not saved (error ' + status + '). They are still in the editor; try saving again.';
    }
    showProblems([{ tab: currentTab(), message: message }], 'Failed to save.');
  }

  // Server validation errors, shown beside the fields they belong to.
  function clearServerErrors() {
    root.querySelectorAll('.me-field-error, .me-card-error').forEach(function (node) { node.remove(); });
    root.querySelectorAll('.is-invalid').forEach(function (node) { node.classList.remove('is-invalid'); });
  }

  function fieldError(input, message) {
    input.classList.add('is-invalid');
    input.setAttribute('aria-invalid', 'true');
    var holder = input.closest('.field') || input.closest('[data-outcome]') || input.parentNode;
    var note = el('p', 'me-field-error', message);
    note.setAttribute('data-error-for', input.name || '');
    holder.appendChild(note);
  }

  function cardError(list, item, message) {
    var card = item && list.nodes[item.uid];
    if (!card) return;
    var note = el('p', 'me-card-error', message);
    card.insertBefore(note, card.querySelector(':scope > .me-card-body'));
  }

  function showServerErrors(errors) {
    var problems = [];
    var messages = [];
    Object.keys(errors).forEach(function (key) {
      [].concat(errors[key]).forEach(function (message) {
        if (typeof message === 'string' && message) messages.push({ key: key, message: message });
      });
    });

    messages.forEach(function (entry) {
      var key = entry.key;
      var message = entry.message;
      var outcomeMatch = /^learning_outcomes(?:\.(\d+))?$/.exec(key);

      if (outcomeMatch) {
        var areas = outcomeList.querySelectorAll('textarea');
        var outcomeNo = outcomeMatch[1] !== undefined ? Number(outcomeMatch[1]) : (/learning outcome (\d+)/i.exec(message) ? Number(/learning outcome (\d+)/i.exec(message)[1]) - 1 : -1);
        var area = outcomeNo >= 0 ? areas[outcomeNo] : null;
        if (area) fieldError(area, message);
        else {
          var note = el('p', 'me-field-error', message);
          outcomeList.parentNode.insertBefore(note, outcomeList.nextSibling);
        }
        problems.push({ tab: 'outcomes', element: area || null, message: message });
        return;
      }

      if (key === sectionsInput.name) {
        var sectionNo = /section (\d+)/i.exec(message);
        var section = sectionNo ? sections[Number(sectionNo[1]) - 1] : null;
        if (section) cardError(sectionCards, section, message);
        problems.push({ tab: 'content', list: section ? sectionCards : null, item: section, message: message });
        return;
      }

      if (key === questionsInput.name) {
        var questionNo = /question (\d+)/i.exec(message);
        var question = questionNo ? questions[Number(questionNo[1]) - 1] : null;
        if (question) cardError(questionCards, question, message);
        problems.push({ tab: 'questions', list: question ? questionCards : null, item: question, message: message });
        return;
      }

      var name = key.replace(/"/g, '');
      var input = form.querySelector('input[name="' + name + '"]:not([type="hidden"]), select[name="' + name + '"], textarea[name="' + name + '"]');
      if (input) {
        fieldError(input, message);
        var panel = input.closest('[data-tab-panel]');
        problems.push({ tab: panel ? panel.getAttribute('data-tab-panel') : 'basic', element: input, message: message });
        return;
      }

      problems.push({ tab: currentTab(), message: message });
    });

    if (!problems.length) {
      problems.push({ tab: currentTab(), message: 'The changes could not be saved. Check the module and try again.' });
    }
    showProblems(problems, problems.length === 1 ? 'Not saved. Fix this, then save again:' : 'Not saved. Fix these ' + problems.length + ' things, then save again:');
  }

  // Editing a field clears the error shown beside it.
  form.addEventListener('input', function (event) {
    var target = event.target;
    if (!target.classList || !target.classList.contains('is-invalid')) return;
    target.classList.remove('is-invalid');
    target.removeAttribute('aria-invalid');
    var holder = target.closest('.field') || target.closest('[data-outcome]') || target.parentNode;
    holder.querySelectorAll('.me-field-error').forEach(function (node) { node.remove(); });
  });

  form.addEventListener('input', function (event) {
    if (event.target.closest('[data-no-dirty]')) return;
    markDirty();
  });
  form.addEventListener('change', function (event) {
    if (event.target.closest('[data-no-dirty]') || event.target.getAttribute('form') === 'module-editor-detached' || event.target.type === 'file') return;
    markDirty();
  });

  window.addEventListener('beforeunload', function (event) {
    if (!dirty || submitting) return;
    event.preventDefault();
    event.returnValue = '';
  });

  // ── Preview Module ───────────────────────────────────────────────────

  root.querySelectorAll('[data-preview-module]').forEach(function (node) {
    node.addEventListener('click', function () {
      serialize();
      var data = new FormData(form);
      sectionsInput.disabled = true;
      questionsInput.disabled = true;
      var temp = document.createElement('form');
      temp.method = 'POST';
      temp.action = config.previewUrl;
      temp.target = '_blank';
      temp.hidden = true;
      data.forEach(function (value, key) {
        if (key === '_method' || key === 'intent' || typeof value !== 'string') return;
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        temp.appendChild(input);
      });
      document.body.appendChild(temp);
      temp.submit();
      temp.remove();
    });
  });
})();
