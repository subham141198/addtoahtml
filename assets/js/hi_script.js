jQuery(document).ready(function () {
  var page_url = window.location.href;
  var form_data = new FormData();
  form_data.append("action", "hi_fetch_post_data");
  form_data.append("page_url", page_url);
  jQuery.ajax({
    //ajax call happens here
    url: wpAjax.ajaxurl,
    type: "POST",
    dataType: "JSON",
    contentType: false,
    processData: false,
    data: form_data,
    success: function (data) {
      jQuery.each(data.hi_html_data, function (hi_data_index, hi_data) {
        if (jQuery(hi_data.hi_selector_name)[0]) {
          //check if selector exists
          if (hi_data.hi_target_location == "after") {
            //check for locations After
            jQuery(hi_data.hi_selector_name).after(hi_data.hi_target_html);
          }
          if (hi_data.hi_target_location == "before") {
            //check for locations before
            jQuery(hi_data.hi_selector_name).before(hi_data.hi_target_html);
          }
          if (hi_data.hi_target_location == "innerhtml") {
            //check for locations innerhtml
            jQuery(hi_data.hi_selector_name).append(hi_data.hi_target_html);
          }
        }
      });
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {},
  });
});
