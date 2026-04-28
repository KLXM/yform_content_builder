(function () {
    'use strict';

    function nearestBlock(element) {
        if (!element || typeof element.closest !== 'function') {
            return null;
        }

        return element.closest('.uk-width-1-1') || element.closest('.uk-margin-top') || element.parentElement;
    }

    function validateStep(step) {
        var inputs = step.querySelectorAll('input, select, textarea');
        for (var i = 0; i < inputs.length; i++) {
            var field = inputs[i];
            if (typeof field.checkValidity === 'function' && !field.checkValidity()) {
                if (typeof field.reportValidity === 'function') {
                    field.reportValidity();
                }
                if (typeof field.focus === 'function') {
                    field.focus();
                }
                return false;
            }
        }
        return true;
    }

    function initMultistepForms(root) {
        var forms = (root || document).querySelectorAll('form[data-cb-contact-form="1"][data-cb-multistep="1"]');
        forms.forEach(function (form) {
            if (form.getAttribute('data-cb-multistep-init') === '1') {
                return;
            }

            var steps = Array.prototype.slice.call(form.querySelectorAll('fieldset.uk-fieldset'));
            if (steps.length < 2) {
                return;
            }

            form.setAttribute('data-cb-multistep-init', '1');
            var prevLabel = form.getAttribute('data-cb-step-prev-label') || 'Zurück';
            var nextLabel = form.getAttribute('data-cb-step-next-label') || 'Weiter';
            var submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            var currentStep = 0;

            function toggleSubmitVisibility(show) {
                submitButtons.forEach(function (button) {
                    var block = nearestBlock(button);
                    if (!block) {
                        return;
                    }
                    block.hidden = !show;
                });
            }

            function showStep(stepIndex) {
                steps.forEach(function (step, index) {
                    var isActive = index === stepIndex;
                    step.hidden = !isActive;
                    step.setAttribute('aria-hidden', isActive ? 'false' : 'true');

                    var nav = step.nextElementSibling;
                    if (nav && nav.classList.contains('cb-contact-step-nav')) {
                        nav.hidden = !isActive;
                    }
                });

                currentStep = stepIndex;
                toggleSubmitVisibility(stepIndex === steps.length - 1);
            }

            steps.forEach(function (step, index) {
                var nav = document.createElement('div');
                nav.className = 'cb-contact-step-nav uk-margin-top uk-flex uk-flex-between';

                var prevButton = document.createElement('button');
                prevButton.type = 'button';
                prevButton.className = 'uk-button uk-button-default';
                prevButton.textContent = prevLabel;
                prevButton.hidden = index === 0;
                prevButton.addEventListener('click', function () {
                    if (index > 0) {
                        showStep(index - 1);
                    }
                });

                var nextButton = document.createElement('button');
                nextButton.type = 'button';
                nextButton.className = 'uk-button uk-button-primary';
                nextButton.textContent = nextLabel;
                nextButton.hidden = index === steps.length - 1;
                nextButton.addEventListener('click', function () {
                    if (!validateStep(step)) {
                        return;
                    }
                    if (index < steps.length - 1) {
                        showStep(index + 1);
                    }
                });

                nav.appendChild(prevButton);
                nav.appendChild(nextButton);
                step.insertAdjacentElement('afterend', nav);
            });

            showStep(0);
        });
    }

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
                initMultistepForms(newWrapper);
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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initMultistepForms(document);
        });
    } else {
        initMultistepForms(document);
    }
})();
