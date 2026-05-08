/**
 * mediaoembed Consent Handler
 * Handles user consent for embedded media content.
 */
(function() {
    'use strict';

    if (typeof window.mediaoembedConsentHandler !== 'undefined') {
        return;
    }

    const getConsentHandler = () => ({
        initialized: false,

        init(config = {}) {
            if (this.initialized) {
                return;
            }

            this.config = config;
            this.initialized = true;

            const containers = document.querySelectorAll('[data-oembed-html]');

            containers.forEach((container) => {
                this.setupConsentContainer(container);
            });
        },

        setupConsentContainer(container) {
            const acceptButton = container.querySelector('.tx-mediaoembed-consent__accept');
            const encodedHtml = container.getAttribute('data-oembed-html');

            if (!acceptButton || !encodedHtml) {
                return;
            }

            acceptButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.loadEmbedContent(container, encodedHtml);
            });

            // Setup accessibility for preview details
            this.setupPreviewAccessibility(container);
        },

        setupPreviewAccessibility(container) {
            const details = container.querySelector('.tx-mediaoembed-consent__preview');
            if (!details) {
                return;
            }

            const summary = details.querySelector('summary');
            if (!summary) {
                return;
            }

            const content = details.querySelector('[id^="tx-mediaoembed-preview-content-"]');

            // Update aria-expanded based on details state
            const updateAriaExpanded = () => {
                summary.setAttribute('aria-expanded', details.hasAttribute('open') ? 'true' : 'false');
            };

            // Scroll to content when expanded
            const handleToggle = () => {
                updateAriaExpanded();
                if (details.hasAttribute('open') && content) {
                    content.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            };

            // Initialize
            updateAriaExpanded();

            // Update on toggle event
            details.addEventListener('toggle', handleToggle);
        },

        loadEmbedContent(container, encodedHtml) {
            const inner = container.querySelector('.tx-mediaoembed-consent__inner');
            inner.outerHTML = atob(encodedHtml);
        }
    });

    window.mediaoembedConsentHandler = getConsentHandler();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.mediaoembedConsentHandler.init();
        });
    } else {
        window.mediaoembedConsentHandler.init();
    }
})();
