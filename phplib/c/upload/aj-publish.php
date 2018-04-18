<?php
#
# AJAX callback handler
#   aj-publish.php: publish a staged image
#
require 'site.inc';

/**
 * Convert a location name to a directory name.
 *
 * @param $location
 * @return mixed|string
 */
function convert_location_to_folder($location)
{
    $location = strtolower($location);
    $location = str_replace(' ', '_', $location);
    $location = str_replace('(', '', $location);
    $location = str_replace(')', '', $location);
    return $location;
}

/**
 * Create a unique filename.
 *
 * If an image name is being re-used, then try giving it a numeric suffix.
 * If this still fails after several goes, then give up - the user needs a
 * better workflow!
 *
 * @param $publish_dir
 * @param $image_base
 * @return string
 * @throws InternalError
 */
function make_unique_filename($publish_dir, $image_base)
{
    $pi = pathinfo($image_base);
    $filename = $pi['filename'];
    $suffix = $pi['extension'];

    for ($n = 2; $n < 20; $n++) {
        $try_base = "$filename-$n.$suffix";
        if (!file_exists("$publish_dir/$try_base")) {
            return $try_base;
        }
    }

    throw new InternalError("Error: couldn't make unique file for $image_base");
}

/**
 * Publish the given image
 *
 * @param $state
 * @param $location
 * @param $image
 * @param $daterange
 * @param $day
 * @param $month
 * @param $year
 * @param $caption
 * @param $tags
 * @throws ImagickException
 * @throws InternalError
 * @throws UserError
 */
function publish_uploaded_image($state, $location, $image, $daterange, $day,
                                $month, $year, $caption, $tags)
{
    /** @var mysqli $db */
    global $db, $user;

    # [re]validate parameters
    if (!is_valid_location($state, $location)) {
        throw new UserError("Error: not a valid location: $state/$location");
    }

    if (strpos($image, '/') !== false or strpos($image, '\\') !== false) {
        Audit::addentry(Audit::A_SECURITY, "publish attempt for [$image]");
        throw new InternalError("Error: invalid image name: $image");
    }

    # XXX: make sure date combination is valid

    if (!$caption) {
        throw new InternalError("Error: missing caption text");
    }

    $uid = $user->uid;

    $stage_dir = get_config('stage-dir') . "/$uid";
    $stage_thumb_dir = "$stage_dir/small";
    $staged_image = "$stage_dir/$image";
    $staged_thumbnail = "$stage_thumb_dir/$image";

    $location_folder = convert_location_to_folder($location);
    $publish_dir = get_config('photo-media-dir') . "/$location_folder";
    $publish_thumb_dir = get_config('photo-media-dir') . "/thumbnails/$location_folder";

    $image_base = "$uid-$image";
    $published_image = "$publish_dir/$image_base";
    $published_thumbnail = "$publish_thumb_dir/$image_base";

    # create destination directories before we commit anything
    if (!file_exists($publish_dir)) {
        if (!mkdir($publish_dir)) {
            throw new InternalError("Error: failed to create publish directory: $publish_dir");
        }
    }
    if (!file_exists($publish_thumb_dir)) {
        if (!mkdir($publish_thumb_dir)) {
            throw new InternalError("Error: failed to create publish thumbnail directory");
        }
    }

    # make sure we don't clash with an existing image
    if (file_exists($published_image)) {
        $image_base = make_unique_filename($publish_dir, $image_base);
        $published_image = "$publish_dir/$image_base";
        $published_thumbnail = "$publish_thumb_dir/$image_base";
    }

    $im = new Imagick($staged_image);
    $width = $im->getImageWidth();
    $height = $im->getImageHeight();

    # insert row into database
    $stmt = $db->stmt_init();

    $stmt->prepare('
        insert into
            r_location_photo
            (
                location_state,
                location_name,
                file,
                owner_uid,
                daterange,
                day,
                month,
                year,
                caption,
                tags,
                width,
                height
            )
        values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->bind_param('sssiiiiissii', $state, $location, $image_base, $uid,
                      $daterange, $day, $month, $year, $caption, $tags, $width,
                      $height);
    $stmt->execute();
    $stmt->close();

    # move and rename images
    if (!rename($staged_image, $published_image)) {
        throw new InternalError("Error: unable to create $published_image");
    }
    if (!rename($staged_thumbnail, $published_thumbnail)) {
        throw new InternalError("Error: unable to create $published_thumbnail");
    }
}

if ($user->is_guest()) {
    noperm_page();
}

try {
    $state = param_post_string('state');
    $location = param_post_string('location');
    $file = param_post_string('file');
    $daterange = param_post_integer('daterange');
    $day = param_post_integer('day');
    $month = param_post_integer('month');
    $year = param_post_integer('year');
    $caption = param_post_string('caption');
    $tags = param_post_string('tags');

    publish_uploaded_image($state, $location, $file, $daterange, $day, $month,
        $year, $caption, $tags);

    $reply = [];
} catch (Exception $e) {
    $reply = [ 'error' => $e->getMessage() ];
}

print(json_encode($reply));
