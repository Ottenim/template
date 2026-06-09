@once('landing-analytics-head')
    <script id="landing-analytics-config" type="application/json">{!! $configJson !!}</script>
    <script id="landing-analytics-loader">
        (() => {
            'use strict';

            const configElement = document.getElementById('landing-analytics-config');

            if (!configElement) {
                return;
            }

            const config = JSON.parse(configElement.textContent || '{}');

            if (!config.enabled) {
                return;
            }

            const layerName = config.dataLayer || 'dataLayer';
            const dataLayer = window[layerName] = window[layerName] || [];
            const loadedProviders = {};
            const trackedDepths = new Set();

            const asObject = (value) => value && typeof value === 'object' && !Array.isArray(value) ? value : {};
            const enabledEvents = asObject(config.events);
            const providers = asObject(config.providers);

            const eventEnabled = (eventName) => enabledEvents[eventName] !== false;

            const readConsent = () => {
                const consent = asObject(config.consent);

                if (!consent.enabled) {
                    return true;
                }

                try {
                    const stored = window.localStorage.getItem(consent.storageKey || 'landing_cookie_consent');

                    if (!stored) {
                        return !!consent.defaultGranted;
                    }

                    if (stored === 'all' || stored === 'accepted' || stored === 'true') {
                        return true;
                    }

                    if (stored === 'none' || stored === 'rejected' || stored === 'false') {
                        return false;
                    }

                    return JSON.parse(stored);
                } catch (error) {
                    return !!consent.defaultGranted;
                }
            };

            const hasConsent = (category) => {
                const consent = asObject(config.consent);

                if (!consent.enabled) {
                    return true;
                }

                const stored = readConsent();

                if (stored === true || stored === false) {
                    return stored;
                }

                const categories = asObject(stored.categories || stored);
                const categoryName = asObject(consent.categories)[category] || category;

                return categories[categoryName] === true || categories[category] === true;
            };

            const loadScript = (id, src, attributes = {}) => {
                if (loadedProviders[id] || document.getElementById(id)) {
                    loadedProviders[id] = true;
                    return;
                }

                const script = document.createElement('script');
                script.id = id;
                script.async = true;
                script.src = src;

                Object.keys(attributes).forEach((name) => {
                    script.setAttribute(name, attributes[name]);
                });

                const firstScript = document.getElementsByTagName('script')[0];
                firstScript.parentNode.insertBefore(script, firstScript);
                loadedProviders[id] = true;
            };

            const loadGtm = (provider) => {
                dataLayer.push({'gtm.start': Date.now(), event: 'gtm.js'});
                loadScript('landing-analytics-gtm', 'https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(provider.id));
            };

            const loadGa4 = (provider) => {
                window.gtag = window.gtag || function () {
                    dataLayer.push(arguments);
                };

                window.gtag('js', new Date());
                window.gtag('config', provider.id, {
                    send_page_view: provider.send_page_view === true,
                });

                loadScript('landing-analytics-ga4', 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(provider.id));
            };

            const loadMetaPixel = (provider) => {
                if (!window.fbq) {
                    const fbq = window.fbq = function () {
                        fbq.callMethod ? fbq.callMethod.apply(fbq, arguments) : fbq.queue.push(arguments);
                    };

                    fbq.push = fbq;
                    fbq.loaded = true;
                    fbq.version = '2.0';
                    fbq.queue = [];
                }

                window.fbq('init', provider.id);
                loadScript('landing-analytics-meta-pixel', 'https://connect.facebook.net/en_US/fbevents.js');
            };

            const loadTiktokPixel = (provider) => {
                const ttq = window.ttq = window.ttq || [];

                if (!ttq._landingInitialized) {
                    ttq.methods = ['page', 'track', 'identify', 'instances', 'debug', 'on', 'off', 'once', 'ready', 'alias', 'group', 'enableCookie', 'disableCookie'];
                    ttq.setAndDefer = function (target, method) {
                        target[method] = function () {
                            target.push([method].concat(Array.prototype.slice.call(arguments, 0)));
                        };
                    };

                    for (let index = 0; index < ttq.methods.length; index += 1) {
                        ttq.setAndDefer(ttq, ttq.methods[index]);
                    }

                    ttq.load = function (pixelId) {
                        loadScript('landing-analytics-tiktok-pixel', 'https://analytics.tiktok.com/i18n/pixel/events.js?sdkid=' + encodeURIComponent(pixelId) + '&lib=ttq');
                    };

                    ttq._landingInitialized = true;
                }

                ttq.load(provider.id);
            };

            const loadLinkedinInsight = (provider) => {
                window._linkedin_partner_id = provider.id;
                window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];

                if (!window._linkedin_data_partner_ids.includes(provider.id)) {
                    window._linkedin_data_partner_ids.push(provider.id);
                }

                loadScript('landing-analytics-linkedin-insight', 'https://snap.licdn.com/li.lms-analytics/insight.min.js');
            };

            const loadProvider = (provider) => {
                if (!provider || !provider.id || !hasConsent(provider.category || 'analytics')) {
                    return;
                }

                if (provider.name === 'gtm') {
                    loadGtm(provider);
                }

                if (provider.name === 'ga4') {
                    loadGa4(provider);
                }

                if (provider.name === 'meta_pixel') {
                    loadMetaPixel(provider);
                }

                if (provider.name === 'tiktok_pixel') {
                    loadTiktokPixel(provider);
                }

                if (provider.name === 'linkedin_insight') {
                    loadLinkedinInsight(provider);
                }
            };

            const loadProviders = () => {
                Object.keys(providers).forEach((name) => loadProvider(providers[name]));
            };

            const normalizePayload = (detail) => {
                const payload = asObject(detail);
                const eventName = String(payload.event || payload.name || '').trim();

                if (!eventName || !eventEnabled(eventName)) {
                    return null;
                }

                return {
                    ...payload,
                    event: eventName,
                    page_path: payload.page_path || window.location.pathname,
                    page_url: payload.page_url || window.location.href,
                };
            };

            const eventParameters = (payload) => {
                const parameters = {...payload};
                delete parameters.event;
                return parameters;
            };

            const debugEvent = (payload) => {
                if (!config.debug) {
                    return;
                }

                window.console.info('[landing-analytics]', payload);

                const list = document.querySelector('[data-landing-analytics-events]');

                if (!list) {
                    return;
                }

                const item = document.createElement('li');
                const time = new Date().toLocaleTimeString();
                item.textContent = time + ' - ' + payload.event;
                list.prepend(item);
            };

            const track = (detail) => {
                const payload = normalizePayload(detail);

                if (!payload) {
                    return false;
                }

                const parameters = eventParameters(payload);

                dataLayer.push(payload);

                if (typeof window.gtag === 'function') {
                    window.gtag('event', payload.event, parameters);
                }

                if (typeof window.fbq === 'function') {
                    if (payload.event === 'page_view') {
                        window.fbq('track', 'PageView', parameters);
                    } else {
                        window.fbq('trackCustom', payload.event, parameters);
                    }
                }

                if (window.ttq && typeof window.ttq.track === 'function') {
                    if (payload.event === 'page_view' && typeof window.ttq.page === 'function') {
                        window.ttq.page();
                    } else {
                        window.ttq.track(payload.event, parameters);
                    }
                }

                const linkedinConversions = asObject(asObject(providers.linkedin_insight).conversion_ids);

                if (linkedinConversions[payload.event] && typeof window.lintrk === 'function') {
                    window.lintrk('track', {conversion_id: linkedinConversions[payload.event]});
                }

                debugEvent(payload);
                window.dispatchEvent(new CustomEvent('landing:tracked', {detail: payload}));

                return true;
            };

            const textLabel = (element) => (element.getAttribute('data-label')
                || element.getAttribute('aria-label')
                || element.textContent
                || ''
            ).trim().replace(/\s+/g, ' ').slice(0, 120);

            const setupClickTracking = () => {
                if (!asObject(config.autoTrack).clicks) {
                    return;
                }

                document.addEventListener('click', (event) => {
                    const target = event.target instanceof Element ? event.target : event.target.parentElement;

                    if (!target) {
                        return;
                    }

                    const selectors = asObject(config.selectors).clicks || [];

                    for (const contract of selectors) {
                        const element = target.closest(contract.selector);

                        if (!element) {
                            continue;
                        }

                        const eventName = element.getAttribute(contract.attribute);

                        if (!eventName) {
                            continue;
                        }

                        track({
                            event: eventName,
                            module: element.getAttribute('data-module') || element.getAttribute('data-landing-module') || contract.module || null,
                            label: textLabel(element),
                            href: element.getAttribute('href'),
                            pricing_plan: element.getAttribute('data-pricing-plan'),
                            pricing_plan_id: element.getAttribute('data-pricing-plan-id'),
                        });

                        break;
                    }
                }, true);
            };

            const setupFormTracking = () => {
                if (!asObject(config.autoTrack).forms) {
                    return;
                }

                document.addEventListener('submit', (event) => {
                    const form = event.target;
                    const selectors = asObject(config.selectors).forms || [];

                    for (const contract of selectors) {
                        if (!form.matches(contract.selector)) {
                            continue;
                        }

                        const eventName = form.getAttribute(contract.attribute);

                        if (!eventName) {
                            continue;
                        }

                        track({
                            event: eventName,
                            module: contract.module || form.getAttribute('data-module') || null,
                            form_id: form.getAttribute('id'),
                            action: form.getAttribute('action'),
                        });

                        break;
                    }
                }, true);
            };

            const setupScrollDepthTracking = () => {
                const scrollDepth = asObject(asObject(config.autoTrack).scrollDepth);

                if (!scrollDepth.enabled || !Array.isArray(scrollDepth.percentages) || scrollDepth.percentages.length === 0) {
                    return;
                }

                let ticking = false;

                const handleScroll = () => {
                    const scrollTop = window.scrollY || document.documentElement.scrollTop || 0;
                    const documentHeight = Math.max(
                        document.body.scrollHeight,
                        document.documentElement.scrollHeight,
                    ) - window.innerHeight;
                    const percent = documentHeight <= 0 ? 100 : Math.round((scrollTop / documentHeight) * 100);

                    scrollDepth.percentages.forEach((depth) => {
                        if (percent >= depth && !trackedDepths.has(depth)) {
                            trackedDepths.add(depth);
                            track({
                                event: scrollDepth.event || 'scroll_depth',
                                depth,
                            });
                        }
                    });

                    ticking = false;
                };

                document.addEventListener('scroll', () => {
                    if (!ticking) {
                        window.requestAnimationFrame(handleScroll);
                        ticking = true;
                    }
                }, {passive: true});

                handleScroll();
            };

            window.landingAnalytics = {
                ...(window.landingAnalytics || {}),
                config,
                loadProviders,
                track,
            };

            window.addEventListener('landing:track', (event) => track(event.detail));
            window.addEventListener('landing:consent-updated', loadProviders);
            window.addEventListener('storage', (event) => {
                if (event.key === asObject(config.consent).storageKey) {
                    loadProviders();
                }
            });

            loadProviders();
            setupClickTracking();
            setupFormTracking();
            setupScrollDepthTracking();

            if (eventEnabled('page_view')) {
                track({
                    event: 'page_view',
                    title: document.title,
                    referrer: document.referrer || null,
                });
            }
        })();
    </script>
@endonce
