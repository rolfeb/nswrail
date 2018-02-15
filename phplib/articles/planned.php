<?php

require 'site.inc';

function run_articles_planned()
{
    $tp = [
        'title' => 'Planned NSW Railway Lines',
        'lines' => [],
    ];

    $tp['lines'][] = [
        'line' => 'Cobar - Wilcannia',
        'act' => 'Cobar to Wilcannia Railway Act 1902 No 85',
        'opt_cost' => '&pound;543,527',
        'text' => <<<'EOS'
Cobar to Wilcannia Railway. This line is an extension of
the Nyngan to Cobar railway, and beginning at four hundred
and fifty-nine miles thirty-six decimal sixty chains from
Sydney proceeds in a generally west by south direction between
the parishes of Balahand Amphitheatre, and through Gidgie,
Springfield, Bluff, Cuckaroo, and passing south of Broekmetta
Lake, and close to Rock Tank; after crossing Bulla Range, the
line runs northwards down Cookermilerie Creek, and again westerly
through the parishes of Wooree, Moama, to Moama out-station;
thence west by south through the parishes of Yoree, Neelyah,
and Weatherley, and along the southern boundaries of Goonalgaa,
and Calcoo, through Gunyulka to travelling stock reserve number
three hundred and twenty-one; the line then follows this north
north-westerly to the east bank of Darling River, where it ends
at about one mile forty-eight chains from the western boundary
of reserve five hundred and eighty-seven in the township of
Wilcannia, measured back along the staked line, being a total
length of about one hundred and sixty-three miles seventy-two
chains, and subject to such deviations and modifications as
may be considered desirable by the Constructing Authority.
EOS
    ];

    $tp['lines'][] = [
        'line' => 'Coonabarabran - Burren Junction',
        'act' => 'Coonabarabran to Burren Junction Railway Act 1913 No 12',
        'opt_cost' => '&pound;403,288',
        'opt_footnote' => 'Although this line was not constructed, the Baradine-Gwabegar
section was built as part of the line from Wallerawang to Gwabegar',
        'text' => <<<'EOS'
This railway commences at the western end of the station site at
Coonabarabran on the Castlereagh River, at 313 miles 59 chains from Sydney,
and immediately crosses that river and follows its left bank north-westerly
for about 2 miles; a more northerly direction is then taken, and the old Nandi
Road is crossed several times in ascending to the Warrumbungle Range, which it
crosses and descends to Bugaldie Creek, the right bank of which it follows
to near the town of Baradine, where the creek is crossed at its confluence
with Baradine Creek; the line then passes through that township and follows
the left bank of the latter creek for twenty miles, crosses it, and proceeds
in close proximity to, and on the eastern side of the Travelling Stock
Reserves to the town of Pilliga, the eastern portion of which it passes
through, thence it takes a generally northerly bearing and crosses
Turragulla Creek, Namoi River, and Millie Creek; 10 miles beyond the latter
it terminates by a junction with the existing railway from Narrabri to
Walgett, about 30 chains west of Burren Junction Station, at 409 miles
8 chains from Sydney via Wallerawang, and approximately 403 miles 30
chains from Sydney, via Newcastle, being a total distance of 95 miles 29
chains, and subject to such deviations and modifications as may be
considered desirable by the Constructing Authority.
EOS
    ];

    $tp['lines'][] = [
        'line' => 'Canowindra - Gregra',
        'act' => 'Canowindra to Gregra Railway Act 1924 No 54',
        'opt_cost' => '&pound;216,198',
        'text' => <<<'EOS'
This railway commences at a point on the branch line from Canowindra to
Eugowra about 1 mile north-westerly from Canowindra Station, and proceeds
northerly on the eastern side of Toms Water Hole Creek for about 14 miles
to the main road to Orange, which it crosses; thence a north-westerly bearing
is taken for about 3 miles to a point about 3 miles east of the village of
Toogong; thence a north-easterly bearing is taken for 6 miles, and Bowen
Creek is crossed near its confluence with Bourimbla Creek, which is ascended
to a point about 2 miles east of the village of Cudal; thence a northerly
direction takes the line across Boree and Sandy Creeks near their confluence,
and the latter creek is ascended for about 4 miles, and the line ends by a
junction with the branch railway from Orange to Condobolin at the eastern
end of Gregra Station at 224 miles 65 chains 52 links from Sydney via Orange,
being a total distance of 33 miles 70 chains; and is subject to such
deviations and modifications as may be considered desirable by the
Constructing Authority.
EOS
    ];

    $tp['lines'][] = [
        'line' => 'Casino - Bonalbo',
        'act' => 'Casino to Bonalbo Railway Act 1928 No 16',
        'opt_cost' => '&pound;943,647',
        'opt_footnote' => 'Construction was started but only as far as creating some of the earthworks.',
        'text' => <<<'EOS'
This railway commences at the centre of the Casino platform on the existing
Grafton to Casino railway at 501 miles 30 chains from Sydney and
proceeds thence in a southerly direction for a distance of 1 mile
37 chains via the aforesaid railway to 499 miles 73 chains from
Sydney at which point it leaves the existing line and proceeds in a
westerly direction for 9 miles to the northern side of the Shannon
Brook or Deep Creek; thence it follows that side of that creek for
9 miles; thence still westerly for a distance of 3 miles ascending
to 22 miles 40 chains from Casino, at which mileage it crosses the
Richmond Range; thence in a south-westerly direction descending
on the northern side of Blacks' Camp Creek for a distance of 2&frac12;
miles to 25 miles from Casino; thence it turns sharply and proceeds
in a north westerly direction for 5 miles to 30 miles from Casino,
at which mileage it crosses the spur dividing the waters of the
Tunglebung and Bottle Creeks; thence descending in a north-westerly
direction for about. 2&frac12; miles to Bottle Creek, which it crosses at
32&frac12; miles and proceeds in a northerly and north-westerly direction
for about 5&frac12; miles to Bonalbo, where it terminates at 539 miles 20
chains from Sydney, being a total distance of 37 miles 76 chains
from Casino and subject to such, deviations and modifications as
may be considered desirable by the Constructing Authority.
EOS
    ];

    $tp['lines'][] = [
        'line' => 'Glen Innes - Inverell',
        'act' => 'Glen Innes to Inverell Railway Act 1950 No 7',
        'opt_cost' => '&pound;3,000,000',
        'text' => <<<'EOS'
The proposed railway commences at a point on the Main Northern Line 423
miles 50 chains from Sydney distant 20 chains north of Glen Innes station
and proceeds northerly and westerly, crossing Furracabad, Reddeston and
Black Plain Creeks, a distance of approximately 13&frac12; miles, to a point
about 3 miles south of Wellingrove, thence generally south-westerly 4&frac12;
miles to cross Wellingrove Creek near its confluence with Maids Valley Creek,
thence southerly 2&frac12; miles along the right bank of Maids Valley Creek and
&frac12; mile beyond towards Fletchers Nob having passed 1&frac12; miles to the west
of Waterloo, thence generally southerly and westerly about 29 miles passing
between Dumbeg on the south and Mount Buckley on the north to follow Swan
Brook past the Bald Hills and Sugarloaf and crossing Swan Brook approximately
&frac12; mile east of its confluence with Main Gully and crossing Red Camp and
Long Plain Gullies and the Macintyre River to join the North Western Line
at Inverell, being a total distance of 50 miles 6 chains and is subject
to such deviations and modifications as may be considered desirable by
the constructing authority.
EOS
    ];

    $tp['lines'][] = [
        'line' => 'Gilgandra - Collie',
        'act' => 'Gilgandra to Collie Railway Act 1915 No 47',
        'opt_cost' => '&pound;105,000',
        'text' => <<<'EOS'
This railway commences by a junction with the existing line from Dubbo to
Coonamble, at Gilgandra station, about 322 miles from Sydney, and proceeds
nearly due west, crosses Marthaguy Creek about 326 miles, and in following
the southern side of that creek it crosses and recrosses Calf Pen Creek; at
about 341 miles the line curves and takes a north-westerly direction to
the western portion of the town of Collie, where it ends on the southern
side of Marthaguy Creek at 346 miles from Sydney, being a total distance of
24 miles, and subject to such deviations and modifications as may be
considered desirable by the Constructing Authority.
EOS
    ];

    $tp['lines'][] = [
        'line' => 'Guyra - Dorrigo',
        'act' => 'Guyra to Dorrigo Railway Act 1928 No 15',
        'opt_cost' => '&pound;1,940,440',
        'text' => <<<'EOS'
This railway commences on the existing Great Northern Railway at 288 miles 
from Newcastle about 2 miles north of Guyra Railway Station, thence it 
proceeds in a generally easterly direction to 323 miles, passing about 1 
mile south of the village of Falconer at 292 miles; from about 300 to 323 
miles it follows the ridge dividing the waters of the Aberfoyle River on 
the north from the waters of the Chandler River on the south; the line then 
proceeds south-easterly to 341 miles and skirts the north-eastern end of 
Doughboy Range, and the head of Guy Fawkes Station is passed about 2&frac12; miles 
on the south; from 354 to 368 miles by the free use of curvature the head 
of Little Murray River is reached, the village of Deer Vale being passed on 
the south at about 363 miles; thence the north-easterly direction is 
resumed to the township of Dorrigo, where the line ends at 377 miles from 
Newcastle and junctions with the existing line from Glenreagh at about 70 
miles from South Grafton being a total distance of 89 miles and is subject 
to such deviations and modifications as may be considered desirable by the 
Constructing Authority.
EOS
    ];

    $tp['lines'][] = [
        'line' => 'Inverell - Ashford',
        'act' => 'Inverell to Ashford Railway Act 1927 No 27',
        'opt_cost' => '&pound;262,000',
        'text' => <<<'EOS'
This railway commences by a junction with the branch line from Moree to
Inverell at 508 miles from Sydney and about 2 miles north-westerly from
Inverell terminus, and it proceeds north-westerly on the western side of
Macintyre River, and across Spicer's, Rob Roy, and Jessie Gullies, also
about 1 mile west of the village of Byron to 517 miles, thence to 527
miles where it crosses the Macintyre River the line runs northerly, and
the eastern side of that river is ascended to 531 miles; thence a generally
northerly direction is taken in close proximity to the western boundary
of Travelling Stock Reserve 337 to the village of Ashford where the line 
terminates at 540 miles from Sydney, being a total distance of 32 miles,
and subject to such deviations and modifications as may be considered
desirable by the Constructing Authority.
EOS
    ];

    $tp['lines'][] = [
        'line' => 'Rand - Bull Plain',
        'act' => 'Rand to Bull Plain Railway Act 1924 No 60',
        'opt_cost' => '&pound;175,933',
        'text' => <<<'EOS'
This line commences at the southern end of Rand Station on the northern bank of
Billabong Creek at 393 miles 56 chains from Sydney; it immediately crosses that
creek and proceeds in a south-westerly direction, and crosses Fighting Harry
Creek at 397 miles 40 chains; the Daysdale-Walbundrie road is crossed at 401
miles 20 chains, about 2 miles beyond which Coreen Hill is passed, and on the
north the Daysdale-Carowa road and stock route are crossed at 410 miles,
Daysdale being distant by that road 4 miles 60 chains; Twelve-mile Creek is
crossed at 412 miles, and the line terminates near Bull Plain and about 6 miles
south-easterly from Savanake and about 5 miles north-westerly from Ringwood at
421 miles 20 chains from Sydney; and is subject to such deviations and
modifications as may be considered desirable by the Constructing Authority.
EOS
    ];

    $tp['lines'][] = [
        'line' => 'St Leonards - Eastwood',
        'act' => 'St. Leonards to Eastwood Railway Act 1927 No 26',
        'opt_cost' => '&pound;693,918',
        'text' => <<<'EOS'
The proposed electric railway commences at a point on the Milson's Point to
Hornsby line at 3 miles 30 chains from Milson's Point, and about 40 chains
north of St. Leonards Station; the route proceeds in a westerly direction to
Parklands-avenue, thence it bears south-westerly to the head of Burns Bay, and
takes a generally north-westerly direction and crosses Lane Cove River
immediately north of its confluence on its western side with Buffalo Creek;
ascends Kitty's Creek, at the head of which a westerly bearing takes it past
the northern side of North Ryde Public School and School of Arts, across the
Great North road and to the terminus at 11 miles 56 chains from Milson's
Point at a point about 20 chains north of the Eastwood Station on the existing
Sydney to Hornsby line, being a total distance of 9 miles 6 chains, including
the branch line to the Northern Suburbs Cemetery, and subject to such
deviations and modifications as may be considered desirable by the Constructing
Authority.
EOS
    ];

    $tp['lines'][] = [
        'line' => 'Sandy Hollow - Maryvale',
        'act' => 'Sandy Hollow, via Gulgong, to Maryvale Railway Act 1927 No 28',
        'opt_cost' => '&pound;1,353,789',
        'opt_footnote' => 'The section Sandy Hollow - Gulgong was started, abandoned, and later completed in the 1980s.',
        'text' => <<<'EOS'
This railway connecting the northern and western railway systems commences 
at 206&frac12; miles from Sydney and at the western end of Sandy Hollow Station
on the Muswellbrook to Merriwa Branch from the Great Northern Railway, and it 
proceeds generally westerly across Goulburn River and in close proximity to 
the right or southern side of that river to 260 miles and past the villages 
of Baerami and Bylong, where a southerly bearing is taken to 265 miles; 
thence the line bears westerly past the village of Wollar and up Wilpinjong 
Creek, also past the village of Ulan and across Murragamba Creek and the 
Main Dividing Range; thence a south-westerly direction is taken to a point 
about 16 chains north of Gulgong Station on the branch line from 
Wallerawang to Coonabarrabran; thence a portion of that railway is followed 
for about 2&frac34; miles north-westerly to the northern side of Wyaldra Creek, 
where a westerly direction is taken across Puggoon Creek and through the 
gap in the range dividing the waters of the Cudgegong and Talbragar Rivers 
and across Sandy Creek: thence a generally south-westerly bearing takes the 
line down Bungiebomar Creek and across Spicer's Creek close to their 
confluence and over the range between the latter creek and Mitchell's 
Creek, across that creek to the terminus at 355 miles 48 chains from 
Sydney, being a point on the Great Western Railway at 263&frac12; miles from 
Sydney and about 1&frac12; miles from the northern end of Maryvale Station,
being a total distance of 146&frac12; miles, exclusive of the portion of the
Wallerawang to Coonabarrabran railway aforesaid, and is subject to such
deviations and modifications as may be considered desirable by the
Constructing Authority.
EOS
    ];

    $tp['lines'][] = [
        'line' => 'Thirlmere - Burragorang',
        'act' => 'Thirlmere to Burragorang Railway Act 1951 No 28',
        'opt_cost' => '&pound;934,000',
        'text' => <<<'EOS'
The proposed railway commences at Thirlmere on the Picton
to Mittagong Loop about 57 miles from Sydney and swings in a
curve from south westerly to northerly about one mile from
its commencement proceeds northerly a further mile thence
generally north westerly crossing Cedar Creek Stonequarry
Creek and the headwaters of Werriberri Creek and skirting
the western side of The Green Hills and generally following
the watershed between Wollondilly and Nepean Catchments
over Pumpkin Hills and past Oakdale to a point about 13
miles 70 chains from its commencement, the whole railway
being within the Parishes of Couridjah, Burragorang and
Werriberri in the County of Camden, and subject to such
deviations and modifications as may be considered desirable
by the Constructing Authority.
EOS
    ];

    $tp['lines'][] = [
        'line' => 'Wyalong - Condobolin',
        'act' => 'Wyalong Towards Condobolin Railway Act 1923 No 47',
        'opt_cost' => '&pound;158,400',
        'text' => <<<'EOS'
This railway commences at the western end of the West Wyalong Station at 346
miles 20 chains from Sydney, and proceeds in a north-easterly direction for
about 15 miles and passes west of White Tank; thence it runs northerly, crosses
Sandy Creek, and passes about 2 miles east of Billy's Look Out near where the
line crosses Billy's Look Out Creek at 365 miles; thence it curves north-
westerly and proceeds approximately parallel to and about 4 miles distant from
the western shore of Lake Cowal, where it ends at 379 miles 20 chains from
Sydney, being a total distance of 33 miles, and subject to such deviations and
modifications as may be considered desirable by the Constructing Authority.
EOS
    ];
    return $tp;
}

normal_page_wrapper('run_articles_planned', 'articles-planned.latte');

?>
