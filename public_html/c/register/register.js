/*
 * Copyright (c) 2018. Rolfe Bozier
 */

function validate_register_form()
{
    let email = document.getElementById("register_username").value;
    let fullname = document.getElementById("register_fullname").value;
    let password1 = document.getElementById("register_password1").value;
    let password2 = document.getElementById("register_password2").value;

    let error = null;
    if (email.length < 5) {
        error = "ERROR: email address is too short";
    } else if (email.indexOf("@") < 0) {
        error = "ERROR: email address contains no '@'";
    } else if (fullname.length < 5) {
        error = "ERROR: full name is too short";
    } else if (password1.length < 6) {
        error = "ERROR: password is too short";
    } else if (password1 !== password2) {
        error = "ERROR: passwords do not match";
    }
    if (error) {
        document.getElementById("register_error").innerHTML = error;
        return false;
    }

    return true;
}
