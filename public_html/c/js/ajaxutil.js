/*
 * Copyright (c) 2018. Rolfe Bozier
 */

/*
 * Get an XMLHttpRequest object in a portable way.
 */
function aj_new_request()
{
    let req = false;

    // For Safari, Firefox, and other non-MS browsers
    if (window.XMLHttpRequest)
    {
        try {
            req = new XMLHttpRequest();
        } catch (e) {
            req = false;
        }
    } else if (window.ActiveXObject) {
        // For Internet Explorer on Windows
        try {
          req = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                req = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                req = false;
            }
        }
    }

    return req;
}
