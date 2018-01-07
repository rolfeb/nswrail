$(function() {
    $("#login-form").dialog("destroy");

    $("#login-form").dialog({
        autoOpen: false,
        modal: true,
        width: 300,
        buttons: {
            "Login": function() {
                $(this).dialog("close");
                form = this.getElementsByTagName("form")[0];
                form.submit();
            },
            "Cancel": function() {
                $(this).dialog("close");
            },
        }
    });


    $("#login").click(function() {
            $("#login-form").dialog("open");
        });
});

