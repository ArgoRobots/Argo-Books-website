(function () {
    'use strict';

    var EYE_OPEN = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>';
    var EYE_CLOSED = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>';

    function attach(input) {
        if (input.dataset.passwordToggle === 'attached') return;
        if (input.dataset.passwordToggle === 'off') return;

        var wrapper = input.parentElement;
        if (!wrapper || !wrapper.classList.contains('password-field-wrapper')) {
            wrapper = document.createElement('div');
            wrapper.className = 'password-field-wrapper';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);
        }

        var existing = wrapper.querySelector('.toggle-password');
        if (existing) existing.remove();

        var toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'toggle-password';
        toggle.setAttribute('aria-label', 'Show password');
        toggle.innerHTML =
            '<span class="icon-show" aria-hidden="true">' + EYE_OPEN + '</span>' +
            '<span class="icon-hide" aria-hidden="true">' + EYE_CLOSED + '</span>';

        toggle.addEventListener('click', function () {
            var revealing = input.getAttribute('type') === 'password';
            input.setAttribute('type', revealing ? 'text' : 'password');
            toggle.classList.toggle('active', revealing);
            toggle.setAttribute('aria-label', revealing ? 'Hide password' : 'Show password');
        });

        wrapper.appendChild(toggle);
        input.dataset.passwordToggle = 'attached';
    }

    function init() {
        var inputs = document.querySelectorAll('input[type="password"]');
        for (var i = 0; i < inputs.length; i++) attach(inputs[i]);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
