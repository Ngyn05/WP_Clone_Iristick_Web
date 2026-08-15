(function () {
    'use strict';

    var navStates = ['active_products', 'active_resources', 'active_industries'];

    function removeStates(elements) {
        elements.forEach(function (element) {
            navStates.forEach(function (state) { element.classList.remove(state); });
        });
    }

    function stateFromText(text) {
        var value = (text || '').trim().toLowerCase();
        if (value.indexOf('product') !== -1 || value.indexOf('sản phẩm') !== -1) {
            return 'active_products';
        }
        if (value.indexOf('resource') !== -1 || value.indexOf('support') !== -1 ||
            value.indexOf('tài nguyên') !== -1 || value.indexOf('hỗ trợ') !== -1) {
            return 'active_resources';
        }
        if (value.indexOf('industr') !== -1 || value.indexOf('ngành nghề') !== -1) {
            return 'active_industries';
        }
        return '';
    }

    function dropdownState(dropdown) {
        var headings = Array.from(dropdown.querySelectorAll('h4')).map(function (heading) {
            return heading.textContent;
        }).join(' ');
        return stateFromText(headings);
    }

    function setExpanded(trigger, expanded, state) {
        trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        var icon = trigger.querySelector('.material-symbols-outlined');
        if (icon && state) {
            removeStates([icon]);
            if (expanded) { icon.classList.add(state); }
        }
    }

    function enhanceNavigation(wrapper) {
        var laptop = wrapper.querySelector('nav.laptop');
        var subnav = laptop ? laptop.querySelector('.subnav') : null;
        var desktopButtons = laptop ? Array.from(laptop.querySelectorAll('.nav-pages > button.nav-cat')) : [];
        var dropdowns = subnav ? Array.from(subnav.querySelectorAll(':scope > .dropdown-content')) : [];

        function closeDesktop() {
            removeStates(dropdowns);
            desktopButtons.forEach(function (button) { setExpanded(button, false, stateFromText(button.textContent)); });
            if (subnav) { subnav.classList.remove('active'); }
            if (laptop) { laptop.classList.remove('active'); }
        }

        desktopButtons.forEach(function (button) {
            var state = stateFromText(button.textContent);
            var target = dropdowns.find(function (dropdown) { return dropdownState(dropdown) === state; });
            if (!state || !target) { return; }
            button.setAttribute('aria-haspopup', 'true');
            setExpanded(button, false, state);
            button.addEventListener('click', function (event) {
                event.stopPropagation();
                var open = button.getAttribute('aria-expanded') === 'true';
                closeDesktop();
                if (!open) {
                    target.classList.add(state);
                    subnav.classList.add('active');
                    laptop.classList.add('active');
                    setExpanded(button, true, state);
                }
            });
        });

        var mobileNav = wrapper.querySelector('nav.mobile');
        var menuButton = wrapper.querySelector('.mobile-menu-button');
        var mobileMenu = wrapper.querySelector('.mobile-menu');

        function closeMobile() {
            if (!menuButton || !mobileMenu) { return; }
            mobileMenu.classList.remove('active');
            if (mobileNav) { mobileNav.classList.remove('active'); }
            document.documentElement.classList.remove('iristick-menu-open');
            menuButton.setAttribute('aria-expanded', 'false');
            menuButton.setAttribute('aria-label', 'Mở menu');
            var menuIcon = menuButton.querySelector('.material-symbols-outlined');
            if (menuIcon) { menuIcon.textContent = 'menu'; }
        }

        if (menuButton && mobileMenu) {
            menuButton.setAttribute('aria-controls', 'iristick-mobile-menu');
            mobileMenu.id = 'iristick-mobile-menu';
            closeMobile();
            menuButton.addEventListener('click', function () {
                var open = !mobileMenu.classList.contains('active');
                closeMobile();
                if (open) {
                    mobileMenu.classList.add('active');
                    if (mobileNav) { mobileNav.classList.add('active'); }
                    document.documentElement.classList.add('iristick-menu-open');
                    menuButton.setAttribute('aria-expanded', 'true');
                    menuButton.setAttribute('aria-label', 'Đóng menu');
                    var icon = menuButton.querySelector('.material-symbols-outlined');
                    if (icon) { icon.textContent = 'close'; }
                }
            });

            Array.from(mobileMenu.querySelectorAll('.menu-topic')).forEach(function (topic) {
                var trigger = topic.querySelector(':scope > .nav-cat');
                var panel = topic.querySelector(':scope > .nav-cat-subnav');
                var state = trigger ? stateFromText(trigger.textContent) : '';
                if (!trigger || !panel || !state) { return; }
                trigger.setAttribute('role', 'button');
                trigger.setAttribute('tabindex', '0');
                setExpanded(trigger, false, state);
                function toggleTopic() {
                    var open = trigger.getAttribute('aria-expanded') === 'true';
                    removeStates(Array.from(mobileMenu.querySelectorAll('.nav-cat-subnav, .menu-topic > .nav-cat, .menu-topic > .nav-cat .material-symbols-outlined')));
                    Array.from(mobileMenu.querySelectorAll('.menu-topic > .nav-cat')).forEach(function (item) {
                        item.setAttribute('aria-expanded', 'false');
                    });
                    if (!open) {
                        panel.classList.add(state);
                        trigger.classList.add(state);
                        setExpanded(trigger, true, state);
                    }
                }
                trigger.addEventListener('click', toggleTopic);
                trigger.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        toggleTopic();
                    }
                });
            });
            mobileMenu.querySelectorAll('a').forEach(function (link) { link.addEventListener('click', closeMobile); });
        }

        document.addEventListener('click', function (event) {
            if (!wrapper.contains(event.target)) { closeDesktop(); closeMobile(); }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') { closeDesktop(); closeMobile(); }
        });
        window.addEventListener('resize', function () {
            if (window.innerWidth > 1440) { closeMobile(); } else { closeDesktop(); }
        });
    }

    function enhanceFaqs() {
        document.querySelectorAll('button.faq').forEach(function (faq, index) {
            var content = faq.querySelector('.content');
            var icons = faq.querySelectorAll('.material-symbols-outlined');
            if (!content) { return; }
            content.id = 'faq-answer-' + index;
            faq.setAttribute('aria-controls', content.id);
            faq.setAttribute('aria-expanded', 'false');
            content.setAttribute('aria-hidden', 'true');
            faq.addEventListener('click', function () {
                var open = faq.classList.toggle('active');
                faq.setAttribute('aria-expanded', open ? 'true' : 'false');
                content.setAttribute('aria-hidden', open ? 'false' : 'true');
                if (icons[0]) { icons[0].classList.toggle('active', !open); }
                if (icons[1]) { icons[1].classList.toggle('active', open); }
            });
        });
    }

    function enhanceNewsSearch() {
        var search = document.querySelector('input.search');
        if (!search) { return; }
        var articles = Array.from(document.querySelectorAll('.articles > .article'));
        search.setAttribute('aria-label', 'Tìm kiếm tin tức');
        search.addEventListener('input', function () {
            var query = search.value.trim().toLocaleLowerCase();
            articles.forEach(function (article) {
                article.hidden = query !== '' && article.textContent.toLocaleLowerCase().indexOf(query) === -1;
            });
        });
    }

    function enhanceProductDetails() {
        document.querySelectorAll('.additional').forEach(function (section, index) {
            var trigger = section.querySelector(':scope > .additional-header');
            var content = section.querySelector(':scope > .additional-content');
            if (!trigger || !content) { return; }

            var icons = trigger.querySelectorAll('.material-symbols-outlined');
            var isOpen = content.classList.contains('active');
            content.id = 'product-detail-' + index;
            trigger.setAttribute('aria-controls', content.id);
            trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            content.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

            if (trigger.tagName !== 'BUTTON') {
                trigger.setAttribute('role', 'button');
                trigger.setAttribute('tabindex', '0');
            } else if (!trigger.getAttribute('type')) {
                trigger.setAttribute('type', 'button');
            }

            function setOpen(open) {
                content.classList.toggle('active', open);
                trigger.classList.toggle('active', open);
                trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                content.setAttribute('aria-hidden', open ? 'false' : 'true');
                if (icons[0]) { icons[0].classList.toggle('active', !open); }
                if (icons[1]) { icons[1].classList.toggle('active', open); }
            }

            setOpen(isOpen);
            trigger.addEventListener('click', function () {
                setOpen(trigger.getAttribute('aria-expanded') !== 'true');
            });
            trigger.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    setOpen(trigger.getAttribute('aria-expanded') !== 'true');
                }
            });
        });
    }

    function enhancePricingCalculator() {
        var calculator = document.querySelector('.pricing-calculator');
        if (!calculator) { calculator = document.querySelector('.pc-inner'); }
        if (!calculator) { return; }

        var pathCards = Array.from(calculator.querySelectorAll('.pc-path-card'));
        var sections = Array.from(calculator.querySelectorAll('.pc-section'));
        if (pathCards.length < 3 || sections.length < 3) { return; }

        var hardwareCards = Array.from(sections[0].querySelectorAll('button.pc-card'));
        var promo = calculator.querySelector('.pc-promo');
        var state = { path: 'buy', hardware: null, hardwarePrice: 0, quantity: 1, software: [] };
        var softwareProducts = [
            { name: 'Iristick.Assist Premium', icon: 'support_agent', color: '#7887ff', tint: '#eef0ff', unit: 'cặp kính', price: 300,
                features: ['Mời tối đa bốn người tham gia với giấy phép linh hoạt', 'Chia sẻ màn hình của chuyên gia mà không cần cài ứng dụng'] },
            { name: 'Iristick.Teams', icon: 'groups', color: '#b9a0dc', tint: '#f5f0fb', unit: 'cặp kính', price: 365,
                features: ['Gọi Microsoft Teams rảnh tay trực tiếp từ kính', 'Cuộc gọi không giới hạn và đăng nhập một lần'] },
            { name: 'Iristick.Collector', icon: 'barcode_scanner', color: '#50a69d', tint: '#edf8f6', unit: 'người dùng', price: 600,
                features: ['Thu thập dữ liệu rảnh tay bằng mã vạch và giọng nói', 'Không giới hạn mẫu và lượt quan sát'] },
            { name: 'Iristick.SDK', icon: 'code', color: '#1d1d1f', tint: '#f5f5f5', unit: 'năm', price: 1000, features: [] }
        ];

        function money(value) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumFractionDigits: 0 }).format(value * 35000);
        }

        function sectionBody(section) {
            Array.from(section.children).forEach(function (child) {
                if (!child.classList.contains('pc-head') && !child.classList.contains('pc-head-left')) { child.remove(); }
            });
        }

        function updateProgress(step, label) {
            var labels = calculator.querySelectorAll('.pc-steplabels span');
            if (labels[0]) { labels[0].textContent = 'Bước ' + step + ' trong 3'; }
            if (labels[1]) { labels[1].textContent = label; }
            var segments = calculator.querySelectorAll('.pc-seg');
            segments.forEach(function (segment, index) {
                segment.innerHTML = index < step ? '<div class="pc-seg-fill"></div>' : '';
            });
        }

        function renderSoftware() {
            sectionBody(sections[1]);
            var wrapper = document.createElement('div');
            wrapper.className = 'pc-step-body svelte-yaxynm';
            wrapper.innerHTML = '<div class="pricing-solution-intro"><span class="material-symbols-outlined">apps</span><div><strong>Thêm giải pháp</strong><small>Chọn Assist Premium, Teams hoặc Collector. Có thể kết hợp nhiều giải pháp.</small></div></div><div class="pc-solgrid svelte-yaxynm"></div>';
            var grid = wrapper.querySelector('.pc-solgrid');
            softwareProducts.slice(0, 3).forEach(function (product) {
                var selected = state.software.some(function (item) { return item.name === product.name; });
                var button = document.createElement('button');
                button.type = 'button';
                button.className = 'pc-card pc-software-card svelte-yaxynm' + (selected ? ' selected' : '');
                button.style.setProperty('--accent', product.color);
                button.style.setProperty('--tint', product.tint);
                var save = product.name.indexOf('Assist') !== -1 ? '<span class="pc-sol-save svelte-yaxynm">Tiết kiệm 2.625.000 ₫</span>' : '';
                var hint = product.name.indexOf('Assist') !== -1 && state.hardware === 'Iristick.G3' ? '<div class="pc-sol-hint svelte-yaxynm"><span class="material-symbols-outlined svelte-yaxynm">bolt</span>Gói cùng G3 chỉ 87.500.000 ₫ trong tháng này</div>' : '';
                var features = product.features.map(function (feature) { return '<li><span class="material-symbols-outlined">check</span>' + feature + '</li>'; }).join('');
                button.innerHTML = '<div class="pricing-sol-top"><div class="pc-sol-head svelte-yaxynm"><span class="pc-sol-icon svelte-yaxynm"><span class="material-symbols-outlined svelte-yaxynm">' + product.icon + '</span></span><div class="pc-sol-name svelte-yaxynm">' + product.name + '</div>' + save + '</div>' + hint +
                    '<div class="pc-sol-price svelte-yaxynm">' + money(product.price) + ' <small class="svelte-yaxynm">/ năm cho mỗi ' + product.unit + '</small></div></div><ul class="pricing-sol-features">' + features + '</ul>';
                button.addEventListener('click', function () {
                    var index = state.software.findIndex(function (item) { return item.name === product.name; });
                    if (index === -1) { state.software.push(product); } else { state.software.splice(index, 1); }
                    renderSoftware();
                });
                grid.appendChild(button);
            });
            if (state.software.length) {
                var rows = state.software.map(function (product) {
                    return '<div class="pc-lic-row svelte-yaxynm"><div class="pc-lic-info svelte-yaxynm"><span class="pc-lic-dot svelte-yaxynm" style="background:' + product.color + '"></span><div><div class="pc-lic-name svelte-yaxynm">' + product.name + '</div><small>' + money(product.price) + ' / năm cho mỗi ' + product.unit + '</small></div></div><div class="pc-lic-right svelte-yaxynm"><div class="pricing-inline-qty"><button type="button" data-qty="dec">−</button><strong>' + state.quantity + '</strong><button type="button" data-qty="inc">+</button></div><span>' + (product.unit === 'người dùng' ? 'người dùng' : 'cặp') + '</span><strong class="pc-lic-sub svelte-yaxynm">' + money(product.price * state.quantity) + '/năm</strong></div></div>';
                }).join('');
                wrapper.insertAdjacentHTML('beforeend', '<div class="pc-licenses svelte-yaxynm">' + rows + '<div class="pc-lic-total svelte-yaxynm"><span>Gia hạn mỗi năm</span><strong>' + money(state.software.reduce(function (sum, item) { return sum + item.price; }, 0) * state.quantity) + ' / năm</strong></div></div>');
            }
            wrapper.insertAdjacentHTML('beforeend', '<button type="button" class="pricing-sdk"><span class="material-symbols-outlined">code</span><span><strong>Tự xây dựng (SDK)</strong><small>Dành cho đội ngũ phát triển ứng dụng riêng trên kính.</small></span></button>');
            sections[1].appendChild(wrapper);
            wrapper.querySelectorAll('[data-qty]').forEach(function (control) {
                control.addEventListener('click', function () {
                    if (control.dataset.qty === 'inc' && state.quantity < 10) { state.quantity++; }
                    if (control.dataset.qty === 'dec' && state.quantity > 1) { state.quantity--; }
                    ensureQuantityControls(); renderSoftware();
                });
            });
            wrapper.querySelector('.pricing-sdk').addEventListener('click', function () {
                window.location.href = '/developers/';
            });
            updateProgress(2, 'Chọn phần mềm.');
            renderEstimate();
        }

        function renderEstimate() {
            sectionBody(sections[2]);
            var softwareAnnual = state.software.reduce(function (sum, item) { return sum + item.price; }, 0) * state.quantity;
            var hardwareTotal = state.path === 'buy' ? state.hardwarePrice * state.quantity : 0;
            var ready = state.path === 'software' || !!state.hardware;
            if (!ready) {
                sections[2].insertAdjacentHTML('beforeend', '<div class="pc-locked svelte-yaxynm">Hãy hoàn thành các bước trên để xem ước tính.</div>');
                return;
            }
            var lines = '';
            if (hardwareTotal) {
                lines += '<div class="pc-line svelte-yaxynm"><span>' + state.hardware + ' × ' + state.quantity + '</span><strong>' + money(hardwareTotal) + '</strong></div>';
            }
            state.software.forEach(function (item) {
                lines += '<div class="pc-line svelte-yaxynm"><span>' + item.name + ' × ' + state.quantity + '</span><strong>' + (item.price ? money(item.price * state.quantity) + '/năm' : 'Miễn phí') + '</strong></div>';
            });
            sections[2].insertAdjacentHTML('beforeend', '<div class="pc-lines svelte-yaxynm">' + (lines || '<div class="pc-line svelte-yaxynm"><span>Không thêm phần mềm</span><strong>—</strong></div>') + '</div>' +
                '<div class="pc-totals svelte-yaxynm"><div class="pc-total svelte-yaxynm"><span>Chi phí năm đầu</span><strong>' + money(hardwareTotal + softwareAnnual) + '</strong></div>' +
                '<div class="pc-total svelte-yaxynm"><span>Gia hạn phần mềm hằng năm</span><strong>' + money(softwareAnnual) + '</strong></div></div>' +
                '<div class="pc-ctas svelte-yaxynm"><a class="pc-cta svelte-yaxynm" href="/book-demo/">Yêu cầu báo giá chính thức</a></div>');
            updateProgress(3, 'Xem ước tính.');
        }

        function ensureQuantityControls() {
            var qty = sections[0].querySelector('.pc-qty');
            if (!qty) { return; }
            qty.style.display = state.hardware ? '' : 'none';
            var value = qty.querySelector('.val');
            if (value) { value.textContent = state.quantity; }
            var dec = qty.querySelector('.dec');
            var inc = qty.querySelector('.inc');
            if (dec && !dec.dataset.pricingBound) {
                dec.dataset.pricingBound = '1';
                dec.addEventListener('click', function () { if (state.quantity > 1) { state.quantity--; ensureQuantityControls(); renderEstimate(); } });
            }
            if (inc && !inc.dataset.pricingBound) {
                inc.dataset.pricingBound = '1';
                inc.addEventListener('click', function () { if (state.quantity < 10) { state.quantity++; ensureQuantityControls(); renderEstimate(); } });
            }
        }

        function selectHardware(card, name, price) {
            hardwareCards.forEach(function (item) { item.classList.remove('selected'); });
            card.classList.add('selected');
            state.hardware = name;
            state.hardwarePrice = price;
            ensureQuantityControls();
            renderSoftware();
        }

        hardwareCards.forEach(function (card) {
            var nameNode = card.querySelector('.name');
            var priceNode = card.querySelector('.pc-price');
            if (!nameNode || !priceNode) { return; }
            var price = Number(priceNode.textContent.replace(/[^0-9]/g, '').slice(0, -2)) || (nameNode.textContent.indexOf('G3') !== -1 ? 2275 : 1975);
            card.addEventListener('click', function () { selectHardware(card, nameNode.textContent.trim(), price); });
        });

        pathCards.forEach(function (card, index) {
            card.addEventListener('click', function () {
                if (index === 2) { window.location.href = '/trial-order/'; return; }
                pathCards.forEach(function (item) { item.classList.remove('selected'); });
                card.classList.add('selected');
                state.path = index === 1 ? 'software' : 'buy';
                state.hardware = null; state.hardwarePrice = 0; state.quantity = 1; state.software = [];
                sections[0].hidden = state.path === 'software';
                if (state.path === 'software') { renderSoftware(); } else {
                    sectionBody(sections[1]);
                    sections[1].insertAdjacentHTML('beforeend', '<div class="pc-locked svelte-yaxynm">Chọn kính trước.</div>');
                    renderEstimate(); updateProgress(1, 'Chọn kính.');
                }
            });
        });

        if (promo) {
            promo.addEventListener('click', function () {
                var g3 = hardwareCards.find(function (card) { return card.textContent.indexOf('Iristick.G3') !== -1; });
                if (g3) {
                    state.software = [softwareProducts[0]];
                    // Promo total: €2,200 hardware + €300 Assist Premium = €2,500.
                    selectHardware(g3, 'Iristick.G3', 2200);
                }
            });
        }
        ensureQuantityControls();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.body.classList.remove('iri-preload');
        document.querySelectorAll('.navigation-wrapper').forEach(enhanceNavigation);
        enhanceFaqs();
        enhanceNewsSearch();
        enhanceProductDetails();
        enhancePricingCalculator();
        document.querySelectorAll('a[target="_blank"]').forEach(function (link) {
            link.setAttribute('rel', 'noopener noreferrer');
        });
    });
}());
