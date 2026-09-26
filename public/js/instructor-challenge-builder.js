(function () {
  'use strict';

  // Instructor challenge builder (instructor/challenge-builder/_mcq_form and
  // _coding_form). One file, two editors:
  //   - form[data-mcq-form]: question and choice add/remove/move with input
  //     renumbering, picture upload and the learner-view live preview
  //     (same behaviour as admin-content-manager.js + admin-mcq-preview.js);
  //   - form[data-coding-form]: problem and test-case editing, the
  //     "Check test cases" call and its live preview
  //     (same behaviour as admin-coding-challenge-editor.js).

  var IMAGE_PATH_PATTERN = /^\/uploads\/challenges\/(?!.*\.\.)[A-Za-z0-9_\-.\/]+\.(png|jpe?g|gif|webp)$/i;
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

  function questionItems(form) {
    return Array.from(form.querySelectorAll('[data-question-list] > .question-item'));
  }

  function readValue(form, selector) {
    var node = form.querySelector(selector);
    return node ? String(node.value || '') : '';
  }

  function renderHeader(form, preview) {
    var title = preview.querySelector('[data-preview-title]');
    if (title) title.textContent = readValue(form, '[name="title"]').trim() || 'Untitled challenge';

    var descriptionText = readValue(form, '[name="description"]').trim();
    var description = preview.querySelector('[data-preview-description]');
    if (description) {
      description.textContent = descriptionText;
      description.hidden = descriptionText === '';
    }

    var xpValue = parseInt(readValue(form, '[name="base_xp"]'), 10);
    var xp = preview.querySelector('[data-preview-xp]');
    if (xp) xp.textContent = String(isNaN(xpValue) ? 0 : xpValue);

    var timeValue = parseInt(readValue(form, '[name="time_limit_seconds"]'), 10);
    var time = preview.querySelector('[data-preview-time]');
    if (time) time.textContent = String(isNaN(timeValue) ? 0 : Math.floor(timeValue / 60));
  }

  // ════════════════════════════════════════════════════════════════════
  // MCQ editor
  // ════════════════════════════════════════════════════════════════════

  function mcqRenumber(form) {
    questionItems(form).forEach(function (question, qi) {
      var number = question.querySelector('[data-question-number]');
      if (number) number.textContent = String(qi + 1);

      setName(question.querySelector('[data-field="question_text"]'), 'questions[' + qi + '][question_text]');
      setName(question.querySelector('[data-field="image_path"]'), 'questions[' + qi + '][image_path]');

      var options = Array.from(question.querySelectorAll('[data-option-list] > .option-item'));
      options.forEach(function (option, oi) {
        var optionNumber = option.querySelector('[data-option-number]');
        if (optionNumber) optionNumber.textContent = String(oi + 1);

        setName(option.querySelector('[data-field="option_text"]'), 'questions[' + qi + '][options][' + oi + '][option_text]');
        var radio = option.querySelector('[data-field="correct_option"]');
        if (radio) {
          radio.name = 'questions[' + qi + '][correct_option]';
          radio.value = String(oi);
        }
      });

      if (options.length && !options.some(function (option) {
        var radio = option.querySelector('[data-field="correct_option"]');
        return radio && radio.checked;
      })) {
        var firstRadio = options[0].querySelector('[data-field="correct_option"]');
        if (firstRadio) firstRadio.checked = true;
      }
    });
  }

  function setQuestionImage(question, path) {
    var hidden = question.querySelector('[data-field="image_path"]');
    var thumb = question.querySelector('[data-image-thumb]');
    var removeButton = question.querySelector('[data-remove-image]');
    var fileInput = question.querySelector('[data-image-file]');
    var value = IMAGE_PATH_PATTERN.test(path || '') ? path : '';

    if (hidden) hidden.value = value;
    if (thumb) {
      if (value) {
        thumb.src = value;
        thumb.hidden = false;
      } else {
        thumb.removeAttribute('src');
        thumb.hidden = true;
      }
    }
    if (removeButton) removeButton.hidden = !value;
    if (fileInput && !value) fileInput.value = '';
  }

  function uploadImage(form, question, file, afterUpload) {
    var url = form.getAttribute('data-image-upload-url');
    var tokenInput = form.querySelector('input[name="_token"]');
    var status = question.querySelector('[data-image-status]');
    var fileInput = question.querySelector('[data-image-file]');

    if (!url || !tokenInput) {
      notify('Picture upload is not available on this page.');
      return;
    }

    if (file.size > 5 * 1024 * 1024) {
      notify('The picture must be 5 MB or smaller.');
      if (fileInput) fileInput.value = '';
      return;
    }

    var body = new FormData();
    body.append('image', file);
    body.append('_token', tokenInput.value);

    if (status) status.textContent = 'Uploading…';
    if (fileInput) fileInput.disabled = true;

    window.fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: body
    }).then(function (response) {
      return response.json().catch(function () { return {}; }).then(function (payload) {
        if (!response.ok) {
          var message = payload && payload.message ? payload.message : 'The picture could not be uploaded.';
          if (payload && payload.errors && payload.errors.image && payload.errors.image[0]) {
            message = payload.errors.image[0];
          }
          throw new Error(message);
        }
        return payload;
      });
    }).then(function (payload) {
      if (!payload || typeof payload.url !== 'string' || !IMAGE_PATH_PATTERN.test(payload.url)) {
        throw new Error('The server returned an unexpected picture address.');
      }
      setQuestionImage(question, payload.url);
      if (status) status.textContent = 'Picture attached.';
      window.setTimeout(function () { if (status && status.textContent === 'Picture attached.') status.textContent = ''; }, 3000);
      afterUpload();
    }).catch(function (error) {
      setQuestionImage(question, '');
      if (status) status.textContent = '';
      notify(error && error.message ? error.message : 'The picture could not be uploaded.');
    }).then(function () {
      if (fileInput) {
        fileInput.disabled = false;
        fileInput.value = '';
      }
    });
  }

  function mcqReadQuestions(form) {
    return questionItems(form).map(function (question) {
      var text = question.querySelector('[data-field="question_text"]');
      var image = question.querySelector('[data-field="image_path"]');
      var options = Array.from(question.querySelectorAll('[data-option-list] > .option-item')).map(function (option) {
        var input = option.querySelector('[data-field="option_text"]');
        var radio = option.querySelector('[data-field="correct_option"]');
        return {
          text: input ? String(input.value || '') : '',
          correct: !!(radio && radio.checked)
        };
      });

      return {
        text: text ? String(text.value || '') : '',
        image: image && IMAGE_PATH_PATTERN.test(image.value || '') ? image.value : '',
        options: options
      };
    });
  }

  function mcqRenderPreview(form, preview) {
    renderHeader(form, preview);

    var container = preview.querySelector('[data-preview-questions]');
    if (!container) return;

    var questions = mcqReadQuestions(form);
    var fragment = document.createDocumentFragment();

    if (!questions.length) {
      fragment.appendChild(el('p', 'mcq-preview-empty', 'Add a question to see it here.'));
    }

    questions.forEach(function (question, index) {
      var card = el('article', 'mcq-preview-question');
      var head = el('div', 'mcq-preview-q-head');
      head.appendChild(el('div', 'mcq-preview-q-number', String(index + 1)));
      head.appendChild(el('div', 'mcq-preview-q-text', question.text.trim() || 'Question text goes here.'));
      card.appendChild(head);

      if (question.image) {
        var img = el('img', 'mcq-preview-q-image');
        img.src = question.image;
        img.alt = '';
        card.appendChild(img);
      }

      var list = el('ul', 'mcq-preview-options');
      question.options.forEach(function (option, optionIndex) {
        var item = el('li', 'mcq-preview-option' + (option.correct ? ' is-correct' : ''));
        item.appendChild(el('span', 'mcq-preview-option-radio'));
        item.appendChild(el('span', null, option.text.trim() || 'Choice ' + (optionIndex + 1)));
        if (option.correct) item.appendChild(el('span', 'mcq-preview-option-note', 'Correct'));
        list.appendChild(item);
      });
      card.appendChild(list);

      fragment.appendChild(card);
    });

    container.textContent = '';
    container.appendChild(fragment);
  }

  function initMcq(form) {
    var editor = form.querySelector('[data-question-editor]');
    var questionList = editor ? editor.querySelector('[data-question-list]') : null;
    var questionTemplate = editor ? editor.querySelector('template[data-question-template]') : null;
    var optionTemplate = editor ? editor.querySelector('template[data-option-template]') : null;
    var preview = document.querySelector('[data-mcq-preview]');

    var render = function () {
      mcqRenumber(form);
      if (preview) mcqRenderPreview(form, preview);
    };
    var schedule = debounce(render, 150);

    form.addEventListener('click', function (event) {
      var button = event.target.closest('button');
      if (!button) return;

      if (button.matches('[data-remove-image]')) {
        event.preventDefault();
        var questionForImage = button.closest('.question-item');
        if (questionForImage) setQuestionImage(questionForImage, '');
        schedule();
        return;
      }

      if (!editor) return;

      if (button.matches('[data-add-question]') && questionTemplate && questionList) {
        event.preventDefault();
        var question = cloneTemplate(questionTemplate);
        questionList.appendChild(question);
        render();
        var focus = question.querySelector('[data-field="question_text"]');
        if (focus) focus.focus();
        return;
      }

      if (button.matches('[data-add-option]') && optionTemplate) {
        event.preventDefault();
        var questionForOption = button.closest('.question-item');
        var optionList = questionForOption ? questionForOption.querySelector('[data-option-list]') : null;
        if (optionList) optionList.appendChild(cloneTemplate(optionTemplate));
        render();
        return;
      }

      if (button.matches('[data-remove-question]')) {
        event.preventDefault();
        if (questionItems(form).length <= 1) {
          notify('At least one question is required.');
          return;
        }
        var toRemove = button.closest('.question-item');
        if (toRemove) toRemove.remove();
        render();
        return;
      }

      if (button.matches('[data-remove-option]')) {
        event.preventDefault();
        var optionListForRemove = button.closest('[data-option-list]');
        if (optionListForRemove && optionListForRemove.querySelectorAll('.option-item').length <= 2) {
          notify('A question needs at least two choices.');
          return;
        }
        var optionToRemove = button.closest('.option-item');
        if (optionToRemove) optionToRemove.remove();
        render();
        return;
      }

      if (button.matches('[data-move-up]') || button.matches('[data-move-down]')) {
        event.preventDefault();
        moveItem(button.closest('.option-item') || button.closest('.question-item'), button.matches('[data-move-up]') ? 'up' : 'down');
        render();
      }
    });

    form.addEventListener('change', function (event) {
      var fileInput = event.target.closest('[data-image-file]');
      if (fileInput) {
        var question = fileInput.closest('.question-item');
        var file = fileInput.files && fileInput.files[0];
        if (question && file) uploadImage(form, question, file, schedule);
        return;
      }
      schedule();
    });

    form.addEventListener('input', schedule);
    form.addEventListener('submit', function () { mcqRenumber(form); });

    render();
  }

  // ════════════════════════════════════════════════════════════════════
  // Coding editor
  // ════════════════════════════════════════════════════════════════════

  function caseItems(question) {
    return Array.from(question.querySelectorAll('[data-test-case-list] > [data-test-case]'));
  }

  function codingRenumber(form) {
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

  function codingReadProblems(form) {
    return questionItems(form).map(function (question) {
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
  }

  function caseRow(label, text) {
    var row = el('div', 'cc-preview-case-row');
    row.appendChild(el('span', 'cc-preview-case-key', label));
    row.appendChild(el('span', 'cc-preview-case-val', text));
    return row;
  }

  function codingRenderPreview(form, preview) {
    renderHeader(form, preview);

    var problems = codingReadProblems(form);
    var count = preview.querySelector('[data-preview-count]');
    if (count) count.textContent = String(problems.length);

    var container = preview.querySelector('[data-preview-problems]');
    if (!container) return;

    var fragment = document.createDocumentFragment();
    if (!problems.length) {
      fragment.appendChild(el('p', 'cc-preview-empty', 'Add a problem to see it here.'));
    }

    problems.forEach(function (problem, index) {
      var card = el('article', 'cc-preview-problem');
      card.appendChild(el('div', 'cc-preview-qnum', 'Question ' + (index + 1) + ' of ' + problems.length));
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

  function initCoding(form) {
    var locked = form.getAttribute('data-locked') === '1';
    var editor = form.querySelector('[data-question-editor]');
    var questionList = editor ? editor.querySelector('[data-question-list]') : null;
    var questionTemplate = editor ? editor.querySelector('template[data-question-template]') : null;
    var caseTemplate = editor ? editor.querySelector('template[data-test-case-template]') : null;
    var preview = document.querySelector('[data-coding-preview]');

    var render = function () {
      codingRenumber(form);
      if (preview) codingRenderPreview(form, preview);
    };
    var schedule = debounce(render, 150);

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

    form.addEventListener('input', schedule);
    form.addEventListener('change', schedule);
    form.addEventListener('submit', function () { codingRenumber(form); });

    render();
  }

  // ════════════════════════════════════════════════════════════════════

  document.addEventListener('DOMContentLoaded', function () {
    var mcqForm = document.querySelector('form[data-mcq-form]');
    if (mcqForm) initMcq(mcqForm);

    var codingForm = document.querySelector('form[data-coding-form]');
    if (codingForm) initCoding(codingForm);
  });
})();
