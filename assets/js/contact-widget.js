(function () {
    'use strict';

    function init() {
        var widget = document.querySelector('.iristick-contact-widget');
        if (!widget) return;

        var toggle = widget.querySelector('.iristick-contact-toggle');
        var panel = widget.querySelector('.iristick-contact-panel');
        var close = widget.querySelector('.iristick-contact-close');
        var officeToggle = widget.querySelector('.iristick-contact-office-toggle');
        var officeBack = widget.querySelector('.iristick-contact-office-back');
        var offices = widget.querySelector('.iristick-contact-offices');

        function showOffices(show) {
            panel.classList.toggle('is-showing-offices', show);
            offices.setAttribute('aria-hidden', show ? 'false' : 'true');
        }

        function setOpen(open) {
            widget.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            panel.setAttribute('aria-hidden', open ? 'false' : 'true');
            toggle.setAttribute('aria-label', open ? 'Đóng cửa sổ liên hệ' : 'Mở cửa sổ liên hệ');
            if (!open) showOffices(false);
        }

        toggle.addEventListener('click', function () {
            setOpen(!widget.classList.contains('is-open'));
        });
        close.addEventListener('click', function () {
            setOpen(false);
            toggle.focus();
        });
        officeToggle.addEventListener('click', function () {
            showOffices(true);
            officeBack.focus();
        });
        officeBack.addEventListener('click', function () {
            showOffices(false);
            officeToggle.focus();
        });
        panel.addEventListener('click', function (event) {
            if (event.target.closest('a')) setOpen(false);
        });
        document.addEventListener('click', function (event) {
            if (!widget.contains(event.target)) setOpen(false);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape' || !widget.classList.contains('is-open')) return;
            if (panel.classList.contains('is-showing-offices')) {
                showOffices(false);
                officeToggle.focus();
            } else {
                setOpen(false);
                toggle.focus();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
