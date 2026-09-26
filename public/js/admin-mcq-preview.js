(function () {
  'use strict';

  // Picture upload and live preview for the admin MCQ challenge form.
  // Loaded only from admin/challenges/_form.blade.php, after admin-content-manager.js,
  // which owns question/option add, remove, move and name renumbering.

  var IMAGE_PATH_PATTERN = /^\/uploads\/challenges\/(?!.*\.\.)[A-Za-z0-9_\-.\/]+\.(png|jpe?g|gif|webp)$/i;

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

  function questionItems(form) {
    return Array.from(form.querySelectorAll('[data-question-list] > .question-item'));
  }

  // The hidden image_path inputs are renumbered by admin-content-manager.js.
  // This is a safety net so a cloned question always submits under its index.
  function renumberImageFields(form) {
    questionItems(form).forEach(function (question, index) {
      var hidden = question.querySelector('[data-field="image_path"]');
      if (hidden) hidden.name = 'questions[' + index + '][image_path]';
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

  function uploadImage(form, question, file) {
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
      schedulePreview();
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

  function readForm(form) {
    var value = function (selector) {
      var node = form.querySelector(selector);
      return node ? String(node.value || '') : '';
    };

    var questions = questionItems(form).map(function (question) {
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

    return {
      title: value('[name="title"]').trim(),
      description: value('[name="description"]').trim(),
      xp: parseInt(value('[name="base_xp"]'), 10),
      timeLimit: parseInt(value('[name="time_limit_seconds"]'), 10),
      questions: questions
    };
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

    var container = preview.querySelector('[data-preview-questions]');
    if (!container) return;

    var fragment = document.createDocumentFragment();

    if (!data.questions.length) {
      fragment.appendChild(el('p', 'mcq-preview-empty', 'Add a question to see it here.'));
    }

    data.questions.forEach(function (question, index) {
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

  var schedulePreview = function () {};

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('form[data-mcq-form]');
    if (!form) return;

    var preview = document.querySelector('[data-mcq-preview]');
    var render = function () {
      renumberImageFields(form);
      if (preview) renderPreview(form, preview);
    };
    schedulePreview = debounce(render, 150);

    form.addEventListener('change', function (event) {
      var fileInput = event.target.closest('[data-image-file]');
      if (fileInput) {
        var question = fileInput.closest('.question-item');
        var file = fileInput.files && fileInput.files[0];
        if (question && file) uploadImage(form, question, file);
        return;
      }
      schedulePreview();
    });

    form.addEventListener('input', schedulePreview);

    form.addEventListener('click', function (event) {
      var removeButton = event.target.closest('[data-remove-image]');
      if (removeButton) {
        event.preventDefault();
        var question = removeButton.closest('.question-item');
        if (question) setQuestionImage(question, '');
        schedulePreview();
        return;
      }

      // Add, remove and move buttons are handled by admin-content-manager.js;
      // re-render once its handler has changed the DOM.
      if (event.target.closest('button')) schedulePreview();
    });

    form.addEventListener('submit', function () {
      renumberImageFields(form);
    });

    render();
  });
})();
