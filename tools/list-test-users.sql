select * from r_user where username like 'rolfe+test%';

select
    U.username,
    U.uid,
    count(LP.owner_uid) num_photos
from
    r_user U
        left outer join r_location_photo LP
            on
            LP.owner_uid = U.uid
where
    U.username like 'rolfe%'
group by
    U.uid
;
