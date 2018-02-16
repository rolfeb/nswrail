<?php

require "site.inc";

function run_photos_contributers()
{
    global $db;

    $tp = [
        'title' => "NSW Railway Photo Contributors",
        'people' => [],
    ];

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) as fullname,
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
            'photos-url' => urlenc("/photos/owner.php?owner=" . $fullname),
        ];
    }
    $stmt->close();

    return $tp;
}

normal_page_wrapper('run_photos_contributers', 'photo-contributers.latte');

?>
