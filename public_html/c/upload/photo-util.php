<?php

require_once 'site.inc';

function get_photo_queue_item_html($image)
{
    $photo_tags = get_available_photo_tags();

    $tag_name_array = [];
    foreach ($photo_tags as $tag) {
        $tag_name_array[] = $tag['name'];
    }
    $tag_name_list = join(',', $tag_name_array);

    # fill in template for this photo
    $t = new HTML_Template_ITX('.');
    $t->loadTemplateFile('photo-queue-item.tpl', true, true);

    foreach ($photo_tags as $tag) {
        $t->setCurrentBlock('PHOTO-TAG');
        $t->setVariable('NAME', $tag['name']);
        $t->setVariable('TEXT', $tag['text']);
        $t->setVariable('DESCRIPTION', $tag['description']);
        $t->parseCurrentBlock();
    }

    foreach (get_locations() as $location) {
        $t->setCurrentBlock('LOCATION');
        $t->setVariable('LOCATION-NAME', $location);
        $t->parseCurrentBlock();
    }

    $t->setCurrentBlock('CONTENT');
    $t->setVariable('IMAGE-NAME', $image);
    $t->setVariable('THIS-YEAR', date('Y'));
    $t->setVariable('ALL-TAG-NAMES', $tag_name_list);
    $t->parseCurrentBlock();

    return $t->get('CONTENT');
}

?>
