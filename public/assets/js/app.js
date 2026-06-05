/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

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
        overlay.classList.add('show');
        panel.classList.add('show');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.setAttribute('aria-label', 'Fechar menu');
        iconOpen?.classList.add('d-none');
        iconClose?.classList.remove('d-none');
    }

    function closeMenu() {
        overlay.classList.remove('show');
        panel.classList.remove('show');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Abrir menu');
        iconOpen?.classList.remove('d-none');
        iconClose?.classList.add('d-none');
        setTimeout(function () {
            overlay.hidden = true;
            panel.hidden = true;
        }, 250);
    }

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

function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    btn.querySelector('i')?.classList.toggle('bi-eye');
    btn.querySelector('i')?.classList.toggle('bi-eye-slash');
}
