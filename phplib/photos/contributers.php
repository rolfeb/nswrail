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
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) owner,
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
    $stmt->bind_result($owner, $count);
    $stmt->execute();

    while ($stmt->fetch()) {
        $tp['people'][] = [
            'name' => $owner,
            'count' => $count,
            'photos-url' => urlenc("/photos/owner.php?owner=" . $owner),
        ];
    }
    $stmt->close();

    return $tp;
}

# require "phplib/photos/contributers.php";
normal_page_wrapper('run_photos_contributers', 'photo-contributers.latte');

?>
