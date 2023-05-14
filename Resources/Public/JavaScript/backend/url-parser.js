'use strict';

// noinspection NpmUsedModulesInstalled,JSFileReferences
import FormEngineValidation from '@typo3/backend/form-engine-validation.js';
// noinspection NpmUsedModulesInstalled,JSFileReferences
import Notification from '@typo3/backend/notification.js';

class UrlParser {
  constructor(wrapperId) {
    this.successMessages = [];

    const formElementWrapper = document.getElementById(wrapperId);

    if (!formElementWrapper) {
      throw 'Could not find form element wrapper with ID "' + wrapperId + '"';
    }

    const formFieldSelector = this.buildFormFieldSelector('tx_mediaoembed_url');

    const formField = formElementWrapper.querySelector(formFieldSelector);

    this.initUrlInput(formField);
  }

  buildFormFieldSelector(fieldName) {
    const selectors = [
      '[type="text"]',
      '[data-formengine-input-name^="data[tt_content]"]',
      '[data-formengine-input-name$="[' + fieldName + ']"]'
    ];
    return 'input' + selectors.join('')
  }

  /**
   * @param {HTMLInputElement} urlInput
   */
  handleUrlInputBlur(urlInput) {
    if (!urlInput.value.includes('<iframe')) {
      return;
    }

    const html = this.parseHtml(urlInput.value);
    const iframes = html.getElementsByTagName('iframe');
    if (!iframes.length) {
      this.handleError('error_iframe_extraction_failed')
      return;
    }

    if (iframes.length > 1) {
      this.handleError('error_more_than_one_iframe_found')
      return;
    }

    const iframe = iframes[0];

    const iframeUrl = iframe.getAttribute('src');
    if (!iframeUrl) {
      this.handleError('error_iframe_has_no_src')
      return;
    }

    this.setFormFieldValue(urlInput, iframeUrl)
    this.successMessages.push(this.translate('success_iframe_src_extracted'));

    const width = parseInt(iframe.getAttribute('width'), 10);
    const height = parseInt(iframe.getAttribute('height'), 10);
    if (!(width && height)) {
      return;
    }

    const form = urlInput.closest('.typo3-TCEforms');
    const aspectRatioInput = form.querySelector(this.buildFormFieldSelector('tx_mediaoembed_aspect_ratio'));
    this.setFormFieldValue(aspectRatioInput, width + ':' + height);
    this.successMessages.push(this.translate('success_iframe_aspect_ratio_extracted'));
  }

  handleError(error) {
    Notification.warning(this.translate(error));
  }

  initUrlInput(urlInput) {
    const that = this;
    urlInput.addEventListener(
      'blur',
      () => {
        that.handleUrlInputBlur(urlInput)
        if (!that.successMessages.length) {
          return;
        }
        Notification.success(that.successMessages.join('\n'));
        that.successMessages = [];
      }
    )
  }

  /**
   * @param {HTMLInputElement} field
   * @param {string} value
   */
  setFormFieldValue(field, value) {
    field.value = value;

    const hiddenElement = field.closest('.t3js-formengine-field-item')
      .querySelector('input[type="hidden"]');

    hiddenElement.value = value;

    // We need to trigger the "change" event for the TYPO3 form engine to update the related hidden field.
    FormEngineValidation.validateField(field);
    FormEngineValidation.markFieldAsChanged(field);
  }

  translate(key) {
    return TYPO3.lang['tx_mediaoembed_' + key];
  }

  parseHtml(value) {
    const el = document.createElement('html');
    // noinspection HtmlRequiredLangAttribute
    el.innerHTML = "<html><head><title>titleTest</title></head><body>" + value + "</body></html>";
    return el;
  }
}

export default UrlParser;
