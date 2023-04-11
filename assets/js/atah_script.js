jQuery(document).ready(function () {


  var page_url = window.location.href;
  console.log(page_url);
  var form_data = new FormData();
  form_data.append("action", "atah_fetch_post_data");
  form_data.append("page_url", page_url);
  jQuery.ajax({
    url: wpAjax.ajaxurl,
    type: "POST",
    dataType: "JSON",
    contentType: false,
    processData: false,
    data: form_data,
    success: function (data) {
      jQuery.each(data.atah_html_data, function (indexInArray, valueOfElement) {
        if (jQuery(valueOfElement.atah_selector_name)[0]) {
          if (valueOfElement.atah_target_location == 'after') {
            jQuery(valueOfElement.atah_selector_name).after(valueOfElement.atah_target_html);
          }
          if (valueOfElement.atah_target_location == 'before') {
            jQuery(valueOfElement.atah_selector_name).before(valueOfElement.atah_target_html);
          }
          if (valueOfElement.atah_target_location == 'innerhtml') {
            jQuery(valueOfElement.atah_selector_name).html(valueOfElement.atah_target_html);
          }
        } else {
          jQuery('body').html(valueOfElement.atah_target_html);

        }
      });


    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log("Error: " + errorThrown);
    },
  });
});
