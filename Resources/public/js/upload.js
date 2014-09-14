function removeParentTr(id){
    $('#'+id).parents("tr").eq(0).remove();
}
$('#upload-menu a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})

$(document).ready(function() {
    
    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: '/upload',
        done: function (e, data) {
            if (data.context){
                data.context.html(data.result.files[0].response);
            }
        }
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload('option','redirect', window.location.href.replace(/\/[^\/]*$/,'/cors/result.html?%s'));

    // Load existing files:
    $('#fileupload').addClass('fileupload-processing');
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#fileupload')[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, $.Event('done'), {result: result});
    });

    $('#fileupload').bind('fileuploadsubmit', function (e, data) {
        var inputs = data.context.find(':input');
        if (inputs.filter(function () {
                return !this.value && $(this).prop('required');
            }).first().focus().length) {
            data.context.find('button').prop('disabled', false);
            return false;
        }
        data.formData = inputs.serializeArray();
    });
});

