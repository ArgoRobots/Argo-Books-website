// Feature tour (shared). Tab switching + mockup entrance animations,
// extracted from the landing page's inline script.
document.addEventListener('DOMContentLoaded', function () {
        // Feature tabs
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        let activeTabAnimation = null;

        function clearTabAnimation() {
            if (activeTabAnimation) {
                activeTabAnimation.forEach(id => clearTimeout(id));
                activeTabAnimation = null;
            }
        }

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.dataset.tab;

                clearTabAnimation();

                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));

                btn.classList.add('active');
                document.getElementById('tab-' + tabId).classList.add('active');

                // Reset animation classes on all mockups
                document.querySelectorAll('.animating').forEach(el => el.classList.remove('animating'));

                startTabAnimation(tabId);
            });
        });


        // Receipt scan & extract animation (loops)
        (function initReceiptScan() {
            const stage = document.getElementById('receiptScan');
            if (!stage) return;
            const status = document.getElementById('efStatus');
            const totalVal = stage.querySelector('.ef-total-val');
            let timers = [];
            function t(fn, d) { timers.push(setTimeout(fn, d)); }
            function clearTimers() { timers.forEach(clearTimeout); timers = []; }

            function countUp(el, target, duration) {
                const start = performance.now();
                function step(now) {
                    const p = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = '$' + (target * eased).toLocaleString('en-US', {
                        minimumFractionDigits: 2, maximumFractionDigits: 2
                    });
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }

            const seq = [
                { at: 380,  f: 'merchant' },
                { at: 720,  f: 'date' },
                { at: 1020, f: 'category', formOnly: true },
                { at: 1320, f: 'item-0' },
                { at: 1560, f: 'item-1' },
                { at: 1800, f: 'item-2' },
                { at: 2040, f: 'item-3' },
                { at: 2380, f: 'tax' },
                { at: 2680, f: 'total' }
            ];

            function run() {
                clearTimers();
                stage.classList.remove('done');
                stage.querySelectorAll('.scan-row').forEach(el => el.classList.remove('detected'));
                stage.querySelectorAll('.ef-field, .ef-line, .ef-trow').forEach(el => el.classList.remove('in'));
                if (status) status.classList.remove('in');
                if (totalVal) totalVal.textContent = '$0.00';

                // restart the beam sweep
                stage.classList.remove('scanning');
                void stage.offsetWidth;
                stage.classList.add('scanning');

                seq.forEach(s => {
                    t(() => {
                        if (!s.formOnly) {
                            const row = stage.querySelector('.scan-row[data-field="' + s.f + '"]');
                            if (row) row.classList.add('detected');
                        }
                        stage.querySelectorAll(
                            '.ef-field[data-field="' + s.f + '"], .ef-line[data-field="' + s.f + '"], .ef-trow[data-field="' + s.f + '"]'
                        ).forEach(el => el.classList.add('in'));
                        if (s.f === 'total' && totalVal) countUp(totalVal, 120.35, 800);
                    }, s.at);
                });

                // completion + loop
                t(() => {
                    stage.classList.remove('scanning');
                    stage.classList.add('done');
                    if (status) status.classList.add('in');
                }, 3150);
                t(run, 6400);
            }
            run();
        })();

        // Feature tab animation controller
        function startTabAnimation(tabId) {
            const timeouts = [];
            function t(fn, delay) {
                timeouts.push(setTimeout(fn, delay));
            }

            switch (tabId) {
                case 'expenses': animateExpenses(t); break;
                case 'predictive': animatePredictive(t); break;
                case 'inventory': animateInventory(t); break;
                case 'rental': animateRental(t); break;
                case 'customers': animateCustomers(t); break;
                case 'invoices': animateInvoices(t); break;
            }

            activeTabAnimation = timeouts;
        }

        // Expense & Revenue Tracking animation
        // Expense & Revenue overview: table streams in, charts pop up and animate
        function animateExpenses(t) {
            const stage = document.getElementById('expenseStage');
            if (!stage) return;
            const rows = stage.querySelectorAll('.txn-row');
            const chartLine = document.getElementById('expChartLine');
            const chartBars = document.getElementById('expChartBars');
            const netVal = stage.querySelector('.exp-net-val');

            function countUp(el, target, duration) {
                const start = performance.now();
                function step(now) {
                    const p = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(target * eased).toLocaleString('en-US');
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }

            function run() {
                rows.forEach(r => r.classList.remove('in'));
                if (chartLine) chartLine.classList.remove('in');
                if (chartBars) chartBars.classList.remove('in');
                if (netVal) netVal.textContent = '0';

                rows.forEach((r, i) => t(() => r.classList.add('in'), 250 + i * 170));
                const afterRows = 250 + rows.length * 170;

                t(() => { if (chartLine) chartLine.classList.add('in'); }, afterRows + 200);
                t(() => {
                    if (chartBars) chartBars.classList.add('in');
                    if (netVal) countUp(netVal, 12089, 1000);
                }, afterRows + 1000);

                t(run, afterRows + 4600);
            }
            run();
        }

        // Predictive Analytics animation
        // Predictive Analytics: KPIs count up, history draws, forecast extends into the cone
        function animatePredictive(t) {
            const stage = document.getElementById('forecastStage');
            if (!stage) return;
            const chart = stage.querySelector('.fc-chart');
            const kpis = stage.querySelectorAll('.fc-kpi');
            const next = stage.querySelector('.fc-next');
            const conf = stage.querySelector('.fc-conf');

            function countUp(el, target, duration) {
                const start = performance.now();
                function step(now) {
                    const p = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(target * eased).toLocaleString('en-US');
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }

            function run() {
                kpis.forEach(k => k.classList.remove('in'));
                if (chart) chart.classList.remove('in');
                if (next) next.textContent = '0';
                if (conf) conf.textContent = '0';

                kpis.forEach((k, i) => t(() => k.classList.add('in'), 250 + i * 160));
                t(() => {
                    if (next) countUp(next, 48200, 1200);
                    if (conf) countUp(conf, 94, 1200);
                }, 650);
                t(() => { if (chart) chart.classList.add('in'); }, 750);

                t(run, 6400);
            }
            run();
        }

        // Inventory Management animation
        // Inventory: stock cards stream in, counts count up, stock bars fill
        function animateInventory(t) {
            const stage = document.getElementById('inventoryStage');
            if (!stage) return;
            const cards = stage.querySelectorAll('.inv-card');

            function countUp(el, target, duration) {
                const start = performance.now();
                function step(now) {
                    const p = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(target * eased).toLocaleString('en-US');
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }

            function run() {
                cards.forEach(c => {
                    c.classList.remove('in');
                    const num = c.querySelector('.inv-num');
                    if (num) num.textContent = '0';
                });

                cards.forEach((c, i) => {
                    t(() => {
                        c.classList.add('in');
                        const num = c.querySelector('.inv-num');
                        if (num) countUp(num, parseInt(num.dataset.target, 10), 800);
                    }, 200 + i * 150);
                });

                t(run, 200 + cards.length * 150 + 4200);
            }
            run();
        }

        // Rental Management: calendar fades in, booked range fills, booking card pops up
        function animateRental(t) {
            const stage = document.getElementById('rentalStage');
            if (!stage) return;
            const seqDays = stage.querySelectorAll('.rent-seq');
            const booking = document.getElementById('rentBooking');

            function run() {
                stage.classList.remove('shown');
                seqDays.forEach(d => d.classList.remove('booked'));
                if (booking) booking.classList.remove('in');

                t(() => stage.classList.add('shown'), 100);
                seqDays.forEach((d, i) => t(() => d.classList.add('booked'), 800 + i * 200));
                t(() => { if (booking) booking.classList.add('in'); }, 800 + seqDays.length * 200 + 350);

                t(run, 6200);
            }
            run();
        }

        // Customer Management animation
        // Customer Management: directory streams in, profile card pops up
        function animateCustomers(t) {
            const stage = document.getElementById('customerStage');
            if (!stage) return;
            const rows = stage.querySelectorAll('.cust-row');
            const profile = document.getElementById('custProfile');
            const ltv = stage.querySelector('.cps-ltv');

            function countUp(el, target, duration) {
                const start = performance.now();
                function step(now) {
                    const p = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(target * eased).toLocaleString('en-US');
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }

            function run() {
                rows.forEach(r => r.classList.remove('in'));
                if (profile) profile.classList.remove('in');
                if (ltv) ltv.textContent = '0';

                rows.forEach((r, i) => t(() => r.classList.add('in'), 250 + i * 150));
                const afterRows = 250 + rows.length * 150;

                t(() => {
                    if (profile) profile.classList.add('in');
                    if (ltv) countUp(ltv, 4230, 1100);
                }, afterRows + 300);

                t(run, afterRows + 4600);
            }
            run();
        }

        // Invoicing animation
        // Invoice Studio: one-shot intro build, then reveal interactive controls
        function animateInvoices(t) {
            const studio = document.getElementById('invoiceStudio');
            const doc = document.getElementById('invoiceDoc');
            if (!studio || !doc) return;

            const colorPanel = document.getElementById('colorPanel');
            const templatePanel = document.getElementById('templatePanel');
            const status = document.getElementById('invStatus');
            const totalVal = doc.querySelector('.inv-total-value');

            function animateCounter(el, target, duration) {
                const startTime = performance.now();
                function update(now) {
                    const progress = Math.min((now - startTime) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = '$' + (target * eased).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    if (progress < 1) requestAnimationFrame(update);
                }
                requestAnimationFrame(update);
            }

            // Reset to the intro state
            doc.classList.add('intro');
            doc.querySelectorAll('.inv-anim, .inv-item').forEach(el => el.classList.remove('in'));
            if (status) status.classList.remove('in');
            if (colorPanel) colorPanel.classList.add('panel-hidden');
            if (templatePanel) templatePanel.classList.add('panel-hidden');

            // Staggered build of each section
            const steps = doc.querySelectorAll('.inv-top, .inv-billto, .inv-row-head, .inv-item, .inv-totals');
            steps.forEach((el, i) => t(() => el.classList.add('in'), 300 + i * 280));

            const lastAt = 300 + (steps.length - 1) * 280; // when the totals row lands

            // Count the total up as it appears
            if (totalVal) {
                totalVal.textContent = '$0.00';
                t(() => animateCounter(totalVal, 1234, 900), lastAt);
            }

            // Stamp "Paid"
            t(() => { if (status) status.classList.add('in'); }, lastAt + 600);

            // Reveal the interactive controls, then drop the intro state
            t(() => {
                if (colorPanel) colorPanel.classList.remove('panel-hidden');
                if (templatePanel) templatePanel.classList.remove('panel-hidden');
            }, lastAt + 1000);
            t(() => doc.classList.remove('intro'), lastAt + 1700);
        }

        // Invoice Studio: live color wheel + template switching (set up once)
        function initInvoiceStudio() {
            const studio = document.getElementById('invoiceStudio');
            const doc = document.getElementById('invoiceDoc');
            if (!studio || !doc) return;

            const wheel = document.getElementById('colorWheel');
            const thumb = document.getElementById('colorThumb');
            const lightSlider = document.getElementById('lightSlider');
            const lightThumb = document.getElementById('lightThumb');

            let hue = 227, sat = 79, light = 58;

            function apply() {
                studio.style.setProperty('--inv-accent', hue + ' ' + sat + '% ' + light + '%');
                if (lightSlider) {
                    lightSlider.style.background =
                        'linear-gradient(to right, hsl(' + hue + ' ' + sat + '% 72%), hsl(' + hue + ' ' + sat + '% 20%))';
                }
            }

            function pickColor(clientX, clientY) {
                const rect = wheel.getBoundingClientRect();
                const r = rect.width / 2;
                let x = clientX - rect.left - r;
                let y = clientY - rect.top - r;
                let dist = Math.sqrt(x * x + y * y);
                if (dist > r) { x = x / dist * r; y = y / dist * r; dist = r; }
                let ang = Math.atan2(y, x) * 180 / Math.PI;
                if (ang < 0) ang += 360;
                hue = Math.round(ang);
                sat = Math.round(48 + (dist / r) * 37); // 48–85, kept tasteful
                thumb.style.left = (r + x) + 'px';
                thumb.style.top = (r + y) + 'px';
                apply();
            }

            function pickLight(clientX) {
                const rect = lightSlider.getBoundingClientRect();
                let p = (clientX - rect.left) / rect.width;
                p = Math.max(0, Math.min(1, p));
                light = Math.round(64 - p * 30); // 64 (light) → 34 (dark)
                lightThumb.style.left = (p * 100) + '%';
                apply();
            }

            function makeDrag(pickFn) {
                return function (e) {
                    e.preventDefault();
                    const move = ev => {
                        const pt = ev.touches ? ev.touches[0] : ev;
                        pickFn(pt.clientX, pt.clientY);
                    };
                    move(e);
                    const up = () => {
                        document.removeEventListener('mousemove', move);
                        document.removeEventListener('mouseup', up);
                        document.removeEventListener('touchmove', move);
                        document.removeEventListener('touchend', up);
                    };
                    document.addEventListener('mousemove', move);
                    document.addEventListener('mouseup', up);
                    document.addEventListener('touchmove', move, { passive: false });
                    document.addEventListener('touchend', up);
                };
            }

            if (wheel) {
                const drag = makeDrag((x, y) => pickColor(x, y));
                wheel.addEventListener('mousedown', drag);
                wheel.addEventListener('touchstart', drag, { passive: false });
            }
            if (lightSlider) {
                const drag = makeDrag(x => pickLight(x));
                lightSlider.addEventListener('mousedown', drag);
                lightSlider.addEventListener('touchstart', drag, { passive: false });
            }

            const tmplBtns = studio.querySelectorAll('.tmpl-btn');
            tmplBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    doc.classList.remove('theme-modern', 'theme-contemporary', 'theme-classic');
                    doc.classList.add(btn.dataset.theme);
                    tmplBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                });
            });

            // Place the wheel thumb at the starting accent
            if (thumb && wheel) {
                const r = 80; // half of the 160px wheel
                const rad = hue * Math.PI / 180;
                const d = ((sat - 48) / 37) * r;
                thumb.style.left = (r + Math.cos(rad) * d) + 'px';
                thumb.style.top = (r + Math.sin(rad) * d) + 'px';
            }
            apply();
        }
        initInvoiceStudio();

});
