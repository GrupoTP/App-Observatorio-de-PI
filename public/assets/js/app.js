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

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        initLucideIcons();
    });
} else {
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
    document.querySelectorAll('[data-file-upload]').forEach(function (zone) {
        const input = zone.querySelector('[data-file-input]');
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
                sizeEl.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            }
        };

        input.addEventListener('change', updateView);
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
