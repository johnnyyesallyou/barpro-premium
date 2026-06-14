/* ============================================================
   BarPro — Event Premium client JS (ТЗ v1.0)
   Лёгкий, без зависимостей. Плавный скролл уже в CSS.
   ============================================================ */
(function () {
    'use strict';

    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Fade-In on scroll ---------- */
    function initReveal() {
        var els = document.querySelectorAll('.tz-reveal');
        if (!els.length) return;

        if (reducedMotion || !('IntersectionObserver' in window)) {
            els.forEach(function (el) { el.classList.add('is-in'); });
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) {
                    en.target.classList.add('is-in');
                    io.unobserve(en.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
        els.forEach(function (el) { io.observe(el); });
    }

    /* ---------- Parallax for Hero ---------- */
    function initParallax() {
        if (reducedMotion) return;
        var heroes = document.querySelectorAll('.tz-hero[data-parallax]');
        if (!heroes.length) return;

        var raf = null;
        function update() {
            heroes.forEach(function (h) {
                var rect = h.getBoundingClientRect();
                var y = -rect.top * 0.18;
                var media = h.querySelector('.tz-hero__media');
                if (media) media.style.transform = 'translate3d(0,' + y.toFixed(1) + 'px,0)';
            });
            raf = null;
        }
        window.addEventListener('scroll', function () {
            if (raf === null) raf = window.requestAnimationFrame(update);
        }, { passive: true });
        update();
    }

    /* ---------- Lightbox for masonry ---------- */
    function initLightbox() {
        var images = document.querySelectorAll('[data-tz-lightbox] img');
        if (!images.length) return;

        var box = document.createElement('div');
        box.className = 'tz-lightbox';
        box.innerHTML = '<button class="tz-lightbox__close" aria-label="Закрыть">×</button><img alt="">';
        document.body.appendChild(box);
        var imgEl = box.querySelector('img');

        function open(src, alt) {
            imgEl.src = src; imgEl.alt = alt || '';
            box.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
        function close() {
            box.classList.remove('is-open');
            document.body.style.overflow = '';
            imgEl.src = '';
        }
        images.forEach(function (img) {
            img.style.cursor = 'zoom-in';
            img.addEventListener('click', function () {
                open(img.dataset.full || img.src, img.alt);
            });
        });
        box.addEventListener('click', function (e) {
            if (e.target === box || e.target.classList.contains('tz-lightbox__close')) close();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });
    }

    /* ---------- Filter (cocktails / cases) ---------- */
    function initFilters() {
        var bars = document.querySelectorAll('[data-tz-filter]');
        bars.forEach(function (bar) {
            var target = document.querySelector(bar.dataset.tzFilter);
            if (!target) return;
            bar.addEventListener('click', function (e) {
                var btn = e.target.closest('.tz-filter__btn');
                if (!btn) return;
                bar.querySelectorAll('.tz-filter__btn').forEach(function (b) { b.classList.remove('is-active'); });
                btn.classList.add('is-active');
                var cat = btn.dataset.cat || 'all';
                target.querySelectorAll('[data-cat]').forEach(function (card) {
                    var match = cat === 'all' || card.dataset.cat === cat || card.dataset.cat.split(' ').indexOf(cat) > -1;
                    card.style.display = match ? '' : 'none';
                });
            });
        });
    }

    /* ---------- TZ Calculator (5 шагов из ТЗ) ---------- */
    function initTzCalculator() {
        var calc = document.getElementById('tzCalc');
        if (!calc) return;

        var steps      = calc.querySelectorAll('.tz-calc__step');
        var dots       = calc.querySelectorAll('.tz-calc__dot');
        var currentIdx = 0;
        var formStartTime = Math.floor(Date.now() / 1000); // Fix 2: реальное время начала сессии

        var state = {
            eventType: '',
            guests: 50,
            mainServices: [],
            extras: [],
            name: '', phone: '', email: '', telegram: '', comment: ''
        };

        // Прайс-карта (диапазоны "от-до" на гостя в зависимости от выбора)
        var PRICES = {
            // мин/макс на 1 гостя по типу основной услуги (в рублях)
            'bar':          { min: 800,  max: 1800 },
            'catering':     { min: 1500, max: 3500 },
            'bar-catering': { min: 2200, max: 5000 },
            'barmen':       { min: 400,  max: 900  },
            'waiters':      { min: 350,  max: 800  }
        };
        var EXTRA_PRICES = {
            'ice':       { min:  3000, max:  8000 },
            'dishes':    { min:  5000, max: 12000 },
            'furniture': { min: 15000, max: 40000 },
            'bar-show':  { min: 12000, max: 25000 },
            'coffee':    { min: 15000, max: 35000 }
        };

        function showStep(i) {
            steps.forEach(function (s, idx) {
                s.classList.toggle('is-active', idx === i);
            });
            dots.forEach(function (d, idx) {
                d.classList.toggle('is-active', idx === i);
                d.classList.toggle('is-done', idx < i);
            });
            currentIdx = i;
            try { calc.scrollIntoView({ behavior: 'smooth', block: 'start' }); } catch (e) {}
        }

        // Универсальная обработка выбора choice / multichoice
        calc.addEventListener('click', function (e) {
            var ch = e.target.closest('.tz-choice');
            if (!ch) return;
            var input = ch.querySelector('input');
            if (!input) return;
            if (input.type === 'radio') {
                ch.parentNode.querySelectorAll('.tz-choice').forEach(function (c) { c.classList.remove('is-selected'); });
                ch.classList.add('is-selected');
                input.checked = true;
            } else if (input.type === 'checkbox') {
                input.checked = !input.checked;
                ch.classList.toggle('is-selected', input.checked);
            }
        });

        var guestsRange = document.getElementById('tzGuests');
        var guestsOut   = document.getElementById('tzGuestsOut');
        if (guestsRange && guestsOut) {
            // Fix 4: синхронизировать min/max ползунка с серверной конфигурацией
            if (window.barproAjax && window.barproAjax.pricing) {
                if (window.barproAjax.pricing.guestsMin) guestsRange.min = window.barproAjax.pricing.guestsMin;
                if (window.barproAjax.pricing.guestsMax) guestsRange.max = window.barproAjax.pricing.guestsMax;
                // Сбросить текущее значение в допустимый диапазон
                var curVal = parseInt(guestsRange.value, 10);
                var maxVal = parseInt(guestsRange.max, 10);
                if (curVal > maxVal) { guestsRange.value = maxVal; guestsOut.textContent = maxVal; }
            }
            guestsRange.addEventListener('input', function () {
                guestsOut.textContent = guestsRange.value;
            });
        }

        // Кнопки навигации
        calc.querySelectorAll('[data-tz-next]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!validateStep(currentIdx)) return;
                if (currentIdx < steps.length - 1) showStep(currentIdx + 1);
                if (currentIdx === steps.length - 1) renderResult();
            });
        });
        calc.querySelectorAll('[data-tz-prev]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (currentIdx > 0) showStep(currentIdx - 1);
            });
        });

        function validateStep(idx) {
            // Шаг 1 — тип события
            if (idx === 0) {
                var et = calc.querySelector('input[name="eventType"]:checked');
                if (!et) { alert('Пожалуйста, выберите тип мероприятия'); return false; }
                state.eventType = et.value;
            }
            // Шаг 2 — гости (range уже валиден)
            if (idx === 1) {
                state.guests = parseInt(guestsRange.value, 10);
            }
            // Шаг 3 — услуги
            if (idx === 2) {
                var services = [].slice.call(calc.querySelectorAll('input[name="mainService"]:checked')).map(function (c) { return c.value; });
                if (!services.length) { alert('Выберите хотя бы одну услугу'); return false; }
                state.mainServices = services;
            }
            // Шаг 4 — доп. услуги (опционально)
            if (idx === 3) {
                state.extras = [].slice.call(calc.querySelectorAll('input[name="extra"]:checked')).map(function (c) { return c.value; });
            }
            // Шаг 5 — контакты
            if (idx === 4) {
                var name = calc.querySelector('#tzName');
                var phone = calc.querySelector('#tzPhone');
                state.name     = name  ? name.value.trim()  : '';
                state.phone    = phone ? phone.value.trim() : '';
                state.email    = (calc.querySelector('#tzEmail')   || {}).value || '';
                state.telegram = (calc.querySelector('#tzTg')      || {}).value || '';
                state.comment  = (calc.querySelector('#tzComment') || {}).value || '';
                if (!state.name || state.name.length < 2) { alert('Укажите имя'); return false; }
                if (!state.phone || state.phone.replace(/\D/g, '').length < 10) { alert('Укажите телефон'); return false; }
                submitLead();
            }
            return true;
        }

        function calcRange() {
            var minP = 0, maxP = 0;
            state.mainServices.forEach(function (k) {
                var p = PRICES[k]; if (!p) return;
                minP += p.min * state.guests;
                maxP += p.max * state.guests;
            });
            state.extras.forEach(function (k) {
                var p = EXTRA_PRICES[k]; if (!p) return;
                minP += p.min;
                maxP += p.max;
            });
            // Минимум для зарплаты, если не выбрано ничего
            if (minP === 0) { minP = 35000; maxP = 85000; }
            return { min: Math.round(minP / 1000) * 1000, max: Math.round(maxP / 1000) * 1000 };
        }

        function fmt(n) { return n.toLocaleString('ru-RU') + ' ₽'; }

        function renderResult() {
            var range = calcRange();
            var out = document.getElementById('tzResultRange');
            if (out) out.textContent = 'от ' + fmt(range.min) + ' до ' + fmt(range.max);

            var summary = document.getElementById('tzResultSummary');
            if (summary) {
                var names = {
                    'wedding': 'Свадьба', 'corporate': 'Корпоратив', 'birthday': 'День рождения',
                    'festival': 'Фестиваль', 'private': 'Частное мероприятие'
                };
                var svc = {
                    'bar': 'Бар', 'catering': 'Кейтеринг', 'bar-catering': 'Бар + Кейтеринг',
                    'barmen': 'Бармены', 'waiters': 'Официанты'
                };
                summary.innerHTML =
                    '<li><strong>Тип:</strong> ' + (names[state.eventType] || '—') + '</li>' +
                    '<li><strong>Гостей:</strong> ' + state.guests + '</li>' +
                    '<li><strong>Услуги:</strong> ' + state.mainServices.map(function (s) { return svc[s] || s; }).join(', ') + '</li>';
            }
        }

        function renderError(msg) {
            // Показываем ошибку на экране результата вместо alert
            var errorEl = document.getElementById('tzSubmitError');
            if (!errorEl) {
                errorEl = document.createElement('p');
                errorEl.id = 'tzSubmitError';
                errorEl.style.cssText = 'color:#e55;margin-top:1rem;font-size:.95rem;';
                var resultStep = calc.querySelector('.tz-calc__step[data-step="5"]') || calc.querySelector('.tz-calc__result');
                if (resultStep) resultStep.appendChild(errorEl);
            }
            errorEl.textContent = msg;
        }

        function submitLead() {
            if (typeof window.barproAjax === 'undefined') return;
            var range = calcRange();
            var data = new URLSearchParams();
            data.append('action',      'save_lead');
            data.append('nonce',       window.barproAjax.nonce);
            data.append('name',        state.name);
            data.append('email',       state.email);
            data.append('phone',       state.phone);
            data.append('_form_time',  formStartTime); // Fix 2: реальное время начала формы
            data.append('message',     JSON.stringify({
                source:     'tz_calculator',
                eventType:  state.eventType,
                guests:     state.guests,
                services:   state.mainServices,
                extras:     state.extras,
                telegram:   state.telegram,
                comment:    state.comment,
                priceRange: range
            }));
            fetch(window.barproAjax.ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: data.toString()
            })
            .then(function(r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function(res) {
                if (res && res.success === false) {
                    var msg = (res.data && res.data.message) ? res.data.message : 'Ошибка при отправке. Позвоните нам напрямую.';
                    renderError(msg);
                }
            })
            .catch(function() {
                renderError('Не удалось отправить заявку. Проверьте соединение или позвоните нам.');
            });
        }
    }

    /* ---------- Cocktail card constructor (по типу события) ---------- */
    function initCocktailConstructor() {
        var box = document.getElementById('tzCocktailConstructor');
        if (!box) return;

        var presets = {
            'wedding':    ['classic', 'signature', 'non-alcoholic'],
            'corporate':  ['classic', 'non-alcoholic'],
            'birthday':   ['signature', 'classic'],
            'festival':   ['classic'],
            'private':    ['signature', 'non-alcoholic']
        };

        var btn = box.querySelector('#tzCocktailGo');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var type = (box.querySelector('input[name="ccType"]:checked') || {}).value || 'wedding';
            var guests = parseInt((box.querySelector('#ccGuests') || {}).value || '50', 10);
            var format = (box.querySelector('input[name="ccFormat"]:checked') || {}).value || 'bar';

            var cats = presets[type] || ['classic'];
            var portionsPerGuest = format === 'bar' ? 3 : 2;
            var totalPortions = guests * portionsPerGuest;

            // Подсветить коктейли соответствующих категорий
            var grid = document.querySelector('#cocktailsGrid');
            if (grid) {
                grid.querySelectorAll('[data-cat]').forEach(function (card) {
                    var c = card.dataset.cat;
                    var match = cats.some(function (k) { return c.indexOf(k) > -1; });
                    card.style.opacity = match ? '1' : '.25';
                    card.style.filter  = match ? 'none' : 'grayscale(0.8)';
                });
            }

            var out = box.querySelector('#ccResult');
            if (out) {
                out.innerHTML =
                    '<p>Рекомендуем для вашего события <strong>' + totalPortions + ' порций</strong> ' +
                    'в категориях: <strong>' + cats.join(', ') + '</strong>.</p>' +
                    '<p>Подходящие коктейли подсвечены ниже.</p>';
                out.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    /* ---------- Init ---------- */
    function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }
    ready(function () {
        initReveal();
        initParallax();
        initLightbox();
        initFilters();
        initTzCalculator();
        initCocktailConstructor();
    });
})();
