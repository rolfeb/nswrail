$(document).ready(function() {
    $("#upload").fileinput({
        theme: "fa",
        uploadUrl: "/c/upload/aj-save.php",
        maxFileCount: 16,
        maxFileSize: 2048,
    });

    $('#upload').on('fileuploaded', function(event, data, previewId, index) {
        var form = data.form, files = data.files, extra = data.extra,
            response = data.response, reader = data.reader;

        // append the new entry to the queue
        document.getElementById('photo-queue').insertAdjacentHTML('beforeend', response.queueEntry);
    });

});
