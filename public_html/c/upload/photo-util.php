<?php

require 'site.inc';

function get_photo_queue_item_html($tp, $image)
{
    $photo_tags = get_available_photo_tags();

    $tag_name_array = [];
    foreach ($photo_tags as $tag) {
        $tag_name_array[] = $tag['name'];
    }
    $tag_name_list = join(',', $tag_name_array);

    # fill in template for this photo

    $tp = [
        'image_name' => $image,
        'this_year' => date('Y'),
        'all_tag_names' => $tag_name_list,
        'tags' => [],
    ];


    # populate the photo tag checkboxes
    foreach ($photo_tags as $tag) {
        $tp['tags'][] = [
            'name' => $tag['name'],
            'text' => $tag['text'],
        ];
    }

    $latte = new Latte\Engine;
    return $latte->renderToString('photo-queue-item.latte', $tp);
}

?>
