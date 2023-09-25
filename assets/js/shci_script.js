jQuery(document).ready(function () {
  var page_url = window.location.href;
  var form_data = new FormData();
  form_data.append("action", "shci_fetch_post_data");
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
      jQuery.each(data.shci_html_data, function (shci_data_index, shci_data) {
        Selector = shci_data.shci_selector_type == "class" ? "." + shci_data.shci_selector_name : "#" + shci_data.shci_selector_name;
        if (shci_data.shci_target_location == "after") {
          //check for locations After
          jQuery(Selector).after(shci_data.shci_target_html);
        }
        if (shci_data.shci_target_location == "before") {
          //check for locations before
          jQuery(Selector).before(shci_data.shci_target_html);
        }
        if (shci_data.shci_target_location == "innerhtml") {
          //check for locations innerhtml
          jQuery(Selector).append(shci_data.shci_target_html);
        }
      });
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) { },
  });
});
