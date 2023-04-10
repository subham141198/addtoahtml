jQuery(document).ready(function () {
  tinymce.init({
    selector: "#myTextarea",
  });

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
      console.log(data);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log("Error: " + errorThrown);
    },
  });
});
