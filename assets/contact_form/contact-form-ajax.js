(function () {
    'use strict';

    function findTargetWrapper(htmlText, key) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(htmlText, 'text/html');
        var safeKey = String(key || '').replace(/"/g, '\\"');
        return doc.querySelector('[data-cb-contact-form-wrapper="1"][data-cb-contact-form-key="' + safeKey + '"]');
    }

    function focusLiveMessage(wrapper) {
        var live = wrapper.querySelector('[data-cb-form-live="1"]');
        if (!live) {
            return;
        }

        var alert = wrapper.querySelector('.uk-alert-success p, .uk-alert-danger p');
        if (!alert) {
            return;
        }

        live.classList.remove('uk-hidden');
        live.textContent = alert.textContent || '';
    }

    function setSubmitState(form, disabled) {
        var submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        submitButtons.forEach(function (button) {
            button.disabled = disabled;
            if (disabled) {
                button.setAttribute('aria-busy', 'true');
            } else {
                button.removeAttribute('aria-busy');
            }
        });
    }

    document.addEventListener('submit', function (event) {
        var form = event.target.closest('form[data-cb-contact-form="1"]');
        if (!form) {
            return;
        }

        var wrapper = form.closest('[data-cb-contact-form-wrapper="1"]');
        if (!wrapper || wrapper.getAttribute('data-cb-ajax-enhancement') !== '1') {
            return;
        }

        if (!window.fetch || !window.FormData || !window.DOMParser) {
            return;
        }

        event.preventDefault();

        var key = wrapper.getAttribute('data-cb-contact-form-key') || '';
        var action = form.getAttribute('action') || window.location.href;
        var formData = new FormData(form);

        setSubmitState(form, true);

        fetch(action, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Request failed');
                }
                return response.text();
            })
            .then(function (htmlText) {
                var newWrapper = findTargetWrapper(htmlText, key);
                if (!newWrapper) {
                    throw new Error('Target form not found in response');
                }

                wrapper.replaceWith(newWrapper);
                focusLiveMessage(newWrapper);

                var firstInvalid = newWrapper.querySelector('.uk-form-danger, [aria-invalid="true"]');
                if (firstInvalid && typeof firstInvalid.focus === 'function') {
                    firstInvalid.focus();
                    return;
                }

                var headline = newWrapper.querySelector('.uk-alert-success, .uk-alert-danger');
                if (headline && typeof headline.focus === 'function') {
                    headline.setAttribute('tabindex', '-1');
                    headline.focus();
                }
            })
            .catch(function () {
                form.submit();
            })
            .finally(function () {
                setSubmitState(form, false);
            });
    });
})();
