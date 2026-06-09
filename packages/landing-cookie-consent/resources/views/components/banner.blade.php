@once('landing-cookie-consent-styles')
    <x-cookie-consent::styles />
@endonce

@once('landing-cookie-consent')
    <div
        id="landing-cookie-consent"
        @class([
            'lp-cookie-consent',
            'lp-cookie-consent-position-'.$position,
            'lp-cookie-consent-layout-'.$layout,
        ])
        data-landing-cookie-consent
        hidden
    >
        <section class="lp-cookie-banner" data-cookie-banner role="region" aria-label="{{ $ariaLabel }}" hidden>
            <div class="lp-container lp-cookie-banner-inner">
                <div class="lp-cookie-copy">
                    @if ($title)
                        <h2 class="lp-subheading lp-cookie-title">{{ $title }}</h2>
                    @endif

                    <p class="lp-muted lp-cookie-message">
                        {{ $message }}

                        @if ($policyUrl)
                            <a class="lp-cookie-policy-link" href="{{ $policyUrl }}">
                                {{ $bannerLabels['policy'] }}
                            </a>
                        @endif
                    </p>
                </div>

                <div class="lp-cookie-actions">
                    @if ($hasOptionalCategories)
                        <button type="button" class="lp-button lp-button-secondary" data-cookie-open-preferences>
                            {{ $bannerLabels['configure'] }}
                        </button>

                        <button type="button" class="lp-button lp-button-secondary" data-cookie-reject-optional>
                            {{ $bannerLabels['reject_optional'] }}
                        </button>
                    @endif

                    <button type="button" class="lp-button lp-button-primary" data-cookie-accept-all>
                        {{ $bannerLabels['accept_all'] }}
                    </button>
                </div>
            </div>
        </section>

        <div
            class="lp-cookie-modal"
            data-cookie-modal
            role="dialog"
            aria-modal="true"
            aria-labelledby="landing-cookie-consent-title"
            hidden
        >
            <div class="lp-card lp-cookie-dialog">
                <header class="lp-cookie-modal-header">
                    <div>
                        <h2 id="landing-cookie-consent-title" class="lp-subheading">
                            {{ $modalLabels['title'] }}
                        </h2>

                        <p class="lp-muted lp-cookie-modal-description">
                            {{ $modalLabels['description'] }}
                        </p>
                    </div>

                    <button type="button" class="lp-cookie-close" data-cookie-close-preferences aria-label="{{ $modalLabels['close'] }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </header>

                <div class="lp-cookie-categories">
                    @foreach ($categories as $category)
                        <label class="lp-cookie-category" for="lp-cookie-category-{{ $category['name'] }}">
                            <span class="lp-cookie-category-copy">
                                <span class="lp-cookie-category-name">{{ $category['label'] }}</span>

                                @if ($category['description'])
                                    <span class="lp-muted lp-cookie-category-description">
                                        {{ $category['description'] }}
                                    </span>
                                @endif
                            </span>

                            <span class="lp-cookie-toggle">
                                <input
                                    id="lp-cookie-category-{{ $category['name'] }}"
                                    type="checkbox"
                                    value="{{ $category['name'] }}"
                                    data-cookie-category-input
                                    data-required="{{ $category['required'] ? 'true' : 'false' }}"
                                    data-default="{{ $category['default'] ? 'true' : 'false' }}"
                                    @checked($category['default'])
                                    @disabled($category['required'])
                                >
                                <span aria-hidden="true"></span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <footer class="lp-cookie-modal-actions">
                    @if ($hasOptionalCategories)
                        <button type="button" class="lp-button lp-button-secondary" data-cookie-reject-optional>
                            {{ $modalLabels['reject_optional'] }}
                        </button>
                    @endif

                    <button type="button" class="lp-button lp-button-secondary" data-cookie-save-preferences>
                        {{ $modalLabels['save_preferences'] }}
                    </button>

                    <button type="button" class="lp-button lp-button-primary" data-cookie-accept-all>
                        {{ $modalLabels['accept_all'] }}
                    </button>
                </footer>
            </div>
        </div>

        <button type="button" class="lp-button lp-button-secondary lp-cookie-reopen" data-cookie-reopen hidden>
            {{ $bannerLabels['reopen'] }}
        </button>
    </div>

    <script id="landing-cookie-consent-config" type="application/json">{!! $configJson !!}</script>
    <script id="landing-cookie-consent-loader">
        (() => {
            'use strict';

            const configElement = document.getElementById('landing-cookie-consent-config');
            const root = document.querySelector('[data-landing-cookie-consent]');

            if (!configElement || !root) {
                return;
            }

            const config = JSON.parse(configElement.textContent || '{}');
            const categories = Array.isArray(config.categories) ? config.categories : [];

            if (categories.length === 0) {
                return;
            }

            const storageKey = config.storageKey || 'landing_cookie_consent';
            const version = String(config.version || '1');
            const lifetimeDays = Number(config.lifetimeDays || 180);
            const banner = root.querySelector('[data-cookie-banner]');
            const modal = root.querySelector('[data-cookie-modal]');
            const reopenButton = root.querySelector('[data-cookie-reopen]');
            const categoryInputs = Array.from(root.querySelectorAll('[data-cookie-category-input]'));
            const firstAction = root.querySelector('[data-cookie-open-preferences], [data-cookie-accept-all]');

            const asObject = (value) => value && typeof value === 'object' && !Array.isArray(value) ? value : {};
            const requiredNames = new Set(categories.filter((category) => category.required).map((category) => category.name));
            const categoryNames = categories.map((category) => category.name);

            const now = () => new Date();
            const isoDate = (date) => date.toISOString();

            const addDays = (date, days) => {
                const nextDate = new Date(date.getTime());
                nextDate.setDate(nextDate.getDate() + Math.max(1, days));

                return nextDate;
            };

            const consentId = () => {
                const key = storageKey + '_id';

                try {
                    const existing = window.localStorage.getItem(key);

                    if (existing) {
                        return existing;
                    }

                    const generated = window.crypto && typeof window.crypto.randomUUID === 'function'
                        ? window.crypto.randomUUID()
                        : String(Date.now()) + '-' + Math.random().toString(16).slice(2);

                    window.localStorage.setItem(key, generated);

                    return generated;
                } catch (error) {
                    return String(Date.now());
                }
            };

            const normalizeCategories = (values = {}) => {
                const input = asObject(values);
                const normalized = {};

                categoryNames.forEach((name) => {
                    normalized[name] = requiredNames.has(name) || input[name] === true;
                });

                return normalized;
            };

            const readStoredConsent = () => {
                try {
                    const raw = window.localStorage.getItem(storageKey);

                    if (!raw) {
                        return null;
                    }

                    const stored = JSON.parse(raw);

                    if (!stored || typeof stored !== 'object' || stored.version !== version) {
                        return null;
                    }

                    if (stored.expiresAt && new Date(stored.expiresAt).getTime() <= Date.now()) {
                        window.localStorage.removeItem(storageKey);

                        return null;
                    }

                    return {
                        ...stored,
                        categories: normalizeCategories(stored.categories),
                    };
                } catch (error) {
                    window.localStorage.removeItem(storageKey);

                    return null;
                }
            };

            const storeConsent = (payload) => {
                try {
                    window.localStorage.setItem(storageKey, JSON.stringify(payload));
                } catch (error) {
                    return false;
                }

                return true;
            };

            const buildConsent = (values, action) => {
                const acceptedAt = now();

                return {
                    id: consentId(),
                    version,
                    action,
                    categories: normalizeCategories(values),
                    acceptedAt: isoDate(acceptedAt),
                    expiresAt: isoDate(addDays(acceptedAt, lifetimeDays)),
                };
            };

            const defaultCategories = () => {
                const defaults = {};

                categories.forEach((category) => {
                    defaults[category.name] = category.required === true || category.default === true;
                });

                return normalizeCategories(defaults);
            };

            const allCategories = () => {
                const granted = {};

                categoryNames.forEach((name) => {
                    granted[name] = true;
                });

                return normalizeCategories(granted);
            };

            const requiredOnlyCategories = () => normalizeCategories({});

            const syncInputs = (consent = null) => {
                const values = consent ? asObject(consent.categories) : defaultCategories();

                categoryInputs.forEach((input) => {
                    const required = input.getAttribute('data-required') === 'true';
                    input.checked = required || values[input.value] === true;
                });
            };

            const showRoot = () => {
                root.hidden = false;
            };

            const showBanner = () => {
                showRoot();

                if (banner) {
                    banner.hidden = false;
                }

                if (reopenButton) {
                    reopenButton.hidden = true;
                }

                if (firstAction) {
                    window.setTimeout(() => firstAction.focus({preventScroll: true}), 80);
                }
            };

            const hideBanner = () => {
                if (banner) {
                    banner.hidden = true;
                }
            };

            const showReopen = () => {
                if (!reopenButton || config.showReopenButton === false) {
                    if (!modal || modal.hidden) {
                        root.hidden = true;
                    }

                    return;
                }

                showRoot();
                reopenButton.hidden = false;
            };

            const openModal = () => {
                showRoot();
                hideBanner();
                syncInputs(readStoredConsent());

                if (modal) {
                    modal.hidden = false;
                    root.classList.add('lp-cookie-consent-modal-open');

                    const firstInput = modal.querySelector('[data-cookie-category-input]:not(:disabled)');
                    const focusTarget = firstInput || modal.querySelector('[data-cookie-save-preferences]');

                    if (focusTarget) {
                        window.setTimeout(() => focusTarget.focus({preventScroll: true}), 50);
                    }
                }
            };

            const closeModal = () => {
                if (modal) {
                    modal.hidden = true;
                    root.classList.remove('lp-cookie-consent-modal-open');
                }

                if (readStoredConsent()) {
                    showReopen();
                } else {
                    showBanner();
                }
            };

            const hasConsent = (category, consent = null) => {
                if (requiredNames.has(category)) {
                    return true;
                }

                const stored = consent || readStoredConsent();

                return asObject(asObject(stored).categories)[category] === true;
            };

            const syncConsentMode = (consent) => {
                if (typeof window.gtag !== 'function') {
                    return;
                }

                window.gtag('consent', 'update', {
                    analytics_storage: hasConsent('analytics', consent) ? 'granted' : 'denied',
                    ad_storage: hasConsent('marketing', consent) ? 'granted' : 'denied',
                    ad_user_data: hasConsent('marketing', consent) ? 'granted' : 'denied',
                    ad_personalization: hasConsent('marketing', consent) ? 'granted' : 'denied',
                    personalization_storage: hasConsent('personalization', consent) ? 'granted' : 'denied',
                });
            };

            const activateBlockedScripts = () => {
                const selector = config.scriptSelector || 'script[type="text/plain"][data-landing-cookie-category], script[type="text/plain"][data-cookie-category]';
                let scripts = [];

                try {
                    scripts = Array.from(document.querySelectorAll(selector));
                } catch (error) {
                    scripts = [];
                }

                scripts.forEach((placeholder) => {
                    if (placeholder.getAttribute('data-landing-cookie-loaded') === 'true') {
                        return;
                    }

                    const category = placeholder.getAttribute('data-landing-cookie-category')
                        || placeholder.getAttribute('data-cookie-category');

                    if (!category || !hasConsent(category)) {
                        return;
                    }

                    const script = document.createElement('script');

                    Array.from(placeholder.attributes).forEach((attribute) => {
                        if ([
                            'type',
                            'data-src',
                            'data-cookie-category',
                            'data-landing-cookie-category',
                            'data-landing-cookie-loaded',
                        ].includes(attribute.name)) {
                            return;
                        }

                        script.setAttribute(attribute.name, attribute.value);
                    });

                    const source = placeholder.getAttribute('data-src');

                    if (source) {
                        script.src = source;
                    }

                    if (placeholder.textContent.trim() !== '') {
                        script.text = placeholder.textContent;
                    }

                    placeholder.setAttribute('data-landing-cookie-loaded', 'true');
                    placeholder.after(script);
                });
            };

            const recordConsent = (payload) => {
                const logging = asObject(config.logging);

                if (!logging.enabled || !logging.endpoint || typeof window.fetch !== 'function') {
                    return;
                }

                window.fetch(logging.endpoint, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        ...(logging.csrfToken ? {'X-CSRF-TOKEN': logging.csrfToken} : {}),
                    },
                    body: JSON.stringify({
                        consent_id: payload.id,
                        version: payload.version,
                        action: payload.action,
                        categories: payload.categories,
                        policy_url: config.policyUrl,
                        url: window.location.href,
                        accepted_at: payload.acceptedAt,
                        expires_at: payload.expiresAt,
                    }),
                }).catch(() => {});
            };

            const saveConsent = (values, action) => {
                const payload = buildConsent(values, action);

                if (!storeConsent(payload)) {
                    return;
                }

                hideBanner();

                if (modal) {
                    modal.hidden = true;
                    root.classList.remove('lp-cookie-consent-modal-open');
                }

                syncConsentMode(payload);
                activateBlockedScripts();
                showReopen();
                recordConsent(payload);

                window.dispatchEvent(new CustomEvent('landing:consent-updated', {detail: payload}));
            };

            const selectedCategories = () => {
                const selected = {};

                categoryInputs.forEach((input) => {
                    selected[input.value] = input.checked;
                });

                return normalizeCategories(selected);
            };

            root.addEventListener('click', (event) => {
                const target = event.target instanceof Element ? event.target : event.target.parentElement;

                if (!target) {
                    return;
                }

                if (target.closest('[data-cookie-accept-all]')) {
                    saveConsent(allCategories(), 'accept_all');
                }

                if (target.closest('[data-cookie-reject-optional]')) {
                    saveConsent(requiredOnlyCategories(), 'reject_optional');
                }

                if (target.closest('[data-cookie-save-preferences]')) {
                    saveConsent(selectedCategories(), 'save_preferences');
                }

                if (target.closest('[data-cookie-open-preferences], [data-cookie-reopen]')) {
                    openModal();
                }

                if (target.closest('[data-cookie-close-preferences]')) {
                    closeModal();
                }

                if (target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal && !modal.hidden) {
                    closeModal();
                }
            });

            window.landingCookieConsent = {
                ...(window.landingCookieConsent || {}),
                config,
                read: readStoredConsent,
                open: openModal,
                acceptAll: () => saveConsent(allCategories(), 'accept_all'),
                rejectOptional: () => saveConsent(requiredOnlyCategories(), 'reject_optional'),
                hasConsent,
            };

            const storedConsent = readStoredConsent();

            if (storedConsent) {
                syncInputs(storedConsent);
                syncConsentMode(storedConsent);
                activateBlockedScripts();
                showReopen();
            } else {
                syncInputs();
                showBanner();
            }
        })();
    </script>
@endonce
