(function () {
  'use strict';

  function directChild(element, selector) {
    return Array.from(element.children).find(function (child) {
      return child.matches(selector);
    }) || null;
  }

  function setName(element, name) {
    if (element) element.name = name;
  }

  function updateQuestionLabels(editor) {
    Array.from(editor.querySelectorAll(':scope > [data-question-list] > .question-item')).forEach(function (question, questionIndex) {
      var number = question.querySelector('[data-question-number]');
      if (number) number.textContent = String(questionIndex + 1);

      setName(question.querySelector('[data-field="question_type"]'), 'questions[' + questionIndex + '][question_type]');
      setName(question.querySelector('[data-field="question_text"]'), 'questions[' + questionIndex + '][question_text]');
      setName(question.querySelector('[data-field="points"]'), 'questions[' + questionIndex + '][points]');
      setName(question.querySelector('[data-field="explanation"]'), 'questions[' + questionIndex + '][explanation]');
      setName(question.querySelector('[data-field="ilo_ids"]'), 'questions[' + questionIndex + '][ilo_ids][]');

      var options = Array.from(question.querySelectorAll('[data-option-list] > .option-item'));
      options.forEach(function (option, optionIndex) {
        var optionNumber = option.querySelector('[data-option-number]');
        if (optionNumber) optionNumber.textContent = String(optionIndex + 1);

        setName(option.querySelector('[data-field="option_text"]'), 'questions[' + questionIndex + '][options][' + optionIndex + '][option_text]');
        var radio = option.querySelector('[data-field="correct_option"]');
        if (radio) {
          radio.name = 'questions[' + questionIndex + '][correct_option]';
          radio.value = String(optionIndex);
        }
      });

      if (options.length && !options.some(function (option) {
        var radio = option.querySelector('[data-field="correct_option"]');
        return radio && radio.checked;
      })) {
        var firstRadio = options[0].querySelector('[data-field="correct_option"]');
        if (firstRadio) firstRadio.checked = true;
      }

      Array.from(question.querySelectorAll('[data-answer-list] > .answer-item')).forEach(function (answer, answerIndex) {
        var answerNumber = answer.querySelector('[data-answer-number]');
        if (answerNumber) answerNumber.textContent = String(answerIndex + 1);

        setName(answer.querySelector('[data-field="answer_text"]'), 'questions[' + questionIndex + '][blank_answers][' + answerIndex + '][answer_text]');
        setName(answer.querySelector('[data-field="is_case_sensitive"]'), 'questions[' + questionIndex + '][blank_answers][' + answerIndex + '][is_case_sensitive]');
      });

      toggleQuestionType(question);
    });
  }

  function toggleQuestionType(question) {
    var typeSelect = question.querySelector('[data-field="question_type"]');
    if (!typeSelect) return;

    var type = typeSelect.value;
    var optionsBlock = question.querySelector('[data-options-block]');
    var answersBlock = question.querySelector('[data-answers-block]');

    if (optionsBlock) optionsBlock.hidden = type !== 'mcq';
    if (answersBlock) answersBlock.hidden = type !== 'fill_blank';
  }

  function cloneTemplate(template) {
    return template.content.firstElementChild.cloneNode(true);
  }

  function moveItem(button, direction) {
    var item = button.closest('.question-item, .option-item, .answer-item');
    if (!item || !item.parentElement) return;

    if (direction === 'up' && item.previousElementSibling) {
      item.parentElement.insertBefore(item, item.previousElementSibling);
    }

    if (direction === 'down' && item.nextElementSibling) {
      item.parentElement.insertBefore(item.nextElementSibling, item);
    }
  }

  function showValidationNotification(message) {
    if (window.DataSenseiNotifications && typeof window.DataSenseiNotifications.show === 'function') {
      window.DataSenseiNotifications.show(message, { type: 'warning', duration: 0 });
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-question-editor]').forEach(function (editor) {
      var questionList = editor.querySelector('[data-question-list]');
      var questionTemplate = editor.querySelector('template[data-question-template]');
      var optionTemplate = editor.querySelector('template[data-option-template]');
      var answerTemplate = editor.querySelector('template[data-answer-template]');

      editor.addEventListener('click', function (event) {
        var button = event.target.closest('button');
        if (!button) return;

        if (button.matches('[data-add-question]')) {
          event.preventDefault();
          var question = cloneTemplate(questionTemplate);
          var typeSelect = question.querySelector('[data-field="question_type"]');
          if (typeSelect) typeSelect.value = button.getAttribute('data-add-question') || 'mcq';
          questionList.appendChild(question);
          updateQuestionLabels(editor);
          question.querySelector('[data-field="question_text"]')?.focus();
          return;
        }

        if (button.matches('[data-add-option]')) {
          event.preventDefault();
          var questionForOption = button.closest('.question-item');
          var optionList = questionForOption.querySelector('[data-option-list]');
          optionList.appendChild(cloneTemplate(optionTemplate));
          updateQuestionLabels(editor);
          return;
        }

        if (button.matches('[data-add-answer]')) {
          event.preventDefault();
          var questionForAnswer = button.closest('.question-item');
          var answerList = questionForAnswer.querySelector('[data-answer-list]');
          answerList.appendChild(cloneTemplate(answerTemplate));
          updateQuestionLabels(editor);
          return;
        }

        if (button.matches('[data-remove-question]')) {
          event.preventDefault();
          if (questionList.querySelectorAll('.question-item').length <= 1) {
            showValidationNotification('At least one question is required.');
            return;
          }
          button.closest('.question-item').remove();
          updateQuestionLabels(editor);
          return;
        }

        if (button.matches('[data-remove-option]')) {
          event.preventDefault();
          var optionListForRemove = button.closest('[data-option-list]');
          if (optionListForRemove.querySelectorAll('.option-item').length <= 2) {
            showValidationNotification('An MCQ question requires at least two choices.');
            return;
          }
          button.closest('.option-item').remove();
          updateQuestionLabels(editor);
          return;
        }

        if (button.matches('[data-remove-answer]')) {
          event.preventDefault();
          var answerListForRemove = button.closest('[data-answer-list]');
          if (answerListForRemove.querySelectorAll('.answer-item').length <= 1) {
            showValidationNotification('A fill-in-the-blank question requires at least one accepted answer.');
            return;
          }
          button.closest('.answer-item').remove();
          updateQuestionLabels(editor);
          return;
        }

        if (button.matches('[data-move-up]')) {
          event.preventDefault();
          moveItem(button, 'up');
          updateQuestionLabels(editor);
          return;
        }

        if (button.matches('[data-move-down]')) {
          event.preventDefault();
          moveItem(button, 'down');
          updateQuestionLabels(editor);
        }
      });

      editor.addEventListener('change', function (event) {
        if (event.target.matches('[data-field="question_type"]')) {
          toggleQuestionType(event.target.closest('.question-item'));
        }
      });

      var form = editor.closest('form');
      if (form) {
        form.addEventListener('submit', function () {
          updateQuestionLabels(editor);
        });
      }

      updateQuestionLabels(editor);
    });
  });
})();
