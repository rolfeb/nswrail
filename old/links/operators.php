<?php

require_once "core.inc";

$intro = "";

$links = array(
array(
    "http://www.railcorp.nsw.gov.au/",
    "Railcorp NSW",
    "Railcorp is the organisation in charge of NSW railways.",
),
array(
    "http://www.cityrail.nsw.gov.au/",
    "CityRail",
    "CityRail is Railcorp's division which looks after non-booked passenger
    travel, within Sydney's greater metropolitan area.  This extends to
    Newcastle and Dungog in the north, Lithgow in the west, and Nowra and
    Goulburn to the south.",
),
array(
    "http://www.countrylink.nsw.gov.au/",
    "CountryLink",
    "CountryLink looks after booked passenger travel outside the Sydney area,
    including interstate travel.",
),
array(
    "http://www.gsr.com.au",
    "Great Southern Railway (GSR)",
    "GSR are the new operators of the famous Indian-Pacific train, running
    between Sydney and Perth.",
),
array(
    "http://www.perisherblue.com.au",
    "Perisher Blue",
    "Perisher Blue Pty Ltd operate the SkiTube between Bullocks Flat
    and Blue Cow Mountain, in the Snowy Mountains.",
),
array(
    "http://www.pacificnational.com.au/",
    "Pacific National",
    "Pacific National is the main freight operator in NSW.  It resulted from
    the merger of National Rail and FreightCorp.",
),
array(
    "http://www.freightaustralia.com.au/",
    "Freight Australia",
    "Briefly known as Freight Victoria, Freight Australia used to be the
    freight division of V/Line.  They work some interstate trains,
    and also run on several broad gauge lines in NSW.",
),
array(
    "http://www.interail.com.au/",
    "Interail Australia",
    "Interail operates in Northern NSW.  It is the result of the purchase of
    the Northern Rivers Railroad by Queensland Rail.
    They use restored 422 locomotives for much of their work.",
),
array(
    "http://www.atn.interliant.com/",
    "ATN Access",
    "ATN Access is a subsidiary of ATN (Australian Transport Network) which
    has contracted to haul grain in southern NSW.",
),
array(
    "http://www.silverton.net.au/",
    "Silverton Rail",
    "A subsidiary of the old Silverton Tramway Company, Silverton Rail are
    based in Parkes and provide locos and haulage around NSW, using some
    48 and 442 class locos.",
),
array(
    "http://www.arg.net.au/",
    "Australian Railroad Group (ARG)",
    "The result of the joining of ASR, ANR and AWR. They run a lot of traffic
    on the South Coast.",
),
array(
    "http://www.cfcla.com.au/",
    "Chicago Freight Car Leasing Australia (CFCLA)",
    "CFCLA provide wagons and motive power, most notably for Interail (the
    Melbourne - Sydney container train) and Grain Corp. ",
),
array(
    "http://www.lvr.com.au/",
    "Lachlan Valley Railway Freight",
    "LVRF is the freight wing of the Lachlan Valley Railway.",
),
array(
    "http://www.3801limited.com.au/",
    "3801 Ltd",
    "3801 Ltd run charters around NSW, often using preserved steam locos 3801
    and 3830.",
),
array(
    "http://www.zigzagrailway.com.au/",
    "Zig Zag Railway",
    "The Zig Zag Railway operates on an abandoned zig-zag railway section near
    Lithgow (west of Sydney).",
),
array(
    "http://www.accsoft.com.au/~rtm/",
    "Railway Transport Museum",
    "The NSW RTM is based at Thirlmere (south-west of Sydney).  They often
    run tours around the state.",
),
array(
    "http://arhsact.org.au/",
    "ARHS ACT",
    "The ACT division of the Australian Railway Historic Society is based in
    Canberra.  They often run tours using their own railway stock, as well as
    rail-motor trips on the Michelago Tourist Railway (near Canberra).",
),
array(
    "http://www.lvr.com.au/",
    "Lachlan Valley Railway",
    "The LVR is based at Cowra, in the central-west.  They run freight and
    passenger trains, mainly over the Blayney-Demondrille line and the
    Eugowra branch line out to Canowindra.",
),
array(
    "http://www.sets.org.au/",
    "Sydney Electric Train Society",
    "SETS runs occasionally tours around the Sydney electric network using some
    preserved passenger carriages (\"red rattlers\").",
),
array(
    "http://www.railmotorsociety.org.au/",
    "Rail Motor Society",
    "The RMS runs preserved railmotors from its base at Paterson, in the Hunter
    Valley.",
),
array(
    "http://www.cmrailway.org.au/",
    "Cooma Monaro Railway",
    "The CMR operates preserved CPH railmotors on part of the old Bombala
    line, from Cooma to Chakola.",
),
array(
    "http://het.org.au",
    "Historic Electric Traction",
    "HET is a group which runs tours using State Rails preserved electric
    train fleet.",
),
array(
    "http://www.dsrm.org.au/",
    "Dorrigo Steam Railway & Museum",
    "This group has been collection rolling stock for a while with the intent
    of running trains over the upper part of the Dorrigo line.",
),
array(
    "http://www.gmr.org.au/",
    "Glenreagh Mountain Railway",
    "This group intends to run tourist trains over the lower part of the
    Dorrigo branch.",
),
array(
    "http://www.tenterfieldbiz.com/railway/",
    "Tenterfield Railway Station Preservation Society",
    "This society is now running rail trike services between the Bruxner
    Highway crossing (4km north of Tenterfield) to Bluff Rock.",
),
array(
    "http://www.freewebs.com/aunewenglandrailwayinc/index.htm",
    "New England Railway Incorporated",
    "This tourist rail society is based at Glen Innes.  They have an agreement
    to use the Glen Innes yard and the line as far as Stonehenge.",
),
array(
    "http://www.gchr.4t.com/",
    "Goulburn Crookwell Heritage Railway",
    "This group were accredited in November 2001 and will shortly be running
    rail trikes at both the top and bottom ends of the Crookwell branch.",
),
array(
    "http://www.railpage.org.au/tumbarail/",
    "Tumba Rail",
    "This group is working on operating on disused Tumbarumba branch.  They
    have restired Ladysmith station and started running rail trikes on a
    portion of the line.",
),
array(
    "http://www.ozsite.com.au/oberonrailway/default.htm",
    "Oberon Tarana Heritage Railway",
    "A group working towards the restoration of the Oberon branch, with the
    goal of eventually running some tourist traffic over the line.",
),
array(
    "http://www.ric.nsw.gov.au/",
    "Rail Infrastructure Corporation",
    "In January 2001, the RSA and RAC were merged into the Rail Infrastructure
    Corporation.",
),
array(
    "http://www.artc.com.au/",
    "Australian Rail Track Corporation",
    "An organisation charged with running the major interstate links.  They
    have a lease on certain NSW lines for the next 60 years.",
),
);

show_links("NSW Railway Operators", $intro, $links);
?>
