<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_photos_contributors()
{
    /** @var mysqli $db */
    global $db;

    $tp = [
        'title' => "NSW Railway Photo Contributors",
        'people' => [],
    ];

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) as owner,
            count(*)
        from
            r_location_photo RP left join r_user U on RP.owner_uid = U.uid
        where
            RP.hold is null
        group by
            owner
        order by
            owner
    ");
    $stmt->bind_result($fullname, $count);
    $stmt->execute();

    while ($stmt->fetch()) {
        $tp['people'][] = [
            'name' => $fullname,
            'count' => $count,
            'photos-url' => "/photos/owner.php?" . http_build_query(['owner' => $fullname]),
        ];
    }
    $stmt->close();

    return $tp;
}

normal_page_wrapper('run_photos_contributors', 'photo-contributors.latte');
