<?php

require "site.inc";

$title = "NSW Railway Photo Contributors";

$tp = [
    'title' => $title,
    'people' => [],
];

$stmt = $db->stmt_init();
if (!$stmt->prepare("
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
")) {
    throw new InternalError('prepare failed: ' . $stmt->error);
}
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

normal_page('photo-contributers.latte', $tp);

?>
