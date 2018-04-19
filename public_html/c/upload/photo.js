/*
 * Copyright (c) 2018. Rolfe Bozier
 */

$(document).ready(function() {
    $("#upload").fileinput({
        theme: "fa",
        uploadUrl: "/c/upload/aj-upload.php",
        allowedFileTypes: ['image'],
        showDrag: false,
        maxFileCount: 16,
        maxFileSize: 2048,
        minImageHeight: 400,
        minImageWidth: 400,
        maxImageHeight: 1000,
        maxImageWidth: 1000,
    });

    $('#upload').on('fileuploaded', function(event, data, previewId, index) {
        let form = data.form, files = data.files, extra = data.extra,
            response = data.response, reader = data.reader;

        // append the new entry to the queue
        document.getElementById('photo-queue').insertAdjacentHTML('beforeend', response.queueEntry);
    });
});

function isNumeric(s)
{
    return !isNaN(s) && isFinite(s);
}

function daysInMonth(m, y)
{
    switch (parseInt(m)) {
    case 2:
        return (y % 4 === 0 && y % 100) || y % 400 === 0 ? 29 : 28;
    case 9: case 4 : case 6 : case 11 :
        return 30;
    default:
        return 31
    }
}

function isValidDate(d, m, y)
{
    return m >= 1 && m <= 12 && d > 0 && d <= daysInMonth(m, y);
}

//
// Handle the Publish button for a queued photo
//
function do_publish_photo(elt)
{
    // walk up to the enclosing DIV.row element
    let rowdiv = null;
    while (elt.parentElement) {
        elt = elt.parentElement;
        if (elt.nodeName === 'DIV' && elt.className === 'row') {
            rowdiv = elt;
            break;
        }
    }

    // get DOM elements/values from below the rowdiv
    let f_state;
    let f_location;
    let f_daterange;
    let f_day;
    let f_month;
    let f_year;
    let f_caption;
    let f_image;
    let f_tagnames;

    let e_error = rowdiv.getElementsByClassName('error')[0];

    try {
        f_state = rowdiv.getElementsByClassName('state')[0].value;
        f_location = rowdiv.getElementsByClassName('location')[0].value;
        f_daterange = rowdiv.getElementsByClassName('daterange')[0].value;
        f_day = rowdiv.getElementsByClassName('day')[0].value;
        f_month = rowdiv.getElementsByClassName('month')[0].value;
        f_year = rowdiv.getElementsByClassName('year')[0].value;
        f_caption = rowdiv.getElementsByClassName('caption')[0].value;
        f_image = rowdiv.getElementsByClassName('image')[0].value;
        f_tagnames = rowdiv.getElementsByClassName('tagnames')[0].value;
    }
    catch (e) {
        e_error.innerHTML = 'Internal error: bad element ID [' + e.filename + ':' + e.lineNumber + ']';
        return false;
    }

    // validate the form contents
    let today = new Date();
    let this_year = today.getFullYear();
    let this_month = today.getMonth() + 1;
    let this_day = today.getDate();

    if (f_location.length === 0) {
        e_error.innerHTML = 'Error: location must be specified';
        return false;
    }

    if (f_year.length === 0) {
        e_error.innerHTML = 'Error: year must be specified';
        return false;
    } else {
        if (!isNumeric(f_year)) {
            e_error.innerHTML = 'Error: this is not a valid year';
            return false;
        }
        if (f_year < 1850) {
            e_error.innerHTML = 'Error: year is too small';
            return false;
        }
    }

    if (f_daterange === 'exact') {
        if (f_day.length === 0) {
            e_error.innerHTML = 'Error: day must be specified for "exact" dates';
            return false;
        } else if (f_month === '') {
            e_error.innerHTML = 'Error: month must be specified for "exact" dates';
            return false;
        }
    }

    if (f_day.length > 0) {
        if (f_month === 0) {
            e_error.innerHTML = 'Error: cannot specify day without month';
            return false;
        }
        if (f_day < 1 || f_day > daysInMonth(f_month, f_year)) {
            e_error.innerHTML = 'Error: this is not a valid date';
            return false;
        }
    }

    let future_date = false;
    if (f_year > this_year) {
        future_date = true;
    } else if (f_month !== '') {
        if (f_year === this_year && f_month > this_month) {
            future_date = true;
        } else if (f_day.length > 0) {
            if (f_year === this_year && f_month === this_month && f_day > this_day) {
                future_date = true;
            }
        }
    }
    if (future_date) {
        e_error.innerHTML = 'Error: cannot specify a future date';
        return false;
    }

    if (f_caption.length === 0) {
        e_error.innerHTML = 'Error: caption must be specified';
        return false;
    }

    // look up the checked tags; f_tagnames contains all possible tags, so
    // we use this to look up each tag checkbox in turn.
    let checked_tags = [];
    let alltags = f_tagnames.split(',');
    for (let i = 0; i < alltags.length; i++) {
        let tag_field = 'tag-' + alltags[i];
        if (rowdiv.getElementsByClassName(tag_field)[0].checked) {
            checked_tags.push(alltags[i]);
        }
    }
    let f_tags = checked_tags.join(',');

    e_error.innerHTML = '';

    let form_data = {
        'state': f_state,
        'location': f_location,
        'file': f_image,
        'daterange': f_daterange,
        'day': f_day,
        'month': f_month,
        'year': f_year,
        'caption': f_caption,
        'tags': f_tags,
    };

    // add the entry to the database
    $.ajax({
        'url': '/c/upload/aj-publish.php',
        'data': form_data,
        'dataType': 'json',
        'success': function(data, text, jqXHR) {
            let reply = JSON.parse(jqXHR.responseText);
            if ('error' in reply) {
                e_error.innerHTML = 'Publish failed: ' + reply['error'];
            } else {
                // remove the row from the queue
                rowdiv.parentNode.removeChild(rowdiv);
            }
        },
        'error': function(jqXHR, status, error) {
            // some network error occurred
            e_error.innerHTML = 'Publish failed: ' + jqXHR.statusText;
        },
    });

    return false;

}

//
// Handle the Delete button for a queued photo
//
function do_delete_photo(elt)
{
    // walk up to the enclosing DIV.row element
    let rowdiv = null;
    while (elt.parentElement) {
        elt = elt.parentElement;
        if (elt.nodeName === 'DIV' && elt.className === 'row') {
            rowdiv = elt;
            break;
        }
    }

    let error = '';
    // get DOM elements/values from below the rowdiv
    let e_error;
    let f_image;
    try {
        e_error = rowdiv.getElementsByClassName('error')[0];
        f_image = rowdiv.getElementsByClassName('image')[0].value;
    }
    catch (e) {
        error = 'Internal error: bad element ID [' + e.filename + ':' + e.lineNumber + ']';
        e_error.innerHTML = error;
        return false;
    }

    // delete from server via ajax callback
    $.ajax({
        url: '/c/upload/aj-delete.php',
        data: {key: f_image},
        dataType: 'json',
        success: function(data, text, jqxhr) {
            // remove the row from the queue
            rowdiv.parentNode.removeChild(rowdiv);
        },
    });

    return false;
}
