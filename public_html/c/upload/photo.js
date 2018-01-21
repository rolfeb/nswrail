$(document).ready(function() {
    $("#upload").fileinput({
        theme: "fa",
        uploadUrl: "/c/upload/save.php",
        maxFileCount: 5,
        maxFileSize: 2048
    });
});
