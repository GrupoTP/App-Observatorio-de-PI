/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

function initLucideIcons(root) {
    if (!window.lucide) {
        return;
    }

    window.lucide.createIcons({
        attrs: {
            'stroke-width': 2,
        },
        nameAttr: 'data-lucide',
        root: root || document,
    });
}

function disableBrowserAutofillHints() {
    document.querySelectorAll('form').forEach(function (form) {
        form.setAttribute('autocomplete', 'off');
    });

    document.querySelectorAll('input, textarea, select').forEach(function (field) {
        const type = (field.getAttribute('type') || '').toLowerCase();

        if (type === 'hidden' || type === 'submit' || type === 'button' || type === 'reset' || type === 'file') {
            return;
        }

        field.setAttribute('autocomplete', 'off');
        field.setAttribute('autocorrect', 'off');
        field.setAttribute('autocapitalize', 'off');
        field.setAttribute('spellcheck', 'false');
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        disableBrowserAutofillHints();
        initLucideIcons();
    });
} else {
    disableBrowserAutofillHints();
    initLucideIcons();
}

(function () {
    const toggle = document.getElementById('menuToggle');
    const overlay = document.getElementById('mobileMenuOverlay');
    const panel = document.getElementById('mobileMenuPanel');
    const closeBtn = document.getElementById('menuCloseBtn');
    const iconOpen = document.getElementById('menuIconOpen');
    const iconClose = document.getElementById('menuIconClose');

    if (!toggle || !overlay || !panel) {
        return;
    }

    function openMenu() {
        overlay.hidden = false;
        panel.hidden = false;
        overlay.removeAttribute('aria-hidden');
        panel.removeAttribute('aria-hidden');
        overlay.classList.add('show');
        panel.classList.add('show');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.setAttribute('aria-label', 'Fechar menu');
        iconOpen?.classList.add('d-none');
        iconClose?.classList.remove('d-none');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        overlay.classList.remove('show');
        panel.classList.remove('show');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Abrir menu');
        iconOpen?.classList.remove('d-none');
        iconClose?.classList.add('d-none');
        document.body.style.overflow = '';
        setTimeout(function () {
            overlay.hidden = true;
            panel.hidden = true;
            overlay.setAttribute('aria-hidden', 'true');
            panel.setAttribute('aria-hidden', 'true');
        }, 250);
    }

    overlay.setAttribute('aria-hidden', 'true');
    panel.setAttribute('aria-hidden', 'true');

    toggle.addEventListener('click', function () {
        if (panel.classList.contains('show')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    overlay.addEventListener('click', closeMenu);
    closeBtn?.addEventListener('click', closeMenu);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && panel.classList.contains('show')) {
            closeMenu();
        }
    });
})();

(function () {
    const textarea = document.getElementById('descricao');
    const counter = document.getElementById('descricao-count');

    if (textarea && counter) {
        const updateCount = function () {
            counter.textContent = textarea.value.length + '/500';
        };

        textarea.addEventListener('input', updateCount);
        updateCount();
    }
})();

(function () {
    const form = document.querySelector('[data-submeter-form]');
    if (!form) {
        return;
    }

    const attachmentMessageId = 'submeter-attachment-error';
    let attachmentMessage = document.getElementById(attachmentMessageId);

    if (!attachmentMessage) {
        attachmentMessage = document.createElement('p');
        attachmentMessage.id = attachmentMessageId;
        attachmentMessage.className = 'app-field__helper text-senac-error mb-0 d-none';
        attachmentMessage.setAttribute('role', 'alert');
        attachmentMessage.textContent = 'Anexe ao menos um arquivo ao projeto.';
        form.querySelector('[data-attachments-manager]')?.appendChild(attachmentMessage);
    }

    const hasRequiredAttachment = function () {
        return Array.from(form.querySelectorAll('[data-file-input]')).some(function (input) {
            return input.files && input.files.length > 0;
        });
    };

    form.addEventListener('submit', function (event) {
        attachmentMessage.classList.add('d-none');
        form.querySelectorAll('.app-file-upload--invalid').forEach(function (zone) {
            zone.classList.remove('app-file-upload--invalid');
        });

        if (!form.reportValidity()) {
            event.preventDefault();
            return;
        }

        if (!hasRequiredAttachment()) {
            event.preventDefault();
            const firstZone = form.querySelector('[data-file-upload]');
            firstZone?.classList.add('app-file-upload--invalid');
            attachmentMessage.classList.remove('d-none');
            firstZone?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    form.querySelectorAll('[data-file-input]').forEach(function (input) {
        input.addEventListener('change', function () {
            if (hasRequiredAttachment()) {
                attachmentMessage.classList.add('d-none');
                form.querySelectorAll('.app-file-upload--invalid').forEach(function (zone) {
                    zone.classList.remove('app-file-upload--invalid');
                });
            }
        });
    });
})();

function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    }

    if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    }

    return (bytes / 1024).toFixed(1) + ' KB';
}

function assignFileToInput(input, file) {
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    input.files = dataTransfer.files;
}

function bindFileUploadZone(zone) {
    if (zone.dataset.fileUploadBound === 'true') {
        return;
    }

    zone.dataset.fileUploadBound = 'true';

    const input = zone.querySelector('[data-file-input]');
    const label = zone.querySelector('.app-file-upload__label');
    const placeholder = zone.querySelector('[data-file-placeholder]');
    const selected = zone.querySelector('[data-file-selected]');
    const nameEl = zone.querySelector('[data-file-name]');
    const sizeEl = zone.querySelector('[data-file-size]');

    if (!input || !placeholder || !selected) {
        return;
    }

    const updateView = function () {
        const file = input.files && input.files[0];

        if (!file) {
            placeholder.classList.remove('d-none');
            selected.classList.add('d-none');
            zone.classList.remove('app-file-upload--has-file');
            return;
        }

        placeholder.classList.add('d-none');
        selected.classList.remove('d-none');
        zone.classList.add('app-file-upload--has-file');

        if (nameEl) {
            nameEl.textContent = file.name;
        }

        if (sizeEl) {
            sizeEl.textContent = formatFileSize(file.size);
        }
    };

    const setDragState = function (active) {
        zone.classList.toggle('app-file-upload--dragover', active);
    };

    label?.addEventListener('click', function (event) {
        event.preventDefault();
        input.click();
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (eventName) {
        zone.addEventListener(eventName, function (event) {
            event.preventDefault();
            event.stopPropagation();
        });
    });

    zone.addEventListener('dragenter', function () {
        setDragState(true);
    });

    zone.addEventListener('dragover', function () {
        setDragState(true);
    });

    zone.addEventListener('dragleave', function (event) {
        if (!zone.contains(event.relatedTarget)) {
            setDragState(false);
        }
    });

    zone.addEventListener('drop', function (event) {
        setDragState(false);

        const file = event.dataTransfer?.files?.[0];
        if (!file) {
            return;
        }

        assignFileToInput(input, file);
        updateView();
    });

    input.addEventListener('change', updateView);
}

(function () {
    document.querySelectorAll('[data-file-upload]').forEach(bindFileUploadZone);
})();

(function () {
    document.querySelectorAll('[data-attachments-manager]').forEach(function (manager) {
        const list = manager.querySelector('[data-attachment-list]');
        const addBtn = manager.querySelector('[data-add-attachment]');

        if (!list) {
            return;
        }

        const syncRemoveButtons = function () {
            const rows = list.querySelectorAll('[data-attachment-row]');
            rows.forEach(function (row, index) {
                const removeBtn = row.querySelector('[data-remove-attachment]');
                if (!removeBtn) {
                    return;
                }

                if (rows.length === 1) {
                    removeBtn.classList.add('d-none');
                } else {
                    removeBtn.classList.remove('d-none');
                }

                if (index === 0) {
                    row.querySelector('[data-file-input]')?.setAttribute('required', '');
                } else {
                    row.querySelector('[data-file-input]')?.removeAttribute('required');
                }
            });
        };

        addBtn?.addEventListener('click', function () {
            const template = list.querySelector('[data-attachment-row]');
            if (!template) {
                return;
            }

            const clone = template.cloneNode(true);
            const suffix = Date.now().toString(36);

            clone.querySelectorAll('[data-file-upload]').forEach(function (zone) {
                delete zone.dataset.fileUploadBound;
            });

            clone.querySelectorAll('[data-file-input]').forEach(function (input) {
                input.value = '';
                input.removeAttribute('required');
            });

            clone.querySelectorAll('[name="anexo_descricao[]"]').forEach(function (input) {
                input.value = '';
            });

            clone.querySelectorAll('[data-file-placeholder]').forEach(function (el) {
                el.classList.remove('d-none');
            });

            clone.querySelectorAll('[data-file-selected]').forEach(function (el) {
                el.classList.add('d-none');
            });

            clone.querySelectorAll('.app-file-upload').forEach(function (zone) {
                zone.classList.remove('app-file-upload--has-file');
            });

            clone.querySelectorAll('label[for]').forEach(function (label) {
                const oldFor = label.getAttribute('for');
                if (!oldFor) {
                    return;
                }

                const newFor = oldFor + '-' + suffix;
                label.setAttribute('for', newFor);
                const field = clone.querySelector('#' + CSS.escape(oldFor));
                if (field) {
                    field.id = newFor;
                }
            });

            list.appendChild(clone);
            clone.querySelectorAll('[data-file-upload]').forEach(bindFileUploadZone);
            syncRemoveButtons();
        });

        list.addEventListener('click', function (event) {
            const target = event.target.closest('[data-remove-attachment]');
            if (!target) {
                return;
            }

            const row = target.closest('[data-attachment-row]');
            if (!row || list.querySelectorAll('[data-attachment-row]').length <= 1) {
                return;
            }

            row.remove();
            syncRemoveButtons();
        });

        syncRemoveButtons();
    });
})();

function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    btn.querySelector('i')?.classList.toggle('bi-eye');
    btn.querySelector('i')?.classList.toggle('bi-eye-slash');
}
