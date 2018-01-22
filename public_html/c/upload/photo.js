$(document).ready(function() {
    $("#upload").fileinput({
        theme: "fa",
        uploadUrl: "/c/upload/aj-save.php",
        maxFileCount: 16,
        maxFileSize: 2048
    });
});
