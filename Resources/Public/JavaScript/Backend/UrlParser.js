define(
  ['jquery', 'TYPO3/CMS/Backend/Notification'],
  function(jQuery, Notification) {
    var successMessages = [];

    function translate(key) {
      return TYPO3.lang['tx_mediaoembed_' + key];
    }

    function buildFormFieldSelector(fieldName) {
      var selectors = [
        '[type="text"]',
        '[data-formengine-input-name^="data[tt_content]"]',
        '[data-formengine-input-name$="[' + fieldName + ']"]'
      ];
      return 'input' + selectors.join('')
    }

    function handleError(error) {
      Notification.warning(translate(error));
    }

    function handleUrlInputBlur(urlInput) {
      if (!urlInput.val().includes('<iframe')) {
        return;
      }

      var html = jQuery(urlInput.val());
      var iframe = html.filter('iframe').add(html.find('iframe'));
      if (!iframe.length) {
        handleError('error_iframe_extraction_failed')
        return;
      }

      if (iframe.length > 1) {
        handleError('error_more_than_one_iframe_found')
        return;
      }

      var iframeUrl = iframe.attr('src')
      if (!iframeUrl) {
        handleError('error_iframe_has_no_src')
        return;
      }

      urlInput.val(iframeUrl);
      successMessages.push(translate('success_iframe_src_extracted'));

      var width = parseInt(iframe.attr('width'), 10);
      var height = parseInt(iframe.attr('height'), 10);
      if (!(width && height)) {
        return;
      }

      var form = urlInput.closest('.typo3-TCEforms');
      var aspectRatioInput = form.find(buildFormFieldSelector('tx_mediaoembed_aspect_ratio'));
      aspectRatioInput.val(width + ':' + height);
      successMessages.push(translate('success_iframe_aspect_ratio_extracted'));
    }

    function initContentForm(urlInput) {
      urlInput.blur(
        function() {
          handleUrlInputBlur(urlInput)
          if (!successMessages.length) {
            return;
          }
          Notification.success(successMessages.join('\n'));
          successMessages = [];
        }
      );
    }

    jQuery(function() {
      jQuery(buildFormFieldSelector('tx_mediaoembed_url')).each(function() {
        initContentForm($(this));
      });
    });
  }
);
