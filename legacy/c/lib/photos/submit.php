<?php

require 'site.inc';

if (!auth_priv_normal())
    noperm_page();

$username = $_SESSION['username'];

$state = quote_external($_POST["state"], '');
$location = quote_external($_POST["location"], '');
$date = quote_external($_POST["date"], '');
$date_error = quote_external($_POST["date_error"], '');
$month = quote_external($_POST["month"], '');
$year = quote_external($_POST["year"], '');
$description = quote_external($_POST["description"], '');
$referer = quote_external($_SERVER["HTTP_REFERER"]);

$image = $_FILES['image'];

if ($err = validate_params($state, $location, $date, $date_error, $day, $month, $year, $description, $image, $username, $uid))
{
    error_page($err, $referer);
}

if ($err = process_upload($state, $location, $date, $date_error, $day, $month, $year, $description, $image, $uid))
{
    error_page($err, $referer);
}

header("Location: $referer");

exit();

#
# Validate parameters
#
function validate_params($state, $location, $date, &$date_error, &$day, &$month, &$year, $description, $image, $username, &$uid)
{
    global $db;

    #
    # Check the location
    #
    if ($state == '')
        return "ERROR: No state specified!";
    if ($location == '')
        return "ERROR: No location specified!";

    if (!is_valid_location($state, $location))
        return "Not a valid location: $state/$location";

    #
    # Check the date
    #
    if ($date != '')
    {
        list($year, $month, $day) = explode('-', $date);
    }

    if ($year != '')
    {
        if ($year < 1850 or $year > 2020)
            return "Invalid year: $year";

        if ($month != '')
        {
            if ($month < 1 or $month > 12)
                return "Invalid month: $month";

            if ($day != '')
            {
                if ($day < 1 or $day > 31)
                    return "Invalid day: $day";

                if
                (
                    (
                        $month != 4 and $month != 6 and $month != 9 and $month != 11
                        and
                        $day > 30 
                    )
                    or
                    (
                        $month == 2
                        and
                        $day > 28
                        and
                        (
                            $year % 4 != 0
                            or
                            ($year % 100 == 0 and $year % 400 != 0)
                        )
                    )
                )
                    return "Not a valid date: $year-$month-$year";
            }

        }
        else
        {
            if ($day != '')
                return "Cannot specify day without month!";
        }
    }
    else
    {
        if ($month != '')
            return "Cannot specify month without year!";
    }

    switch ($date_error)
    {
        case 'circa':
            $date_error = 1;
            break;
        case 'before':
            $date_error = -1;
            break;
        case 'after':
            $date_error = -2;
            break;
        case 'decade':
            $date_error = 2;
            break;
        default:
            $date_error = 0;
            break;
    }

    #
    # Check the description
    #
    if ($description == '')
        return "ERROR: No description specified!";

    #
    # Check the image
    #
    #
    # $image['name']        original basename
    # $image['type']        mime type (image/jpeg)
    # $image['tmp_name']    tmp file name
    # $image['error']       0
    # $image['size']        size in bytes
    #
    if ($image['error'] != 0 or !is_readable($image['tmp_name']))
        return "ERROR: Error occurred during file upload [" . $image['error'] . "]";

    if ($image['size'] > 1024 * 1024)
        return "Image must be smaller than 1 Mbyte";

    if ($image['type'] != 'image/jpeg')
        return "Image must be a JPEG file";

    list($width, $height) = getimagesize($image['tmp_name']);

    if ($width < 500 and $height > 500)
        return "Image should be no smaller than 500x500";

    if ($width > 800 or $height > 800)
        return "Image should be no greater than 800x800";

    #
    # Check the submitter
    #
    if (($person = get_person_by_username($username)) == null)
        return "ERROR: invalid username: [$username]";

    $uid = $person['uid'];

    return 0;
}

#
# Process the uploaded file
#
function process_upload($state, $location, $date, $date_error, $day, $month, $year, $description, $image, $uid)
{
    $now = time();
    $submit_dt = strftime("%F %T", $now);

    $filename = sprintf(
        "%s-%s-%s.jpg",
        make_file_name($location),
        strftime("%Y%m%d%H%M%S", $now),
        $uid
    );

    list($width, $height) = getimagesize($image['tmp_name']);

    #
    # Save the file to the photo area
    #
    $full_filename = apache_getenv("AUSRAIL_PHOTO_DIR") . "/" . $filename;
    if ($err = save_file($image['tmp_name'], $filename, $full_filename))
    {
        unlink($image['tmp_name']);
        return $err;
    }

    #
    # Update the database with the details of the new photo
    #
    if ($err = update_database($state, $location, $day, $month, $year, $date_error, $description, $filename, $width, $height, $submit_dt, $uid))
    {
        unlink($image['tmp_name']);
        unlink($full_filename);
        return $err;
    }

    return 0;
}

function save_file($image, $basename, $filename)
{
    #
    # Copy the image to the images area
    # XXX: avoid clobbering an existing one?
    #
    if (!copy($image, $filename))
        return "ERROR: failed to copy uploaded image";

    #
    # Create a thumbnail image
    #
    list($width, $height) = getimagesize($image);
    if ($width > $height)
    {
        $t_width = 150;
        $t_height = $height * (150.0 / $width);
    }
    else
    {
        $t_height = 150;
        $t_width = $width * (150.0 / $height);
    }

    $t_filename = apache_getenv("AUSRAIL_PHOTO_DIR") . "/small/" . $basename;

    if (!($src = imagecreatefromjpeg($filename)))
        return "ERROR: failed to save open jpeg image";

    if (!($dst = imagecreatetruecolor($t_width, $t_height)))
    {
        imagedestroy($src);
        return "ERROR: failed to save create thumbnail image [$t_width, $t_height]";
    }

    if (!imagecopyresampled(
        $dst,
        $src,
        0, 0,
        0, 0,
        $t_width, $t_height,
        $width, $height
    ))
    {
        imagedestroy($src);
        imagedestroy($dst);
        return "ERROR: failed to save resample image";
    }

    if (!imagejpeg($dst, $t_filename))
    {
        imagedestroy($src);
        imagedestroy($dst);
        return "ERROR: failed to save thumbnail image";
    }

    imagedestroy($src);
    imagedestroy($dst);

    return 0;
}

function update_database($state, $location, $day, $month, $year, $date_error, $description, $filename, $width, $height, $submit_dt, $uid)
{
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        insert into r_location_photo
            select
                L.location_state,
                L.location_name,
                max(ifnull(LP.seqno, 0)) + 1,
                ?,
                NULL,
                ?,
                ?, ?, ?, ?,
                ?,
                NULL,
                ?, ?,
                ?,
                NULL,
                'U',
                'N'
            from
                r_location L
                left outer join r_location_photo LP on
                    LP.location_state = L.location_state
                    and
                    LP.location_name = L.location_name
            where
                L.location_state = ?
                and
                L.location_name = ?
            group by
                L.location_state,
                L.location_name
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("siiiiisiisss", $filename, $uid, $day, $month, $year, $date_error, $description, $width, $height, $submit_dt, $state, $location);

    if (!$stmt->execute())
    {
        $err = $db->error;
        $stmt->close();
        $db->rollback();
        return "ERROR: update failed: $err";
    }
    $stmt->close();
    $db->commit();

    return 0;
}

?>
