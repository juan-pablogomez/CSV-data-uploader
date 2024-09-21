jQuery(document).ready(() => {
  const formElement = document.querySelector("#form-csv-upload")
  jQuery("#form-csv-upload").on("submit", (event) => {
    event.preventDefault();
    var formData = new FormData(formElement);

  jQuery.ajax({
      url: cdu_object.ajax_url,
      data: formData,
      dataType: "json",
      method: "POST",
      processData: false,
      contentType: false,
      success: (response) => {
        console.log(response);
        if(response.status) {
          jQuery("#show-upload-message").text(response.message).css({
            color: 'green'
          })
          jQuery("#form-csv-upload")[0].reset()
        }
      }
    });
  });
});