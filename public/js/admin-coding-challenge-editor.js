(function () {
  'use strict';

  // Editor for the admin coding challenge form (admin/coding-challenges/_form.blade.php):
  // problem and test-case add/remove/move with input renumbering, the
  // "Check test cases" call, and the learner-view live preview.

  var CHECK_MAX_CASES = 30;

  function notify(message, type) {
    if (window.DataSenseiNotifications && typeof window.DataSenseiNotifications.show === 'function') {
      window.DataSenseiNotifications.show(message, { type: type || 'warning', duration: type === 'success' ? 4000 : 0 });
    } else if (type !== 'success') {
      window.alert(message);
    }
  }

  function debounce(fn, wait) {
    var timer = null;
    return function () {
      window.clearTimeout(timer);
      timer = window.setTimeout(fn, wait);
    };
  }

  function el(tag, className, text) {
    var node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined && text !== null) node.textContent = text;
    return node;
  }

  function setName(node, name) {
    if (node) node.name = name;
  }

  function questionItems(form) {
    return Array.from(form.querySelectorAll('[data-question-list] > .question-item'));
  }

  function caseItems(question) {
    return Array.from(question.querySelectorAll('[data-test-case-list] > [data-test-case]'));
  }

  // ── Names and numbering ─────────────────────────────────────────────
  function renumber(form) {
    questionItems(form).forEach(function (question, qi) {
      var number = question.querySelector('[data-question-number]');
      if (number) number.textContent = String(qi + 1);

      ['title', 'problem_description', 'language', 'starter_code', 'reference_solution', 'time_limit_seconds', 'base_xp'].forEach(function (field) {
        setName(question.querySelector('[data-field="' + field + '"]'), 'questions[' + qi + '][' + field + ']');
      });

      caseItems(question).forEach(function (testCase, ci) {
        var caseNumber = testCase.querySelector('[data-test-case-number]');
        if (caseNumber) caseNumber.textContent = String(ci + 1);

        var prefix = 'questions[' + qi + '][test_cases][' + ci + ']';
        setName(testCase.querySelector('[data-field="input"]'), prefix + '[input]');
        setName(testCase.querySelector('[data-field="expected_output"]'), prefix + '[expected_output]');
        setName(testCase.querySelector('[data-field="is_hidden"]'), prefix + '[is_hidden]');
      });
    });
  }

  function cloneTemplate(template) {
    return template.content.firstElementChild.cloneNode(true);
  }

  function moveItem(item, direction) {
    if (!item || !item.parentElement) return;
    if (direction === 'up' && item.previousElementSibling) {
      item.parentElement.insertBefore(item, item.previousElementSibling);
    }
    if (direction === 'down' && item.nextElementSibling) {
      item.parentElement.insertBefore(item.nextElementSibling, item);
    }
  }

  // ── Check test cases ────────────────────────────────────────────────
  function readCases(question) {
    return caseItems(question).map(function (testCase) {
      var input = testCase.querySelector('[data-field="input"]');
      var expected = testCase.querySelector('[data-field="expected_output"]');
      var hidden = testCase.querySelector('[data-field="is_hidden"]');
      return {
        input: input ? String(input.value || '') : '',
        expected_output: expected ? String(expected.value || '') : '',
        is_hidden: !!(hidden && hidden.checked) ? 1 : 0
      };
    });
  }

  function clearCaseResults(question) {
    caseItems(question).forEach(function (testCase) {
      var box = testCase.querySelector('[data-case-result]');
      if (!box) return;
      box.textContent = '';
      box.className = 'cc-case-result';
      box.hidden = true;
    });
  }

  function renderCaseResult(testCase, result) {
    var box = testCase.querySelector('[data-case-result]');
    if (!box) return;

    box.textContent = '';
    box.className = 'cc-case-result ' + (result.passed ? 'is-pass' : 'is-fail');
    box.hidden = false;

    var headline = result.passed ? 'Passed' : 'Failed';
    if (!result.passed && result.timed_out) headline = 'Failed: time limit exceeded';
    else if (!result.passed && result.failed) headline = 'Failed: the program ended with an error';
    else if (!result.passed) headline = 'Failed: output did not match';
    box.appendChild(el('strong', null, headline));

    var grid = el('div', 'cc-case-result-grid');
    var expectedCol = el('div');
    expectedCol.appendChild(el('span', null, 'Expected output'));
    expectedCol.appendChild(el('pre', null, result.expected || ''));
    var actualCol = el('div');
    actualCol.appendChild(el('span', null, 'Actual output'));
    actualCol.appendChild(el('pre', null, result.actual || ''));
    grid.appendChild(expectedCol);
    grid.appendChild(actualCol);
    box.appendChild(grid);

    if (result.stderr) {
      var err = el('div', 'cc-stderr');
      err.appendChild(el('span', null, 'Error output'));
      err.appendChild(el('pre', null, result.stderr));
      box.appendChild(err);
    }
  }

  function checkTests(form, question, button) {
    var url = form.getAttribute('data-check-url');
    var tokenInput = form.querySelector('input[name="_token"]');
    var status = question.querySelector('[data-check-status]');
    var solution = question.querySelector('[data-field="reference_solution"]');
    var limit = question.querySelector('[data-field="time_limit_seconds"]');
    var cases = readCases(question);

    if (!url || !tokenInput) {
      notify('Test case checking is not available on this page.');
      return;
    }

    var code = solution ? String(solution.value || '') : '';
    if (code.trim() === '') {
      notify('Enter a reference solution for this problem before checking its test cases.');
      if (solution) solution.focus();
      return;
    }

    if (!cases.length) {
      notify('Add at least one test case before checking.');
      return;
    }

    clearCaseResults(question);
    if (status) status.textContent = cases.length > CHECK_MAX_CASES
      ? 'Running the first ' + CHECK_MAX_CASES + ' test cases…'
      : 'Running ' + cases.length + ' test case' + (cases.length === 1 ? '' : 's') + '…';
    button.disabled = true;

    window.fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': tokenInput.value
      },
      body: JSON.stringify({
        reference_solution: code,
        time_limit_seconds: limit ? parseInt(limit.value, 10) || null : null,
        test_cases: cases
      })
    }).then(function (response) {
      return response.json().catch(function () { return {}; }).then(function (payload) {
        if (!response.ok) {
          var message = payload && payload.message ? payload.message : 'The test cases could not be checked.';
          if (payload && payload.errors) {
            var first = Object.keys(payload.errors)[0];
            if (first && payload.errors[first] && payload.errors[first][0]) message = payload.errors[first][0];
          }
          throw new Error(message);
        }
        return payload;
      });
    }).then(function (payload) {
      var results = Array.isArray(payload.results) ? payload.results : [];
      var items = caseItems(question);

      results.forEach(function (result) {
        var index = typeof result.index === 'number' ? result.index : -1;
        if (items[index]) renderCaseResult(items[index], result);
      });

      var summary = payload.summary || {};
      var text = String(summary.passed || 0) + ' of ' + String(summary.total || results.length) + ' test cases passed.';
      if (summary.capped) text += ' Only the first ' + CHECK_MAX_CASES + ' were run.';
      if (status) status.textContent = text;
    }).catch(function (error) {
      if (status) status.textContent = '';
      notify(error && error.message ? error.message : 'The test cases could not be checked.');
    }).then(function () {
      button.disabled = false;
    });
  }

  // ── Live preview ────────────────────────────────────────────────────
  function readForm(form) {
    var value = function (selector) {
      var node = form.querySelector(selector);
      return node ? String(node.value || '') : '';
    };

    var problems = questionItems(form).map(function (question) {
      var field = function (name) {
        var node = question.querySelector('[data-field="' + name + '"]');
        return node ? String(node.value || '') : '';
      };
      var cases = readCases(question);
      return {
        title: field('title').trim(),
        description: field('problem_description'),
        starter: field('starter_code'),
        timeLimit: parseInt(field('time_limit_seconds'), 10),
        xp: parseInt(field('base_xp'), 10),
        visibleCases: cases.filter(function (c) { return !c.is_hidden; }),
        hiddenCount: cases.filter(function (c) { return !!c.is_hidden; }).length
      };
    });

    return {
      title: value('[name="title"]').trim(),
      description: value('[name="description"]').trim(),
      xp: parseInt(value('[name="base_xp"]'), 10),
      timeLimit: parseInt(value('[name="time_limit_seconds"]'), 10),
      problems: problems
    };
  }

  function caseRow(label, text) {
    var row = el('div', 'cc-preview-case-row');
    row.appendChild(el('span', 'cc-preview-case-key', label));
    row.appendChild(el('span', 'cc-preview-case-val', text));
    return row;
  }

  function renderPreview(form, preview) {
    var data = readForm(form);

    var title = preview.querySelector('[data-preview-title]');
    if (title) title.textContent = data.title || 'Untitled challenge';

    var description = preview.querySelector('[data-preview-description]');
    if (description) {
      description.textContent = data.description;
      description.hidden = data.description === '';
    }

    var xp = preview.querySelector('[data-preview-xp]');
    if (xp) xp.textContent = String(isNaN(data.xp) ? 0 : data.xp);
    var time = preview.querySelector('[data-preview-time]');
    if (time) time.textContent = String(isNaN(data.timeLimit) ? 0 : Math.floor(data.timeLimit / 60));
    var count = preview.querySelector('[data-preview-count]');
    if (count) count.textContent = String(data.problems.length);

    var container = preview.querySelector('[data-preview-problems]');
    if (!container) return;

    var fragment = document.createDocumentFragment();
    if (!data.problems.length) {
      fragment.appendChild(el('p', 'cc-preview-empty', 'Add a problem to see it here.'));
    }

    data.problems.forEach(function (problem, index) {
      var card = el('article', 'cc-preview-problem');
      card.appendChild(el('div', 'cc-preview-qnum', 'Question ' + (index + 1) + ' of ' + data.problems.length));
      card.appendChild(el('h4', 'cc-preview-ptitle', problem.title || 'Problem ' + (index + 1)));
      card.appendChild(el('p', 'cc-preview-pdesc', problem.description.trim() || 'Problem description goes here.'));

      var limits = [];
      if (!isNaN(problem.timeLimit)) limits.push(Math.floor(problem.timeLimit / 60) + ' min');
      if (!isNaN(problem.xp)) limits.push(problem.xp + ' XP');
      if (limits.length) card.appendChild(el('p', 'cc-preview-limits', limits.join(', ')));

      if (problem.starter.trim() !== '') {
        card.appendChild(el('div', 'cc-preview-label', 'Starter code'));
        card.appendChild(el('pre', 'cc-preview-code', problem.starter));
      }

      if (problem.visibleCases.length) {
        card.appendChild(el('div', 'cc-preview-label', 'Sample test cases'));
        problem.visibleCases.forEach(function (testCase) {
          var box = el('div', 'cc-preview-case');
          if (testCase.input.trim() !== '') box.appendChild(caseRow('Input:', testCase.input));
          box.appendChild(caseRow('Expected:', testCase.expected_output));
          card.appendChild(box);
        });
      }

      if (problem.hiddenCount > 0) {
        card.appendChild(el('p', 'cc-preview-hidden', problem.hiddenCount + ' hidden test case' + (problem.hiddenCount === 1 ? '' : 's')));
      }

      fragment.appendChild(card);
    });

    container.textContent = '';
    container.appendChild(fragment);
  }

  // ── Wiring ──────────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('form[data-coding-form]');
    if (!form) return;

    var locked = form.getAttribute('data-locked') === '1';
    var editor = form.querySelector('[data-question-editor]');
    var questionList = editor ? editor.querySelector('[data-question-list]') : null;
    var questionTemplate = editor ? editor.querySelector('template[data-question-template]') : null;
    var caseTemplate = editor ? editor.querySelector('template[data-test-case-template]') : null;
    var preview = document.querySelector('[data-coding-preview]');

    var render = function () {
      renumber(form);
      if (preview) renderPreview(form, preview);
    };
    var schedulePreview = debounce(render, 150);

    form.addEventListener('click', function (event) {
      var button = event.target.closest('button');
      if (!button || !editor) return;

      if (button.matches('[data-check-tests]')) {
        event.preventDefault();
        var questionForCheck = button.closest('.question-item');
        if (questionForCheck) checkTests(form, questionForCheck, button);
        return;
      }

      if (locked) return;

      if (button.matches('[data-add-question]') && questionTemplate && questionList) {
        event.preventDefault();
        var question = cloneTemplate(questionTemplate);
        questionList.appendChild(question);
        render();
        var focus = question.querySelector('[data-field="title"]');
        if (focus) focus.focus();
        return;
      }

      if (button.matches('[data-add-test-case]') && caseTemplate) {
        event.preventDefault();
        var questionForCase = button.closest('.question-item');
        var list = questionForCase ? questionForCase.querySelector('[data-test-case-list]') : null;
        if (list) list.appendChild(cloneTemplate(caseTemplate));
        render();
        return;
      }

      if (button.matches('[data-remove-question]')) {
        event.preventDefault();
        if (questionItems(form).length <= 1) {
          notify('At least one problem is required.');
          return;
        }
        var toRemove = button.closest('.question-item');
        if (toRemove) toRemove.remove();
        render();
        return;
      }

      if (button.matches('[data-remove-test-case]')) {
        event.preventDefault();
        var caseToRemove = button.closest('[data-test-case]');
        var owner = caseToRemove ? caseToRemove.closest('.question-item') : null;
        if (owner && caseItems(owner).length <= 1) {
          notify('Every problem needs at least one test case.');
          return;
        }
        if (caseToRemove) caseToRemove.remove();
        render();
        return;
      }

      if (button.matches('[data-move-up]') || button.matches('[data-move-down]')) {
        event.preventDefault();
        var item = button.closest('[data-test-case]') || button.closest('.question-item');
        moveItem(item, button.matches('[data-move-up]') ? 'up' : 'down');
        render();
      }
    });

    form.addEventListener('input', schedulePreview);
    form.addEventListener('change', schedulePreview);
    form.addEventListener('submit', function () { renumber(form); });

    render();
  });
})();
