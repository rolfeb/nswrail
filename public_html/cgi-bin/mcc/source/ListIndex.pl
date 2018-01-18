#ListIndex

$listbox_script= qq~
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	function getSelectedString(selectItem) {
		var i;
		var j = 0;
		var outlist = "";

		for (i = 0; i < selectItem.options.length; i++) {
			if (j > 0) {
				outlist = outlist + "+";
			}
			outlist = outlist + selectItem.options[i].value;
			j++;
		}
		return outlist;
	}

	function addItems(fromItem, toCtrl) {
		var i;
		var j;
		var itemexists;
		var nextitem;

		// step through all items in fromItem
		for (i = 0; i < fromItem.options.length; i++) {
			if (fromItem.options[i].selected) {
				// search toCtrl to see if duplicate
				j = 0;
				itemexists = false;
				while ((j < toCtrl.options.length) && (!(itemexists))) {
					if (toCtrl.options[j].value == fromItem.options[i].value) {
						itemexists = true;
						alert(fromItem.options[i].value + " found!");
					}
					j++;
				}
				if (!(itemexists)) {
				// add the item
					nextitem = toCtrl.options.length;
					toCtrl.options[nextitem] = new Option(fromItem.options[i].text);
					toCtrl.options[nextitem].value = fromItem.options[i].value;
				}
			}
		}
	}


	function removeItems(fromItem) {
	var i = 0;
		var j;
		var k = 0;

		while (i < (fromItem.options.length - k)) {
			if (fromItem.options[i].selected) {
				// remove the item
				for (j = i; j < (fromItem.options.length - 1); j++) {
					fromItem.options[j].text = fromItem.options[j+1].text;
					fromItem.options[j].value = fromItem.options[j+1].value;
					fromItem.options[j].selected = fromItem.options[j+1].selected;
				}
				k++;
			} else {
				i++;
			}
		}
		for (i = 0; i < k; i++) {
			fromItem.options[fromItem.options.length - 1] = null;
		}
	}
	//-->
	</SCRIPT>
~;

sub addlisttitle {
	my ($column,$title,$asc,$action,$sortcolumn,$width,$width2,$nosort) = @_;
	my $order = $column!=$sortcolumn ? '0' : $asc==0 ? '1' : '0' ;
	$width2='99%' if (!$width2);
	$width='100%' if (!$width);
	if ($nosort==1) {
		$subsmain .= qq~<td><table cellpadding=0 width=$width cellspacing=0 border=0><tr valign=middle>
					<td width=$width2 class="catbg" bgcolor="$color{'catbg'}">$title</td></tr></table></td>~;
	} else {
		my $img =  $column!=$sortcolumn ?
				 qq~<img src="$imagesdir/ano.gif" border=0>~ :
				 $asc==1 ? qq~<img src="$imagesdir/aup.gif" border=0>~ :
									qq~<img src="$imagesdir/adn.gif" border=0>~ ;
		$subsmain .= qq~<td><table cellpadding=0 width=$width cellspacing=0 border=0><tr valign=middle>
			<td width=$width2 class="catbg" bgcolor="$color{'catbg'}"><a href="$cgi;action=$action;order=$order;sort=$column">$title</a></td>
			<td class="catbg" bgcolor="$color{'catbg'}">
			<a href="$cgi;action=$action;order=$order;sort=$column">$img</a></td></tr></table></td>~;
	}
}

sub ListIndex {
	my ($indexfile,$action,@unsortedlist) = @_;
	#$title1,$title2,$msg
	#sort,recordnr,title,width,color,align,opmaak
	fopen(FILE,"$datadir/$indexfile");
	my (@lines) = <FILE>;
	fclose(FILE);
	my @sortlist = ($INFO{'column'} || 0);
	my @sortdata = ();
	my $colums = @sortdata;
	my ($title1,$title2,$msg) = split(/\|/,$lines[0]);
	for (my $i=1;$i<@lines;$i++) {
		my (@sortitem) = split(/\|/,$lines[$i]);
		#unshift @sortdata;
	}
	my @list = sort {
		for (my $i=0;$i<@sortlist;$i++) {
			return (1) if ($a[$sortlist[$i]]>$b[$sortlist[$i]]);
			return (-1) if ($a[$sortlist[$i]]<$b[$sortlist[$i]]);
		}
		return (0);
	 } @unsortedlist;
	my $count = @list;

	my $start = $INFO{'start'} || 0;
	my $numshown=0;
	my $numbegin = ($start + 1);
	my $numend = ($start + $ItemsPerPage);
	$numend = $count if($numend > $memcount);
	my $b = $start;
	$subsmain .= qq~
		<center><font size="2" class="nav"><B>$title1 $numbegin $txt{'311'} $numend ($txt{'309'} $count $title2)</B></font></center><BR>
		<table border=0 width=100% cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor">
			<tr>
				<td class="titlebg" bgcolor="$color{'titlebg'}" colspan="4"><b><a href="$cgi;action=$action;column=$sortlist[0]"><font size=2 class="imgmenu" color="$color{'titletext'}">$msg</font></a></b>
				<b><font size=2 class="text1" color="$color{'titletext'}">$mcctxt{'25'}</font></b></td>
		  </tr><tr>~;
		  for (my $i;$i<$columns;$i++) {
				$subsmain .= qq~<td class="catbg" bgcolor="$color{'catbg'}" ~;
				$subsmain .= qq~width="$sortdata[$i][3]" ~ if ($sortdata[$i][3] ne '');
			  $subsmain .= qq~<b><font size=2>$sortdata[$i][2]</font></b></td>~;
			}
			$subsmain .= qq~</tr>~;
  my ($line);
	while(($numshown < $ItemsPerPage) and $b<$count) {
		$numshown++;
		$subsmain .= qq~<tr>~;
		for (my $i;$i<$columns;$i++) {
			$subsmain .= qq~<td ~;
			$subsmain .= qq~class="$sortdata[$i][4]" bgcolor="$color{$sortdata[$i][4]}" ~ if ($sortdata[$i][4] ne '');
			$subsmain .= qq~align="$sortdata[$i][5]" ~ if ($sortdata[$i][5] ne '');
			$line = $sortdata[$i][6] || '<item>';
			$line =~ s~<item>~$list[$b][$sortdata[$i][1]]~g;
		  $subsmain .= qq~$line</td>~;
			}
		$subsmain .=qq~</tr>~;
		$b++;
	}
	$subsmain .= qq~</table>~;
	if ($numend==0) {$numbegin=0;}
  $subsmain .= page_index($start,$action,$count,$sortlist[0]);
	$substitle = "$title1 $numbegin $txt{'311'} $numend";
	template();

}

#A list with all groups
sub GroupList {
  my $column = $INFO{"sort"} || 0;
  my $order = $INFO{"order"} || 0;
	my @list = (get_group_sort_keys(!$order,$column));
	my $count = ($#list)+1;

	my $start = $INFO{'start'} || 0;
	my $numshown=0;
	my $numbegin = ($start + 1);
	my $numend = ($start + $ItemsPerPage);
	if($numend > $memcount) {
		$numend = $count;
	}
	my $b = $start;
	$subsmain .= qq~
		<center><font size="2" class="nav"><B>$mcctxt{'26'} $numbegin $txt{'311'} $numend ($txt{'309'} $count $mcctxt{'27'})</B></font></center><BR>
		<table border=0 width=100% cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor">
			<tr>
				<td class="titlebg" bgcolor="$color{'titlebg'}" colspan="4"><b><a href="$cgi;action=newgroup"><font size=2 class="imgmenu" color="$color{'titletext'}">$mcctxt{'33'}  </font></a></b>
				<b><font size=2 class="text1" color="$color{'titletext'}">$mcctxt{'25'}</font></b></td></tr><tr>~;

		my $action = 'managegroups';
		addlisttitle('x',qq~<b><font size=2>$mcctxt{'90'}</font></b>~,$order,$action,$column,80,0,1);
		addlisttitle(0,qq~<b><font size=2>$mcctxt{'21'}</font></b>~,$order,$action,$column);
		addlisttitle(2,qq~<b><font size=2>$mcctxt{'30'}</font></b>~,$order,$action,$column);
		addlisttitle(3,qq~<b><font size=2>$mcctxt{'22'}</font></b>~,$order,$action,$column);
		addlisttitle(1,qq~<b><font size=2>$mcctxt{'23'}</font></b>~,$order,$action,$column);
		$subsmain .= qq~</tr>~;

	while(($numshown < $ItemsPerPage) and $b<$count) {
		$numshown++;
		my @record = get_group_info($list[$b]);
		my $img = $record[1]==1
			? qq~<img src="$imagesdir/on.gif" alt="yes" border="0">~
			: qq~<img src="$imagesdir/off.gif" alt="no" border="0">~;
		$subsmain .= qq~
			<tr>
			  <td class="windowbg" bgcolor="$color{'windowbg'}" align="center" width=80><font size=1><a href="$cgi;action=mailgroup;group=$record[0]"><img src="$imagesdir/message.gif" alt="yes" border="0"></a></font></td>
				<td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><a href="$cgi;action=editgroup;group=$record[0]">$record[0]</a></font></td>
 				<td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2>$record[2]</font></td>
				<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size=2>$record[3]</font> </td>
				<td class="windowbg2" bgcolor="$color{'windowbg2'}" align="center">$img</td>
			</tr>
			~;
		$b++;
	}
	$subsmain .= qq~</table>~;
	if ($numend==0) {$numbegin=0;}
  $subsmain .= page_index($start,"managegroups;sort=$column;order=$order",$count);
	$substitle = "$mcctxt{'25'} $numbegin $txt{'311'} $numend";
	template();
}

#MemberList
sub MemberList {
	my $column = $INFO{"sort"} || 0;
	my $order = $INFO{"order"} || 0;
	my @membername = (get_member_sort_keys(!$order,$column));
	MemberListRef(\@membername,$column,$order,'memberlist');
}

sub MemberListRef {
	my ($listref,$column,$order,$action) = @_;
	if(!defined @memberData) { &fatal_error("$txt{'223'}"); }

	# Get the number of members
	my @membername = @$listref;
	my $memcount = ($#membername)+1;

	if($INFO{'start'} eq "") { $start=0; } else { $start="$INFO{'start'}"; }
	my $numshown=0;
	my $numbegin = ($start + 1);
	my $numend = ($start + $ItemsPerPage);
	if($numend > $memcount) { $numend = $memcount; }
	my $b = $start;

	$subsmain .= qq~
	  <center><font size="2" class="nav"><b>$txt{'308'} $numbegin $txt{'311'} $numend ($txt{'309'} $memcount $txt{'310'})</b></font></center><BR>
		<table border=0 width=100% cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor">
		<tr>
		~;
		if ($memberData[7]>0) {
	 		$subsmain .= qq~
	 	 	<td class="titlebg" bgcolor="$color{'titlebg'}"><b><a href="$cgi;action=newmember"><font size=2 class="imgmenu" color="$color{'titletext'}">$mcctxt{'33'}  </font></a></b>
	 	 	<b><font size=2 class="text1" color="$color{'titletext'}">$txt{'331'}</font></b></td>
	 	 	~
	 } else {
		 $subsmain .= qq~
			<td class="titlebg" bgcolor="$color{'titlebg'}" colspan="3"><b><font size=2 class="text1" color="$color{'titletext'}">$txt{'331'}</font></b></td>
     ~;
   }
    $subsmain .= qq~</tr><tr>~;
		addlisttitle(0,qq~<b><font size=2>$txt{'35'}</font></b>~,$order,$action,$column);
    addlisttitle(8,qq~<b><font size=2>$mcctxt{'15'}</font></b>~,$order,$action,$column);
    addlisttitle(9,qq~<b><font size=2>$txt{'69'}</font></b>~,$order,$action,$column);
    addlisttitle(5,qq~<b><font size=2>$mcctxt{'69'}</font></b>~,$order,$action,$column);
    addlisttitle(-5,qq~<b><font size=2>$mcctxt{'135'}</font></b>~,$order,$action,$column);
    addlisttitle(3,qq~<b><font size=2>$mcctxt{'23'}</font></b>~,$order,$action,$column);
		$subsmain .= qq~</tr>~;

	while(($numshown < $ItemsPerPage and $b<$memcount)) {
		$numshown++;
		$pages="";
		my @member = get_member_info($membername[$b]);
		my ($date,$count,$lastdate) = split(/-/,$member[5]);
		my $img = $member[3]==1
					? qq~<img src="$imagesdir/on.gif" alt="yes" border="0">~
			: qq~<img src="$imagesdir/off.gif" alt="no" border="0">~;
		if ($member[0] eq $memberData[0] or $memberData[7]>0) {
			$subsmain .= qq~
				<tr>
					<td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><a href="$cgi;action=profile;username=$member[0]">$member[0]</a></font></td>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size=2>$member[8]</font></td>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size=2><a href="mailto:$member[9]">$member[9]</a></font></td>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size=2>$date</font></td>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size=2>$count</font></td>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}" align="center">$img</td>
				</tr>
				~;
		} else {
			$subsmain .= qq~
				<tr>
					<td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2>$member[0]</a></font></td>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size=2>$member[8]</a></font></td>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size=2>$mcctxt{'16'}</a></font></td>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size=2>$date - $count</font></td>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}" align="center">$img</td>
			</tr>
			 ~;
		 }
		$b++;
	}
	$subsmain .= qq~</table>~;
  if ($numend==0) {$numbegin=0;}
  $subsmain .= page_index($start,"$action;sort=$column;order=$order",$memcount);
	$substitle = "$txt{'308'} $numbegin $txt{'311'} $numend";
	&template;
}


#A list with all areas
sub AreaList {
	my $column = $INFO{"sort"} || 0;
	my $order = $INFO{"order"} || 0;
	my @list= (get_area_sort_keys(!$order,$column));
  my $count = ($#list)+1;

	my $start = $INFO{'start'} || 0;
	my $numshown=0;
	my $numbegin = ($start + 1);
	my $numend = ($start + $ItemsPerPage);
	if($numend > $memcount) {
		$numend = $count;
	}
	my $b = $start;
	$subsmain .= qq~
			<center><font size="2" class="nav"><B>$mcctxt{'28'} $numbegin $txt{'311'} $numend ($txt{'309'} $count $mcctxt{'29'})</B></font></center><BR>
			<table border=0 width=100% cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor">
				<tr>
					<td class="titlebg" bgcolor="$color{'titlebg'}"><b><a href="$cgi;action=newarea"><font size=2 class="imgmenu" color="$color{'titletext'}">$mcctxt{'33'}   </font></a></b>
					<b><font size=2 class="text1" color="$color{'titletext'}">$mcctxt{'24'}</font></b></td>
		    </tr>
				<tr>~;
	my $action = 'manageareas';
	addlisttitle(0,qq~<b><font size=2>$mcctxt{'21'}</font></b>~,$order,$action,$column);
	addlisttitle(4,qq~<b><font size=2>$mcctxt{'30'}</font></b>~,$order,$action,$column);
	addlisttitle(5,qq~<b><font size=2>$mcctxt{'22'}</font></b>~,$order,$action,$column);
	addlisttitle(1,qq~<b><font size=2>$mcctxt{'23'}</font></b>~,$order,$action,$column);
	$subsmain .= qq~</tr>~;

	while(($numshown < $ItemsPerPage) and $b<$count) {
		$numshown++;
		my @record = get_area_info($list[$b]);
		my $img = $record[1]==1
			? qq~<img src="$imagesdir/on.gif" alt="yes" border="0">~
			: qq~<img src="$imagesdir/off.gif" alt="no" border="0">~;
		$subsmain .= qq~
			<tr>
 				<td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><a href="$cgi;action=editarea;area=$record[0]">$record[0]</a></font></td>
 				<td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2>$record[4]</font></td>
				<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size=2>$record[5]</font> </td>
				<td class="windowbg2" bgcolor="$color{'windowbg2'}" align="center">$img</td>
			</tr>
			~;
		$b++;
	}
	$subsmain .= qq~</table>~;
	if ($numend==0) {$numbegin=0;}
	$subsmain .= page_index($start,"manageareas;sort=$column;order=$order",$count);
	$substitle = "$mcctxt{'24'} $numbegin $txt{'311'} $numend";
	template();
}

sub page_location {
	my ($key,@list) = @_;
	my $start = 0;
	my $length=($#list)+1;
	while ($start<$length) {
		if ($list[$start] eq $key) {
			return ( int( $start / $ItemsPerPage ) ) * $ItemsPerPage;
		}
		$start++;
  }
  return 0;
}

#Create page index at bottom
sub page_index {
	  my ($start,$action,$count,$column) = @_;
	  my ($pageindex,$pageindexadd,$startpage,$endpage,$counter,$max);
		# Build the page links list.
		my $postdisplaynum = 8;	# max number of pages to display
		$max = $count;
		$start = ( int( $start / $ItemsPerPage ) ) * $ItemsPerPage;
		my $tmpa = 1;
		my $tmpx = int( $max / $ItemsPerPage );
		if ($start >= (($postdisplaynum-1) * $ItemsPerPage)) {
			$startpage = $start - (($postdisplaynum-1) * $ItemsPerPage);
			$tmpa = int( $startpage /$ItemsPerPage ) + 1;
		} else {
			$startPage=0;
		}
		if ($max >= $start + ($postdisplaynum * $ItemsPerPage)) {
			$endpage = $start + ($postdisplaynum * $ItemsPerPage);
		} else {
			$endpage = $max
		}
		if ($startpage > 0) {
			$pageindex = qq~<a href="$cgi;action=$action;column=$column;start=0">1</a> ... ~;
		}
		if ($startpage == $ItemsPerPage) {
			$pageindex = qq~<a href="$cgi;action=$action;column=$column;start=0">1</a> ~;
		}
		for( $counter = $startpage; $counter < $endpage; $counter += $ItemsPerPage ) {
			$pageindex .= $start == $counter
					? qq~<b>$tmpa</b> ~
					: qq~<a href="$cgi;action=$action;column=$column;start=$counter">$tmpa</a> ~;
			$tmpa++;
		}
		$tmpx = $max - $ItemsPerPage;
		my $outerpn = int($tmpx / $ItemsPerPage) + 0;
		my $lastpn = int($count / $ItemsPerPage) + 1;
		my $lastptn = ($lastpn - 1) * $ItemsPerPage;
		if ($endpage < $max - ($ItemsPerPage) ) {
			$pageindexadd = qq~ ... ~;
		}
		if ($endpage != $max) {
			$pageindexadd .= qq~ <a href="$cgi;action=$action;column=$column;start=$lastptn">$lastpn</a>~;}
		$pageindex .= $pageindexadd;
    return 	qq~
				<table border=0 width=100% cellpadding=0 cellspacing=0>
				<tr>
					<td><font size=2><b>$txt{'139'}:</b>$pageindex</font>
					</td>
				</tr>
				</table>
			~;
}


sub RequestList {
	is_admin();
	#message_page($mcctxt{'31'},$mcctxt{'32'});

	my @list = ();
	foreach my $key (get_member_keys()) {
		my @record = get_member_info($key);
		if ($record[6] ne '') {
		  @list = ($record[0],@list);
		}
	}
	@list = sort @list;
	my $count = ($#list)+1;

	my $start = $INFO{'start'} || 0;
	my $numshown=0;
	my $numbegin = ($start + 1);
	my $numend = ($start + $ItemsPerPage);
	if($numend > $memcount) {
		$numend = $count;
	}
	my $b = $start;
	$subsmain .= qq~
			<center><font size="2" class="nav"><B>$mcctxt{'73'} $numbegin $txt{'311'} $numend ($txt{'309'} $count $mcctxt{'74'})</B></font></center><BR>
			<table border=0 width=100% cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor">
				<tr>
					<td class="titlebg" bgcolor="$color{'titlebg'}" colspan="3"><b><font size=2 class="text1" color="$color{'titletext'}">$mcctxt{'72'}</font></b></td>
				</tr>
				<tr>
					<td class="catbg" bgcolor="$color{'catbg'}" width="250" ><b><font size=2>$mcctxt{'34'}</font></b></td>
					<td class="catbg" bgcolor="$color{'catbg'}" ><b><font size=2>$mcctxt{'24'}</font></b></td>
					<td class="catbg" bgcolor="$color{'catbg'}" ><b><font size=2>$mcctxt{'23'}</font></b></td>
				</tr>
	~;
	while(($numshown < $ItemsPerPage) and $b<$count) {
		$numshown++;
		my @record = get_member_info($list[$b]);
		my $img = $record[3]==1
			? qq~<img src="$imagesdir/on.gif" alt="yes" border="0">~
			: qq~<img src="$imagesdir/off.gif" alt="no" border="0">~;
		my $line = $record[6];
		$line =~s~\+~ ~;
		$subsmain .= qq~
			<tr>
				<td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><a href="$cgi;action=profile;username=$record[0];requestlist=1">$record[0]</a></font></td>
				<td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2>$line</font></td>
				<td class="windowbg2" bgcolor="$color{'windowbg2'}" align="center">$img</td>
			</tr>
			~;
		$b++;
	}
	$subsmain .= qq~</table>~;
	if ($numend==0) {$numbegin=0;}
	$subsmain .= page_index($start,"managerequest",$count);
	$substitle = "$mcctxt{'72'} $numbegin $txt{'311'} $numend";
	template();
}

1;