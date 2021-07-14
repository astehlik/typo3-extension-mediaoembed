define(
  ['jquery', 'TYPO3/CMS/Backend/Notification'],
  function(jQuery, Notification) {
    function UrlParser(wrapperId) {
      this.successMessages = [];

      var that = this;
      jQuery(function() {
        jQuery('#' + wrapperId)
          .find(that.buildFormFieldSelector('tx_mediaoembed_url'))
          .each(function() {
            that.initUrlInput(jQuery(this));
          });
      });
    }

    UrlParser.prototype.buildFormFieldSelector = function(fieldName) {
      var selectors = [
        '[type="text"]',
        '[data-formengine-input-name^="data[tt_content]"]',
        '[data-formengine-input-name$="[' + fieldName + ']"]'
      ];
      return 'input' + selectors.join('')
    };

    UrlParser.prototype.handleUrlInputBlur = function(urlInput) {
      if (!urlInput.val().includes('<iframe')) {
        return;
      }

      var html = jQuery(urlInput.val());
      var iframe = html.filter('iframe').add(html.find('iframe'));
      if (!iframe.length) {
        this.handleError('error_iframe_extraction_failed')
        return;
      }

      if (iframe.length > 1) {
        this.handleError('error_more_than_one_iframe_found')
        return;
      }

      var iframeUrl = iframe.attr('src')
      if (!iframeUrl) {
        this.handleError('error_iframe_has_no_src')
        return;
      }

      this.setFormFieldValue(urlInput, iframeUrl)
      this.successMessages.push(this.translate('success_iframe_src_extracted'));

      var width = parseInt(iframe.attr('width'), 10);
      var height = parseInt(iframe.attr('height'), 10);
      if (!(width && height)) {
        return;
      }

      var form = urlInput.closest('.typo3-TCEforms');
      var aspectRatioInput = form.find(this.buildFormFieldSelector('tx_mediaoembed_aspect_ratio'));
      this.setFormFieldValue(aspectRatioInput, width + ':' + height);
      this.successMessages.push(this.translate('success_iframe_aspect_ratio_extracted'));
    };

    UrlParser.prototype.handleError = function(error) {
      Notification.warning(this.translate(error));
    };

    UrlParser.prototype.initUrlInput = function(urlInput) {
      var that = this;
      urlInput.blur(
        function() {
          that.handleUrlInputBlur(urlInput)
          if (!that.successMessages.length) {
            return;
          }
          Notification.success(that.successMessages.join('\n'));
          that.successMessages = [];
        }
      )
    };

    UrlParser.prototype.setFormFieldValue = function(field, value) {
      field.val(value);
      // We need to trigger the "change" event for the TYPO3 form engine to update the related hidden field.
      field.change();
    };

    UrlParser.prototype.translate = function(key) {
      return TYPO3.lang['tx_mediaoembed_' + key];
    }

    return UrlParser;
  }
);
