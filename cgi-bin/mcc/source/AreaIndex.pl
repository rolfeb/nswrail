#AreaIndex

sub AreaIndexSmall {
	my ($areaaction,$area,$url,$title) = @_;

	if ($areaaction ne '') {
		fopen(FILE,"$datadir/AreaRedirect.js");
		my ($line);
		while ($line = <FILE>) { $subsscripts .= qq~$line~; }
		fclose(FILE);
  }

	if ($memberData[3]==0) {
		$subsmain .= qq~
			<form action="$cgi;action=activate;member=$memberData[0];" method="post" name="form">
					<table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="$color{'bordercolor'}" class="bordercolor">
						<tr>
							<td class="windowbg" bgcolor="$color{'windowbg'}" width="100%">
							<table width="100%" cellspacing="0" cellpadding="3">
								<tr>
									<td class="titlebg" bgcolor="$color{'titlebg'}" colspan="2">
									<font size=2 class="text1" color="$color{'titletext'}"><b>$mcctxt{'62'}</b></font></td>
								</tr><tr>
									<td class="windowbg" bgcolor="$color{'windowbg'}" colspan="2"><font size=1>$mcctxt{'63'}</font></td>
								</tr><tr>
									<td colspan="2" class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><b>$mcctxt{'65'}:</b><BR>
									   <input type=text name="code" size=20 tabindex="2"></font></td>
								</tr><tr>
									<td class="windowbg" bgcolor="$color{'windowbg'}"><a href="$cgi;action=requestcode;member=$memberData[0]"><font size="1">$mcctxt{'64'}</font></a></td>
								</tr><tr>
									<td align=center colspan="2" class="windowbg" bgcolor="$color{'windowbg'}"><BR><input type=submit value="$mcctxt{'66'}" tabindex="5" accesskey="l"></td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
			</form>
			<BR>
			~;
	}
  addOnExecute("HomePage",2);
	$subsmain .= qq~<table border=0 width="100%" cellspacing="0" cellpadding="0" bgcolor="$color{'bordercolor'}" class="bordercolor">
	  <tr>
		<td>
		<table cellpadding="4" cellspacing="1" border="0" width="100%">~;

	#Authorized
	is_authorized($memberData[0]); #Load all areas
	my @autoAreas = (get_area_sort_keys(1,14,1));
	foreach my $curArea (@autoAreas) {
		if (is_authorized($memberData[0],$curArea)!=0) {
			my @area = get_area_info($curArea);
			if ($area[12] ne '1') {  #Hide area
				my $urlaction = ($memberData[3]==1 && $area[1]==1) ? qq~<a href="$cgitarget;action=goto;area=$curArea" target="$smalltarget">$area[4]</a>~ : "";
				$subsmain .= qq~<tr>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}" align="left" width="100%">
					$urlaction</td></tr>~;
			}
		}
	}
	$subsmain .= qq~
		</table>
		</td>
	  </tr>
	</table>
	~;
	addOnExecute("HomePage",3);

	#Make sure we trigger the right load
	if ($areaaction ne '') {
		  $subsmain .= qq~<script language="JavaScript1.2" type="text/javascript">\n<!--
		  function dummy_redirect() {
		    AreaRedirect("$areaaction","$area","$url","$title","$smalltarget");
		  }
		  window.onload = dummy_redirect\n // -->\n</script>\n~;
	}
	$subtitle = "$txt{'18'}";
	template(1);
}

sub AreaIndex {
	if ($smalltemplate) {AreaIndexSmall(@_)};
	my ($areaaction,$area,$url,$title) = @_;

	if ($areaaction) {
		fopen(FILE,"$datadir/AreaRedirect.js");
		my ($line);
		my $on = 0;
		my $off = 0;
		my $s1 =lc("\/\/redirect.$area=on");
		my $s2 =lc("\/\/redirect.all=on");
		my $s3 =lc("\/\/redirect.$area=off");
		while ($line = <FILE>) {
			$subsscripts .= qq~$line~;
			$_ = lc($line);
			$on |= (m/$s1/) || (m/$s2/);
			$off |= (m/$s3/);
		}
		fclose(FILE);
		if ($on and !$off) {
			print header(-status=>'200 OK', -charset=>"$subscharset");
			print qq~<html><head><title>$title</title></head>
			    <body onload='AreaRedirect("$areaaction","$area","$url","$title","_self");'>
			    $subsscripts <a href=$url>$title</a></body></html>~;
			Core:exit(0);
		}
  }

	if ($memberData[3]==0) {
		$subsmain .= qq~
			<form action="$cgi;action=activate;member=$memberData[0];" method="post" name="form">
					<table border="0" width="60%" cellspacing="1" cellpadding="0" bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
						<tr>
							<td class="windowbg" bgcolor="$color{'windowbg'}" width="100%">
							<table width="100%" cellspacing="0" cellpadding="3">
								<tr>
									<td class="titlebg" bgcolor="$color{'titlebg'}" colspan="3">
									<font size=2 class="text1" color="$color{'titletext'}"><b>$mcctxt{'62'}</b></font></td>
								</tr><tr>
									<td class="windowbg" bgcolor="$color{'windowbg'}" colspan="3"><font size=2>$mcctxt{'63'}</font></td>
								</tr><tr>
									<td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><b>$mcctxt{'65'}:</b></font></td>
									<td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2><input type=text name="code" size=20 tabindex="2"></font></td>
									<td class="windowbg" bgcolor="$color{'windowbg'}"><a href="$cgi;action=requestcode;member=$memberData[0]"><font size="1">$mcctxt{'64'}</font></a></td>
								</tr><tr>
									<td align=center colspan="3" class="windowbg" bgcolor="$color{'windowbg'}"><BR><input type=submit value="$mcctxt{'66'}" tabindex="5" accesskey="l"></td>
								</tr><tr>
									<td align=center colspan="3" class="windowbg" bgcolor="$color{'windowbg'}"><BR></td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
			</form>
			<BR><BR>
			~;
	}

  if ($shownewsfader==1) {
		fopen(FILE, "$datadir/news.txt");
		my (@lines) = <FILE>;
		fclose(FILE);
	  news_fader($fadertime,@lines);
  }
  addOnExecute("HomePage",2);
	$subsmain .= qq~<table border=0 width="100%" cellspacing="0" cellpadding="0" bgcolor="$color{'bordercolor'}" class="bordercolor">
	  <tr>
		<td>
		<table cellpadding="4" cellspacing="1" border="0" width="100%">
		  <tr>
			<td class="titlebg" bgcolor="$color{'titlebg'}" colspan="2"><font class="text1" color="$color{'titletext'}" size="2"><b>$mcctxt{'10'}</b></font></td>
			<td class="titlebg" bgcolor="$color{'titlebg'}" width="1%" align="center"><font class="text1" color="$color{'titletext'}" size="2"><b>$mcctxt{'11'}</b></font></td>
			<td class="titlebg" bgcolor="$color{'titlebg'}" width="1%" align="center"><font class="text1" color="$color{'titletext'}" size="2"><b>$mcctxt{'12'}</b></font></td>
			<td class="titlebg" bgcolor="$color{'titlebg'}" width="24%" align="center"><font class="text1" color="$color{'titletext'}" size="2"><b>$mcctxt{'13'}</b></font></td>
		  </tr>~;

	#Authorized
	is_authorized($memberData[0]); #Load all areas
	$subsmain .= qq~<tr>
        <td colspan="5" class="catbg" bgcolor="$color{'catbg'}" height="18"><a name="Authorized"> <font size=2><b>Authorized</b></font></a></td> </tr>~;
	my @sortAreas = (get_area_sort_keys(1,14,1));
	foreach my $curArea (@sortAreas) {
		if (is_authorized($memberData[0],$curArea)!=0) {
			my @area = get_area_info($curArea);
			if ($area[12] ne '1') {  #Hide area
				my $urlaction = ($memberData[3]==1 && $area[1]==1) ? qq~<a href="$cgi;action=goto;area=$curArea">~ : "";
				$subsmain .= qq~<tr>
					<td class="windowbg" bgcolor="$color{'windowbg'}" width="8%" align="center" valign="top"></td>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}" align="left" width="66%">
					<a name="$area[0]"></a>
					<font size=2>$urlaction<B>$area[4]</B></a></font>
					<br><font size="1">$area[5]</font></td>~;
				if ($area[1]==1) {
					 $subsmain .= qq~<td class="windowbg" bgcolor="$color{'windowbg'}" valign="middle" align="center" width="1%"><font size="2"><IMG SRC="$imagesdir/on.gif" BORDER="0" alt="X"></font></td>~;
				} else {
					 $subsmain .= qq~<td class="windowbg" bgcolor="$color{'windowbg'}" valign="middle" align="center" width="1%"><font size="2"><IMG SRC="$imagesdir/off.gif" BORDER="0" alt=""></font></td>~;
				}
				$subsmain .= qq~<td class="windowbg" bgcolor="$color{'windowbg'}" valign="middle" align="center" width="1%"><font size="2"> </font></td>
												<td class="windowbg" bgcolor="$color{'windowbg'}" valign="middle" align="center" width="1%"><font size="1">$area[8]</font></td>
												</tr>~;
			}
		}
	}
	if ($shownotauthorized==1) {
		$subsmain .= qq~<tr>
					<td colspan="5" class="catbg" bgcolor="$color{'catbg'}" height="18"><a name="Not Authorized"> <font size=2><b>Not Authorized</b></font></a></td> </tr>~;
		#Not Authorized
		my %includes = include_index($memberData[6]);
		foreach my $curArea (@sortAreas) {
			if (is_authorized($memberData[0],$curArea)==0) {
				my @area = get_area_info($curArea);
				if ($area[12] ne '1') {  #Hide area
					$subsmain .= qq~<tr>
						<td class="windowbg" bgcolor="$color{'windowbg'}" width="8%" align="center" valign="top"></td>
						<td class="windowbg2" bgcolor="$color{'windowbg2'}" align="left" width="66%">
						<font size=2>~;
					if ($area[15]==1 and $memberData[3]) {
						$subsmain .= qq~<a href="$cgi;action=requestaccess;member=$memberData[0];area=$curArea"><B>$area[4]</B></a>~;
					}else {
						$subsmain .= qq~<B>$area[4]</B>~;
					}
					$subsmain .= qq~</font><br><font size="1">$area[5]</font></td>~;
					if ($area[1]==1) {
						 $subsmain .= qq~<td class="windowbg" bgcolor="$color{'windowbg'}" valign="middle" align="center" width="1%"><font size="2"><IMG SRC="$imagesdir/on.gif" BORDER="0" alt="X"></font></td>~;
					} else {
						 $subsmain .= qq~<td class="windowbg" bgcolor="$color{'windowbg'}" valign="middle" align="center" width="1%"><font size="2"><IMG SRC="$imagesdir/off.gif" BORDER="0" alt=""></font></td>~;
					}
					if (exists $includes{$curArea}) {
						$subsmain .= qq~<td class="windowbg" bgcolor="$color{'windowbg'}" valign="middle" align="center" width="1%"><font size="2"><IMG SRC="$imagesdir/on.gif" BORDER="0" alt="X"></font></td>~;
					} else {
						$subsmain .= qq~<td class="windowbg" bgcolor="$color{'windowbg'}" valign="middle" align="center" width="1%"><font size="2"><IMG SRC="$imagesdir/off.gif" BORDER="0" alt=""></font></td>~;
					}
					 $subsmain .= qq~<td class="windowbg" bgcolor="$color{'windowbg'}" valign="middle" align="center" width="1%"><font size="1">$area[8]</font></td>
													</tr>~;
			  }
			}
		}
  }

	$subsmain .= qq~
		</table>
		</td>
	  </tr>
	</table>
	~;

	addOnExecute("HomePage",3);

	#Make sure we trigger the right load
	if ($areaaction) {
		  $subsmain .= qq~<script language="JavaScript1.2" type="text/javascript">\n<!--
		  function dummy_redirect() {
		    AreaRedirect("$areaaction","$area","$url","$title","_self");
		    if ($shownewsfader == 1) {
					fade();
			  }
		  }
		  window.onload = dummy_redirect\n // -->\n</script>\n~;
	} elsif ($shownewsfader){
		$subsmain .= qq~<script language="JavaScript1.2" type="text/javascript">\n<!--\nwindow.onload = fade\n // -->\n</script>\n~;
  }
	$subtitle = "$mcctxt{'35'}";
	&template;
}

sub RequestAccess {
	my( %member, $key, $value);
	while( ($key,$value) = each(%FORM) ) {
		$value =~ s~\A\s+~~;
		$value =~ s~\s+\Z~~;
		$value =~ s~[\n\r]~~g;
		$member{$key} = $value;
	}

  my ($found,@record) = find_member($INFO{'member'});
  my $area = $member{'area'} || $INFO{'area'};
  my (@arearec) = get_area_info($area);
  if ($arearec[15]==1 and $found and $record[3]) {
			addOnExecute("RequestAccess",$member,$area,includes($area,$memberData[6])==-1);
			toggle_area_request($record[0],$area);
			commit_database();
			loadUserSettings();
	}
  AreaIndex();
}


sub GotoArea {
	my $area =$INFO{'area'};
	my ($found,@record) = find_area($area);
	fatal_error("$mcctxt{'3'} $area") if (!$found);
	fatal_error("$txt{'1'}") if (is_authorized($memberData[0],$area)==0 || !$record[1]);
	my $url = $record[3];
	#Remove http:// if there
	my ($prefix,$path) = split('://',$url);
	if ($path eq '') {
		$prefix="";
		$path=$url;
	} else {
		$prefix.= '://';
	}
	$url= $record[11]==1 ? "${prefix}$memberData[0]:$memberData[1]\@$path" : "${prefix}$path";
	if ($record[10]) { #use script
		AreaIndex($record[9],$record[0],$url,$record[4]);
  } else {
		redirectexit($url);
  }
}

sub news_fader {
	my ($fadedelay,@newsmessages) = @_;
	if(!$fadedelay) { $fadedelay = 5000; }

	$subsmain .= qq~
		<table border=0 width="100%" cellspacing="0" cellpadding="0" bgcolor="$color{'bordercolor'}" class="bordercolor">
			<tr>
				<td>
				<table cellpadding="4" cellspacing="1" border="0" width="100%">
					<tr>
						<td bgcolor="$color{'titlebg'}" class="titlebg" align="center"><font class="text1" color="$color{'titletext'}" size="2"><b>$txt{'102'}</b></font></td>
					</tr><tr>
						<td bgcolor="$color{'windowbg2'}" valign="middle" align="center" height="60">
							<script language="JavaScript1.2" type="text/javascript">
								<!--
								var delay = $fadertime
								var bcolor = "$color{'windowbg2'}"
								var tcolor = "$color{'fadertext'}"
								var fcontent = new Array()
								begintag = '<font size="2"><b>'
								~;
								for($i=0; $i < @newsmessages; $i++) {
									$newsmessages[$i] =~ s/\n|\r//g;
									if($i != 0){ $subsmain .= qq~\n~; }
									$message = $newsmessages[$i];
									$message =~ s/\"/\\\"/g;
									$subsmain .= qq~fcontent[$i] = "$message"~;
								}
								$subsmain .= qq~
									closetag = '</b></font>'
									// -->
									</script>
									<script language="JavaScript1.2" type="text/javascript" src="$faderpath"></script>
									<script language="JavaScript1.2" type="text/javascript">
									<!--
									if (navigator.appVersion.substring(0,1) < 5 && navigator.appName == "Netscape") {
										 var fwidth = screen.availWidth / 2;
										 var bwidth = screen.availWidth / 4;
										 document.write('<ilayer id="fscrollerns" width='+fwidth+' height=35 left='+bwidth+' top=0><layer id="fscrollerns_sub" width='+fwidth+' height=35 left=0 top=0></layer></ilayer>');
									}
									else if (navigator.userAgent.search(/Opera/) != -1 || (navigator.platform != "Win32" && navigator.userAgent.indexOf('Gecko') == -1)) {
										 document.open();
										 document.write('<div id="fscroller" style="width:90% height:15px; padding:2px">');
										 for(i=0; i < fcontent.length; ++i) {
												document.write(begintag+fcontent[i]+closetag+'<br>');
										 }
										 document.write('</div>');
										 document.close();
									}
									else {
										 document.write('<div id="fscroller" style="width:90% height:15px; padding:2px"></div>');
									}
									// -->
								</script>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<BR>
		~;
}
1;