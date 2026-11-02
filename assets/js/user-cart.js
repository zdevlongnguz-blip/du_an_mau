(function () {
  'use strict';

  function clampValue(input, rawValue) {
    var min = parseInt(input.min, 10) || 1;
    var max = parseInt(input.max, 10) || 9999;
    var value = parseInt(rawValue, 10);
    if (isNaN(value)) value = min;
    if (value < min) value = min;
    if (value > max) value = max;
    return value;
  }

  function bindStepper(box) {
    var input = box.querySelector('input[name="quantity"]');
    if (!input) return;

    var buttons = box.querySelectorAll('button[data-qty-minus], button[data-qty-plus]');
    buttons.forEach(function (button) {
      button.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var form = button.closest('form');
        if (!form) return;

        var current = clampValue(input, input.value);
        var nextValue = current;

        if (button.hasAttribute('data-qty-minus')) {
          nextValue = Math.max(parseInt(input.min, 10) || 1, current - 1);
        }

        if (button.hasAttribute('data-qty-plus')) {
          nextValue = Math.min(parseInt(input.max, 10) || 9999, current + 1);
        }

        input.value = String(nextValue);
        form.submit();
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.hs-qty').forEach(function (box) {
      bindStepper(box);
    });
  });
})();
