/**
 * PDF Settings
 * Dependancies: jQuery, gfpdf_current_pdf, GFPDF
 * @since 1.0
 */

(function ($) {

  /**
   * @var string The current saved template
   * @since 1.0
   */
  var currentTemplate

  $(function () {
    if (typeof gfpdf_current_pdf === 'object') {
      var isToolkit = gfpdf_current_pdf.toolkit || false
      showOrHideAppearanceSettings(isToolkit)

      /* Gravity PDF JS unbinds change events on actual selector. As events bubble, we'll lisen on the parent element */
      var $select = $('#gfpdf_settings\\[template\\]')
      currentTemplate = $select.val()
      $select.parent().change(queryTemplateHeader)
    }
  })

  /**
   * Trigger out AJAX request
   *
   * @since 1.0
   */
  function queryTemplateHeader (event) {

    var value = $(this).find('select').val()

    if (currentTemplate !== value) {
      currentTemplate = value

      var data = {
        'action': 'gfpdf_get_template_headers',
        'nonce': GFPDF.ajaxNonce,
        'template': $(this).find('select').val(),
      }

      ajax(data, function (response) {
        var isToolkit = response.toolkit || false
        showOrHideAppearanceSettings(isToolkit)
      })
    }
  }

  /**
   * Show or hide the appearance settings based on the status of Toolkit
   *
   * @param bool isToolkit
   *
   * @since 1.0
   */
  function showOrHideAppearanceSettings (isToolkit) {
    var $rows = $('#pdf-general-appearance').find('tr:nth-child(1), tr:nth-child(2), tr:nth-child(3), tr:nth-child(7)')

    if (!isToolkit) {
      $rows.show()
      $('#gfpdf_settings\\[pdf_size\\]').trigger('change')
    } else {
      $rows.hide()
    }
  }

  /**
   * Make an AJAX Request and run successCallback on success, or ajaxError on failure
   *
   * @param object post
   * @param object successCallback
   *
   * @since 1.0
   */
  function ajax (post, successCallback) {
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: GFPDF.ajaxUrl,
      data: post,
      success: successCallback,
      error: ajaxError,
    })
  }

  /**
   * Add AJAX response to console for debugging
   *
   * @param jqXHR
   * @param textStatus
   * @param errorThrown
   *
   * @since 1.0
   */
  function ajaxError (jqXHR, textStatus, errorThrown) {
    console.log(textStatus)
    console.log(errorThrown)
  }

})(jQuery)