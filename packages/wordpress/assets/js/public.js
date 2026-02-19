(function () {
    'use strict';
    // CFG is now passed via wp_add_inline_script
    var CFG = window.rayelsLoi25 || {};
    var SK = 'loi25-consent',
        SD = 'loi25-consent-date';

    // ─── Check expiry ───
    function isExpired() {
        var d = localStorage.getItem(SD);
        if (!d) return true;
        var age = (Date.now() - parseInt(d, 10)) / (1000 * 60 * 60 * 24);
        return age > CFG.expiry;
    }

    function hasConsent() {
        try {
            return localStorage.getItem(SK) && !isExpired();
        } catch (e) {
            return false;
        }
    }

    // ─── Re-consent floating button ───
    function showReconsent() {
        if (!CFG.reconsent) return;
        var rb = document.createElement('button');
        rb.id = 'loi25-reconsent';
        rb.setAttribute('aria-label', CFG.lang === 'fr' ? 'Gérer les cookies' : 'Manage cookies');
        rb.innerHTML = CFG.showIcon ? '🍪' : '⚙️';
        rb.style.background = CFG.brand;
        rb.onclick = function () {
            try {
                localStorage.removeItem(SK);
                localStorage.removeItem(SD);
            } catch (e) { }
            rb.remove();
            showBanner();
        };
        document.body.appendChild(rb);
    }

    // ─── If already consented ───
    if (hasConsent()) {
        showReconsent();
        return;
    }

    // ─── If expired, clear old consent ───
    if (localStorage.getItem(SK) && isExpired()) {
        try {
            localStorage.removeItem(SK);
            localStorage.removeItem(SD);
        } catch (e) { }
    }

    // ─── Theme colors ───
    var dk = CFG.theme === 'dark';
    var colors = {
        bg: dk ? 'rgba(24,24,27,' + (CFG.glass ? '.75' : '1') + ')' : 'rgba(255,255,255,' + (CFG.glass ? '.8' : '1') + ')',
        text: dk ? '#e4e4e7' : '#1e293b',
        muted: dk ? '#a1a1aa' : '#64748b',
        border: dk ? '#3f3f46' : '#e2e8f0',
        btnBg: dk ? '#27272a' : '#f1f5f9',
        btnText: dk ? '#e4e4e7' : '#334155',
    };

    function showBanner() {
        // Cleanup any existing banner/overlay first
        var old = document.getElementById('loi25-banner');
        if (old) old.remove();
        var oldOv = document.getElementById('loi25-overlay');
        if (oldOv) oldOv.remove();

        var d = document.createElement('div');
        d.id = 'loi25-banner';
        d.setAttribute('role', 'dialog');
        d.setAttribute('aria-label', CFG.lang === 'fr' ? 'Consentement aux cookies' : 'Cookie consent');
        d.setAttribute('aria-modal', 'false');

        // ─── Style: Bar ───
        if (CFG.style === 'bar') {
            d.style.cssText = 'position:fixed;left:0;right:0;' + (CFG.position === 'top' ? 'top:0' : 'bottom:0') + ';z-index:999999;background:' + colors.bg + ';border-' + (CFG.position === 'top' ? 'bottom' : 'top') + ':1px solid ' + colors.border + ';padding:0;color:' + colors.text + ';box-shadow:0 ' + (CFG.position === 'top' ? '2' : '-2') + 'px 20px rgba(0,0,0,.1);';
            if (CFG.animation === 'slide') {
                d.classList.add('loi25-anim-slide', 'loi25-hidden-' + CFG.position);
            } else {
                d.classList.add('loi25-anim-fade', 'loi25-hidden-fade');
            }
        }
        // ─── Style: Popup ───
        else if (CFG.style === 'popup') {
            d.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%) scale(.9);z-index:999999;background:' + colors.bg + ';border-radius:16px;padding:0;color:' + colors.text + ';box-shadow:0 25px 60px rgba(0,0,0,.2);max-width:480px;width:calc(100% - 40px);';
            d.classList.add('loi25-style-popup', 'loi25-hidden-popup');
            // Overlay
            var overlay = document.createElement('div');
            overlay.id = 'loi25-overlay';
            overlay.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;z-index:999998;background:rgba(0,0,0,.4);opacity:0;transition:opacity .35s ease;';
            document.body.appendChild(overlay);
            requestAnimationFrame(function () {
                overlay.style.opacity = '1';
            });
        }
        // ─── Style: Corner ───
        else if (CFG.style === 'corner') {
            d.style.cssText = 'position:fixed;' + (CFG.position === 'top' ? 'top:20px' : 'bottom:20px') + ';right:20px;z-index:999999;background:' + colors.bg + ';border-radius:16px;padding:0;color:' + colors.text + ';box-shadow:0 8px 30px rgba(0,0,0,.12);max-width:380px;width:calc(100% - 40px);border:1px solid ' + colors.border + ';';
            d.classList.add('loi25-style-corner');
            if (CFG.animation === 'slide') {
                d.style.transform = 'translateX(120%)';
                d.style.opacity = '0';
                d.style.transition = 'transform .4s cubic-bezier(.4,0,.2,1),opacity .4s ease';
            } else {
                d.classList.add('loi25-hidden-fade');
                d.style.transition = 'opacity .5s ease';
            }
        }

        if (CFG.glass) d.classList.add('loi25-glass');

        // ─── Inner HTML ───
        var T = CFG.texts;
        var inner = '<div class="loi25-inner" style="padding:24px 28px;">' +
            '<div style="font-weight:700;font-size:17px;margin-bottom:10px;display:flex;align-items:center;gap:8px;">' +
            (CFG.showIcon ? '<span style="font-size:22px;">🍪</span> ' : '') + escHtml(T.title) + '</div>' +
            '<p style="margin:0 0 18px;color:' + colors.muted + ';font-size:14px;line-height:1.6;">' + escHtml(T.message) + '</p>' +
            '<div class="loi25-btns" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">' +
            '<button id="loi25-yes" style="background:' + CFG.brand + ';color:#fff;border:none;padding:11px 24px;border-radius:8px;font-weight:600;font-size:14px;">' + escHtml(T.accept) + '</button>' +
            '<button id="loi25-no" style="background:' + colors.btnBg + ';color:' + colors.btnText + ';border:1px solid ' + colors.border + ';padding:11px 24px;border-radius:8px;font-weight:600;font-size:14px;">' + escHtml(T.reject) + '</button>' +
            '</div>' +
            '<div style="margin-top:14px;display:flex;flex-wrap:wrap;gap:12px;align-items:center;">' +
            '<a href="' + CFG.privacyUrl + '" style="color:' + colors.muted + ';font-size:12px;text-decoration:underline;" target="_blank" rel="noopener">' + escHtml(T.privacy) + '</a>';

        if (CFG.poweredBy) {
            inner += '<a href="https://rayelsconsulting.com" target="_blank" rel="noopener" style="color:' + colors.muted + ';font-size:11px;margin-left:auto;text-decoration:none;opacity:.6;">' + escHtml(T.powered) + ' Rayels</a>';
        }
        inner += '</div></div>';

        d.innerHTML = inner;
        document.body.appendChild(d);

        // ─── Animate in ───
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                if (CFG.style === 'bar') {
                    d.classList.remove('loi25-hidden-' + CFG.position, 'loi25-hidden-fade');
                } else if (CFG.style === 'popup') {
                    d.classList.remove('loi25-hidden-popup');
                    d.style.transform = 'translate(-50%,-50%) scale(1)';
                    d.style.opacity = '1';
                } else if (CFG.style === 'corner') {
                    d.style.transform = 'translateX(0)';
                    d.style.opacity = '1';
                    d.classList.remove('loi25-hidden-fade');
                }
            });
        });

        // ─── Accept handler ───
        function accept(level) {
            try {
                localStorage.setItem(SK, level);
                localStorage.setItem(SD, Date.now().toString());
            } catch (e) { }

            // Animate out
            if (CFG.style === 'bar') {
                d.classList.add('loi25-hidden-' + CFG.position);
            } else if (CFG.style === 'popup') {
                d.style.transform = 'translate(-50%,-50%) scale(.9)';
                d.style.opacity = '0';
                var ov = document.getElementById('loi25-overlay');
                if (ov) ov.style.opacity = '0';
            } else if (CFG.style === 'corner') {
                d.style.transform = 'translateX(120%)';
                d.style.opacity = '0';
            }

            setTimeout(function () {
                d.remove();
                var ov = document.getElementById('loi25-overlay');
                if (ov) ov.remove();
                showReconsent();
            }, 400);

            // Google Consent Mode update
            if (window.gtag) {
                if (level === 'all') {
                    gtag('consent', 'update', {
                        'ad_storage': 'granted',
                        'ad_user_data': 'granted',
                        'ad_personalization': 'granted',
                        'analytics_storage': 'granted'
                    });
                }
            }

            // Log consent via sendBeacon (non-blocking, survives page reload)
            try {
                var fd = new FormData();
                fd.append('action', 'rayels_loi25_log_consent'); // Updated prefix
                fd.append('type', level);
                fd.append('_nonce', CFG.nonce);
                navigator.sendBeacon(CFG.ajaxUrl, fd);
            } catch (e) { }

            // Google Consent Mode handles updates without page reload.
        }

        document.getElementById('loi25-yes').onclick = function () {
            accept('all');
        };
        document.getElementById('loi25-no').onclick = function () {
            accept('necessary');
        };

        // ─── Keyboard: Escape = Necessary Only ───
        document.addEventListener('keydown', function handler(e) {
            if (e.key === 'Escape') {
                accept('necessary');
                document.removeEventListener('keydown', handler);
            }
        });

        // Focus first button for accessibility
        setTimeout(function () {
            var b = document.getElementById('loi25-yes');
            if (b) b.focus();
        }, 500);
    }

    function escHtml(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    showBanner();
})();
