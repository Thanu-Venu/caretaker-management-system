/**
 * SmartCare — reusable admin form validation (inline errors, real-time + submit).
 * Attach to any POST form: data-admin-validate
 */
(function () {
    'use strict';

    var ERROR_CLASS = 'is-invalid';
    var ERROR_TEXT_CLASS = 'field-error';
    var PHONE_LEN = 10;

    function qs(form, sel) {
        return form.querySelector(sel);
    }

    function qsa(form, sel) {
        return Array.prototype.slice.call(form.querySelectorAll(sel));
    }

    function trim(v) {
        return (v == null ? '' : String(v)).trim();
    }

    function getErrorHost(input) {
        var field = input.closest('.field');
        if (field) return field;
        var lab = input.closest('label');
        if (lab && lab.parentElement) return lab.parentElement;
        return input.parentElement || document.body;
    }

    function passwordErrorAnchor(input) {
        var wrap = input.closest('.password-input-wrap');
        return wrap || input;
    }

    function getOrCreateErrorEl(input) {
        var anchor = passwordErrorAnchor(input);
        var next = anchor.nextElementSibling;
        if (next && next.classList && next.classList.contains(ERROR_TEXT_CLASS)) {
            return next;
        }
        var el = document.createElement('p');
        el.className = ERROR_TEXT_CLASS;
        el.setAttribute('role', 'alert');
        anchor.insertAdjacentElement('afterend', el);
        return el;
    }

    function clearFieldError(input) {
        input.classList.remove(ERROR_CLASS);
        var anchor = passwordErrorAnchor(input);
        var n = anchor.nextElementSibling;
        if (n && n.classList && n.classList.contains(ERROR_TEXT_CLASS)) {
            n.textContent = '';
            n.style.display = 'none';
        }
    }

    function setFieldError(input, message) {
        input.classList.add(ERROR_CLASS);
        var el = getOrCreateErrorEl(input);
        el.textContent = message || '';
        el.style.display = message ? 'block' : 'none';
    }

    function isOptionalPhone(input) {
        return input.name === 'phone' && !input.required;
    }

    var Validators = {
        required: function (input) {
            if (input.type === 'file') {
                if (!input.required) return { ok: true };
                return input.files && input.files.length
                    ? { ok: true }
                    : { ok: false, message: 'This field is required.' };
            }
            if (input.tagName === 'SELECT') {
                var v = input.value;
                if (!trim(v) || v === '') {
                    return { ok: false, message: 'Please select an option.' };
                }
                return { ok: true };
            }
            if (!trim(input.value)) {
                return { ok: false, message: 'This field is required.' };
            }
            return { ok: true };
        },

        email: function (input) {
            var v = trim(input.value);
            if (!v) return { ok: true };
            var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(v)
                ? { ok: true }
                : { ok: false, message: 'Enter a valid email address.' };
        },

        phone10: function (input) {
            var v = trim(input.value).replace(/\D/g, '');
            if (isOptionalPhone(input)) {
                if (!trim(input.value)) return { ok: true };
                return v.length === PHONE_LEN
                    ? { ok: true }
                    : { ok: false, message: 'Phone number must be 10 digits.' };
            }
            if (v.length !== PHONE_LEN) {
                return { ok: false, message: 'Phone number must be 10 digits.' };
            }
            return { ok: true };
        },

        passwordStrong: function (input) {
            var v = input.value;
            if (!input.required && !v) return { ok: true };
            if (v.length < 8) {
                return { ok: false, message: 'Password must be at least 8 characters.' };
            }
            if (!/[A-Z]/.test(v)) {
                return { ok: false, message: 'Password must include at least one uppercase letter.' };
            }
            if (!/[a-z]/.test(v)) {
                return { ok: false, message: 'Password must include at least one lowercase letter.' };
            }
            if (!/[0-9]/.test(v)) {
                return { ok: false, message: 'Password must include at least one number.' };
            }
            if (!/[^A-Za-z0-9]/.test(v)) {
                return { ok: false, message: 'Password must include at least one special character.' };
            }
            return { ok: true };
        },

        matchNewPassword: function (input) {
            var form = input.form;
            if (!form) return { ok: true };
            var target = qs(form, 'input[name="new_password"]') || qs(form, 'input[name="password"]');
            if (!target) return { ok: true };
            if (input.value !== target.value) {
                return { ok: false, message: 'Passwords do not match.' };
            }
            return { ok: true };
        },

        username: function (input) {
            var v = trim(input.value);
            if (!v) return { ok: true };
            if (v.length < 3) {
                return { ok: false, message: 'Username must be at least 3 characters.' };
            }
            if (v.length > 48) {
                return { ok: false, message: 'Username must be at most 48 characters.' };
            }
            if (!/^[a-zA-Z0-9_.-]+$/.test(v)) {
                return {
                    ok: false,
                    message: 'Username may only contain letters, numbers, dots, underscores, and hyphens.',
                };
            }
            return { ok: true };
        },

        minLength: function (input, min) {
            var v = trim(input.value);
            if (!input.required && !v) return { ok: true };
            if (v.length < min) {
                return { ok: false, message: 'Enter at least ' + min + ' characters.' };
            }
            return { ok: true };
        },

        maxLength: function (input, max) {
            var v = input.value;
            if (v.length > max) {
                return { ok: false, message: 'Must be at most ' + max + ' characters.' };
            }
            return { ok: true };
        },

        imageFileOptional: function (input) {
            if (input.type !== 'file' || !input.files || !input.files.length) return { ok: true };
            var f = input.files[0];
            if (!f.type || f.type.indexOf('image/') !== 0) {
                return { ok: false, message: 'Please choose an image file (JPEG, PNG, GIF, WebP).' };
            }
            if (f.size > 5 * 1024 * 1024) {
                return { ok: false, message: 'Image must be 5 MB or smaller.' };
            }
            return { ok: true };
        },

        imageFileRequired: function (input) {
            if (!input.files || !input.files.length) {
                return { ok: false, message: 'Please select a profile picture.' };
            }
            return Validators.imageFileOptional(input);
        },
    };

    function ruleListForInput(input) {
        var rules = [];
        var form = input.form;
        var name = (input.name || '').toLowerCase();
        var type = (input.type || '').toLowerCase();
        var tag = (input.tagName || '').toUpperCase();
        var isCaretakerForm = form && form.classList.contains('caretaker-form');
        var isAnnouncementForm = form && form.classList.contains('announcement-form');

        if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') {
            if (input.hasAttribute('required')) rules.push('required');
        }

        /* Staff login usernames only (alphanumeric). Profile "Full name" uses name=username elsewhere. */
        if (form.classList.contains('user-form') && name === 'username') {
            rules.push('username');
        }
        if (input.getAttribute('data-validate-username') === '1') {
            rules.push('username');
        }
        if (name === 'username' && !form.classList.contains('user-form')) {
            rules.push('minLength:2');
            rules.push('maxLength:120');
        }
        if (name === 'email' || type === 'email') {
            rules.push('email');
        }
        if (name === 'phone' || type === 'tel' || input.getAttribute('data-validate-phone') === '1') {
            rules.push('phone10');
        }
        if (type === 'password') {
            if (name === 'confirm_password') {
                rules.push('matchNewPassword');
            } else if (name === 'new_password' || name === 'password') {
                rules.push('passwordStrong');
            }
        }

        if (isAnnouncementForm && name === 'title') {
            rules.push('minLength:2');
            rules.push('maxLength:200');
        }
        if (isAnnouncementForm && name === 'message') {
            rules.push('minLength:1');
            rules.push('maxLength:8000');
        }

        if (isCaretakerForm && name === 'name') {
            rules.push('minLength:2');
            rules.push('maxLength:120');
        }
        if (isCaretakerForm && name === 'experience') {
            rules.push('minLength:1');
            rules.push('maxLength:100');
        }
        if (isCaretakerForm && name === 'location') {
            rules.push('minLength:2');
            rules.push('maxLength:120');
        }
        if (isCaretakerForm && name === 'qualifications') {
            rules.push('minLength:3');
            rules.push('maxLength:8000');
        }

        var ln = name.toLowerCase();
        if (ln === 'profile_image' || ln === 'profilefile') {
            if (input.required) rules.push('imageFileRequired');
            else rules.push('imageFileOptional');
        }

        var extra = input.getAttribute('data-admin-rules');
        if (extra) {
            extra.split(/\s+/).forEach(function (r) {
                if (r && rules.indexOf(r) === -1) rules.push(r);
            });
        }

        return rules;
    }

    function runRule(input, rule) {
        var parts = rule.split(':');
        var key = parts[0];
        var arg = parts[1];
        if (key === 'minLength') {
            return Validators.minLength(input, parseInt(arg, 10) || 1);
        }
        if (key === 'maxLength') {
            return Validators.maxLength(input, parseInt(arg, 10) || 99999);
        }
        var fn = Validators[key];
        if (!fn) return { ok: true };
        return fn(input);
    }

    function validateField(input) {
        if (input.disabled || input.readOnly || input.type === 'hidden' || input.name === '') {
            if (input.readOnly) clearFieldError(input);
            return true;
        }
        if (input.type === 'submit' || input.type === 'button') return true;

        var rules = ruleListForInput(input);
        if (!rules.length) return true;

        for (var i = 0; i < rules.length; i++) {
            var res = runRule(input, rules[i]);
            if (!res.ok) {
                setFieldError(input, res.message);
                return false;
            }
        }
        clearFieldError(input);
        return true;
    }

    function validateForm(form) {
        var fields = qsa(
            form,
            'input:not([type="hidden"]):not([type="submit"]):not([type="button"]), select, textarea'
        );
        var ok = true;
        fields.forEach(function (el) {
            if (!validateField(el)) ok = false;
        });
        return ok;
    }

    function focusFirstInvalid(form) {
        var el = qs(form, '.' + ERROR_CLASS);
        if (el && typeof el.focus === 'function') {
            el.focus();
        }
    }

    function bindPhoneSanitize(input) {
        if (!input || (input.name !== 'phone' && input.getAttribute('data-validate-phone') !== '1')) return;

        input.setAttribute('inputmode', 'numeric');
        input.setAttribute('pattern', '[0-9]*');
        input.setAttribute('maxlength', String(PHONE_LEN));
        input.setAttribute('autocomplete', 'tel');

        input.addEventListener('keydown', function (e) {
            var allowed =
                e.key === 'Backspace' ||
                e.key === 'Delete' ||
                e.key === 'Tab' ||
                e.key === 'Escape' ||
                e.key === 'Enter' ||
                e.key === 'ArrowLeft' ||
                e.key === 'ArrowRight' ||
                e.key === 'Home' ||
                e.key === 'End' ||
                (e.ctrlKey && (e.key === 'a' || e.key === 'c' || e.key === 'v' || e.key === 'x'));
            if (allowed) return;
            if (e.key && e.key.length === 1 && !/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        input.addEventListener('input', function () {
            var digits = this.value.replace(/\D/g, '').slice(0, PHONE_LEN);
            if (this.value !== digits) this.value = digits;
        });

        input.addEventListener('paste', function (e) {
            e.preventDefault();
            var text = (e.clipboardData || window.clipboardData).getData('text') || '';
            var digits = text.replace(/\D/g, '').slice(0, PHONE_LEN);
            this.value = digits;
            validateField(this);
        });
    }

    function bindForm(form) {
        if (form.getAttribute('data-admin-validate-bound') === '1') return;
        form.setAttribute('data-admin-validate-bound', '1');

        var fields = qsa(
            form,
            'input:not([type="hidden"]):not([type="submit"]):not([type="button"]), select, textarea'
        );

        fields.forEach(function (el) {
            bindPhoneSanitize(el);
            var ev = el.tagName === 'SELECT' ? 'change' : 'input';
            el.addEventListener(ev, function () {
                if (el.classList.contains(ERROR_CLASS)) validateField(el);
            });
            el.addEventListener('blur', function () {
                validateField(el);
            });
        });

        form.addEventListener(
            'submit',
            function (e) {
                if (form.getAttribute('method') && String(form.getAttribute('method')).toLowerCase() === 'get') {
                    return;
                }
                if (!validateForm(form)) {
                    e.preventDefault();
                    e.stopPropagation();
                    focusFirstInvalid(form);
                }
            },
            true
        );
    }

    function init(root) {
        var scope = root || document;
        qsa(scope, 'form[data-admin-validate]').forEach(bindForm);
    }

    window.SmartCareAdminValidation = {
        init: init,
        validateForm: validateForm,
        validateField: validateField,
        clearFieldError: clearFieldError,
        setFieldError: setFieldError,
    };

    document.addEventListener('DOMContentLoaded', function () {
        init(document);
    });
})();
