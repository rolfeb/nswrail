sub Editor {
	is_admin();
	my ($title,$msg,$filename,$isHtml) = @_;
	my($line,$lines);
	$subsmain .= qq~
	<form action="$cgi;action=editor2" method="post">
	<table border="0" width="95%" cellspacing="1" cellpadding="3" bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
		<tr>
			<td class="titlebg" bgcolor="$color{'titlebg'}">
			<img src="$imagesdir/xx.gif" alt="">
			<font size=2 class="text1" color="$color{'titletext'}"><b>$title ($filename)</b></font></td>
		</tr><tr>
			<td class="windowbg" bgcolor="$color{'windowbg'}"><BR><font size="1">$msg</font><BR><BR></td>
		</tr><tr>
			<td class="windowbg2" bgcolor="$color{'windowbg2'}" align="center"><BR>
			<font size=2>
			<textarea cols=100 rows=20 name="data">~;
		fopen(FILE, "$filename");
		if ($isHtml) {
			while( $line = <FILE> ) {
					$line =~ s~[\r\n]~~g;
					&FromHTML;
					$lines .= qq~$line\n~;
			}
	  } else {
			while( $line = <FILE> ) {
  		   $lines .= $line;
			}
  	}
		$subsmain .= qq~$lines~;
		fclose(FILE);
		$subsmain .= qq~</textarea><br><input type="submit" value="$txt{'10'}"></font><BR></td>
		</tr>
	</table>
	<input type="hidden" name="filename" value="$filename">
	<input type="hidden" name="ishtml" value="$isHtml">
	</form>
	~;
	$substitle = "$title";
	template();
}

sub Editor2 {
	is_admin();
	fopen(FILE, ">$FORM{'filename'}", 1);
	$FORM{'data'} =~ tr/\r//d;
  $FORM{'data'} =~ s~\A\n~~;
  $FORM{'data'} =~ s~\n\Z~~;
	print FILE "$FORM{'data'}";
	fclose(FILE);
	&redirectexit("$cgi;action=admin");
}

sub EditNews {
	Editor($txt{'7'},$txt{'670'},"$datadir/news.txt",0);
}


sub EditRegisterText {
	Editor($mcctxt{'111'},$mcctxt{'112'},"$datadir/register.txt",0);
}

sub EditRedirectScript {
	Editor($mcctxt{'102'},$mcctxt{'103'},"$datadir/AreaRedirect.js",0);
}


sub EditFlexprofile {
	Editor($mcctxt{'92'},$mcctxt{'93'},"$datadir/flexprofile.cfg",0);
}

sub EditWelcome {
	Editor($mcctxt{'86'},$mcctxt{'88'},"$datadir/welcome.txt",0);
}

sub EditHtaccess {
	Editor($mcctxt{'117'},$mcctxt{'118'},"$datadir/htatemplate.txt",0);
}

sub EditReminder {
	Editor($mcctxt{'136'},$mcctxt{'137'},"$datadir/reminder.txt",0);
}

sub EditActivationCode {
	Editor($mcctxt{'138'},$mcctxt{'137'},"$datadir/activationcode.txt",0);
}

sub ModifyTemplate {
	my( $fulltemplate);
	if ($INFO{'type'} eq 'small') {
		Editor($txt{'216'},$txt{'682'},"$datadir/stemplate.html",1);
	} else {
		Editor($txt{'216'},$txt{'682'},"$datadir/template.html",1);
	}
}

sub DeactivateMembers {
	is_admin();
		$subsmain .= qq~
<form action="$cgi;action=deactivatemembers2" method="post">
<table border=0 width="100%" cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" cellpadding="4">
  <tr>
    <td class="titlebg" bgcolor="$color{'titlebg'}">
    <img src="$imagesdir/xx.gif" alt="" border="0">
    <font size=2 class="text1" color="$color{'titletext'}"><b>$mcctxt{'81'}</b></font></td>
     </tr><tr>
   		<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'82'}</font></td>
 			<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="days" size="5" value="90"></td>
	   </tr><tr>
		   <td bgcolor="$color{'windowbg2'}" class="windowbg2" colspan="4">
		   <center>
		    <textarea cols=80 rows=9 name="message">$mcctxt{'85'}</textarea>
		    </center><td>
	   </tr><tr>

    <td><BR><center><input type=submit value="$mcctxt{'83'}"></center></td>
  </tr>
</table>
</form>
~;
	$substitle = "$txt{'81'}";
	&template;
}

sub DeactivateMembers2 {
	is_admin();
	$days = $FORM{'days'};
	if (!defined $days) {$days = 90;}
	my ($curdate) = get_date_value(get_date()) - $days;
	foreach my $key (get_member_keys()) {
		my @record = get_member_info($key);
		my $mdate = get_date_value($record[5]);
		if ($mdate <= $curdate) {
			set_member_state($record[0],0);
			set_activation_code($record[0],salt(8));
			if ($FORM{'message'}) {
				&sendmail( $record[9], "$mbname: $mcctxt{'84'}",
				"$FORM{'message'}\n\n$txt{'130'}\n\n$cgi;action=activate;member=$record[0];code=$record[4]");
				}
		 }
	}
  commit_database();
  redirectexit("$cgi;action=admin");
}


sub ModifySettings {
	is_admin();
	my $url = $rooturl;
	$url =~ s`/cgi-bin``;
  $imagesdir = $imagesdir || "$url/images";
	$helpfile = $orghelpfile || "$url/help/index.html";
  $faderpath = $faderpath || "$url/fader.js";
  my %checked=(0=>'',1=>' checked ','0'=>'','1',' checked ');
	if ($use_flock == 0) { $fls1 = " selected" } elsif ($use_flock == 1) { $fls2 = " selected" } elsif ($use_flock == 2) { $fls3 = " selected" }
	if ($crypt_method == 0) { $cls1 = " selected" } elsif ($crypt_method == 1) { $cls2 = " selected" } elsif ($crypt_method == 2) { $cls3 = " selected" }
	if ($mailtype == 0) { $mts1 = ' selected'; } elsif ($mailtype == 1) { $mts2 = ' selected'; } elsif( $mailtype == 2 ) { $mts3 = ' selected'; }
	if ($servertype == 0) { $st1 = " selected"; } elsif ($servertype == 1) { $st2 = " selected"; }


	$subsmain .= qq~
		<form action="$cgi;action=modsettings2" method="post">
			<table width="90%" border="0" cellspacing="1" cellpadding="0" bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
				<tr>
				<td>
				<table border="0" cellspacing="0" cellpadding="4" align="center">
					<tr>
						<td class="titlebg" bgcolor="$color{'titlebg'}" colspan=2>
						<img src="$imagesdir/preferences.gif" alt="" border="0">
						<font size=2 class="text1" color="$color{'titletext'}"><b>$txt{'222'}</b></font></td>
					</tr><tr>
						<td class="windowbg" bgcolor="$color{'windowbg'}" colspan=2><BR><font size="1">$txt{'347'}</font><BR><BR></td>
					</tr><tr>
						<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'350'}</font></td>
						<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="mbname" size="35" value="$mbname"></td>
					</tr><tr>
						<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'351'}</font></td>
						<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="rooturl" size="45" value="$rooturl"></td>
					</tr><tr>
						<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'349'}</font></td>
						<td class="windowbg2" bgcolor="$color{'windowbg2'}"><select name="language">
		~;
							#langauge
							opendir(DIR, "$rootdir") || die "$mcctxt{'120'} ($rootdir) :: $!";
							@contents = readdir(DIR);
							closedir(DIR);
							foreach $line (@contents){
								($name, $extension) = split (/\./, $line);
								if ($extension eq "lng"){
									$selected = "";
									if ($line eq $language) { $selected = " selected" }
									$subsmain .= "<option value=\"$line\"$selected>$name\n";
								}
							}
				$subsmain .= qq~
					</select></td>
						</tr><tr>
						<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'134'}</font></td>
						<td class="windowbg2" bgcolor="$color{'windowbg2'}"><select name="databasedriver">
		~;
							#database
							opendir(DIR, "$sourcedir") || die "$mcctxt{'120'} ($sourcedir) :: $!";
							@contents = readdir(DIR);
							closedir(DIR);
							foreach $line (@contents){
								($name, $extension) = split (/\./, $line);
								if ($extension eq "dbd"){
									$selected = "";
									if ($line eq $databasedriver) { $selected = " selected" }
									$subsmain .= "<option value=\"$line\"$selected>$name\n";
								}
							}
				$subsmain .= qq~
					</select></td>
						</tr><tr>
							<td colspan=2 class="windowbg2" bgcolor="$color{'windowbg2'}">
							<HR size=1 width="100%" class="hr"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'432'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="cookielength" size="5" value="$Cookie_Length"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'352'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="cookieusername" size="20" value="$cookieusername"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'353'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="cookiepassword" size="20" value="$cookiepassword"></td>
						</tr><tr>
							<td colspan=2 class="windowbg2" bgcolor="$color{'windowbg2'}">
							<HR size=1 width="100%" class="hr"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'55'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="shownotauthorized" $checked{$shownotauthorized}></td>
					  </tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'56'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="logging" $checked{$logging}></td>
					  </tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'71'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="defaultmemberstate" $checked{$defaultmemberstate}></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'87'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="sendwelcome" $checked{$sendwelcome}></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'109'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="allonregister" $checked{$allonregister}></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'116'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="hidememberlist" $checked{$hidememberlist}></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'123'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="disableregister" $checked{$disableregister}></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'128'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="allowdoubleemail" $checked{$allowdoubleemail}></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'129'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="personalemail" $checked{$personalemail}></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'152'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="useaddon" $checked{$useaddon}></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'58'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="defaultgroups" size="40" value="$defaultgroups"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'151'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="maxmemberlength" size="5" value="$maxmemberlength"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'59'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="areasFile" size="20" value="$areasFile"></td>
						</tr><tr>
						  <td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'60'}</font></td>
						  <td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="groupsFile" size="20" value="$groupsFile"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'61'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="membersFile" size="20" value="$membersFile"></td>
						</tr><tr>
							<td colspan=2 class="windowbg2" bgcolor="$color{'windowbg2'}">
							<HR size=1 width="100%" class="hr"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'354'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="mailprog" size="20" value="$mailprog"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'407'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="smtp_server" size="20" value="$smtp_server"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'355'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="webmaster_email" size="35" value="$webmaster_email"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'404'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}">
							<select name="mailtype" size=1>
							<option value="0"$mts1>$txt{'405'}
							<option value="1"$mts2>$txt{'406'}
							<option value="2"$mts3>Net::SMTP
							</select></td>
						</tr><tr>
							<td colspan=2 class="windowbg2" bgcolor="$color{'windowbg2'}">
							<HR size=1 width="100%" class="hr"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'356'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="rootdir" size="30" value="$rootdir"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'360'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="sourcedir" size="30" value="$sourcedir"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'119'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="datadir" size="30" value="$datadir"></td>
						</tr><tr>
						  <td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'121'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="addondir" size="30" value="$addondir"></td>
						</tr><tr>
						  <td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'363'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="imagesdir" size="45" value="$imagesdir"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'390'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="faderpath" size="45" value="$faderpath"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'364'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="helpfile" size="45" value="$helpfile"></td>
						</tr><tr>
							<td colspan=2 class="windowbg2" bgcolor="$color{'windowbg2'}">
							<HR size=1 width="100%" class="hr"><b><font size="1">$txt{'784'}</font></b></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'365'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="titlebg" size="10" value="$color{'titlebg'}"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'366'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="titletext" size="10" value="$color{'titletext'}"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'367'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="windowbg" size="10" value="$color{'windowbg'}"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'368'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="windowbg2" size="10" value="$color{'windowbg2'}"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'640'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="windowbg3" size="10" value="$color{'windowbg3'}"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'369'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="catbg" size="10" value="$color{'catbg'}"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'370'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="bordercolor" size="10" value="$color{'bordercolor'}"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'389'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="fadertext" size="10" value="$color{'fadertext'}"></td>
						</tr><tr>
							<td colspan=2 class="windowbg2" bgcolor="$color{'windowbg2'}">
							<HR size=1 width="100%" class="hr"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'387'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="shownewsfader" $checked{$shownewsfader}></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'388'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="fadertime" size="5" value="$fadertime"></td>
						</tr><tr>
							<td colspan=2 class="windowbg2" bgcolor="$color{'windowbg2'}">
							<HR size=1 width="100%" class="hr"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'373'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="ItemsPerPage" size="5" value="$ItemsPerPage"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'80'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="maxinactive" size="5" value="$maxinactive"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'106'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="smalltarget" size="40" value="$smalltarget"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'108'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="passwordtext" size="40" value="$passwordtext"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'107'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><textarea cols=45 rows=3 name="passwordfilter">$passwordfilter</textarea></td>
						</tr><tr>
							<td colspan=2 class="windowbg2" bgcolor="$color{'windowbg2'}">
							<HR size=1 width="100%" class="hr"></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'391'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}">
							<select name="use_flock" size=1>
							<option value="0"$fls1>$txt{'401'}
							<option value="1"$fls2>$txt{'402'}
							<option value="2"$fls3>$txt{'403'}
							</select></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'113'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}">
							<select name="cryptmethod" size=1>
							<option value="0"$cls1>$mcctxt{'114'}
							<option value="1"$cls2>$mcctxt{'115'}
							<option value="2"$cls3>$mcctxt{'133'}
							</select></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$txt{'630'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="faketruncation" $checked{$faketruncation}></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'122'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=checkbox name="traperrors" $checked{$traperrors}></td>
						</tr><tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><font size="2">$mcctxt{'146'}</font></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}"><input type=text name="timeoffset" size="3" value="$timeoffset"></td>
						</tr>~;
					addOnExecute("SettingsPage");
					$subsmain .= qq~</table>
					</td>
					</tr><tr>
						<td class="windowbg" bgcolor="$color{'windowbg'}" colspan="2" align="center" valign="middle">
						<BR><input type=submit value="$txt{'10'}">
						</td>
					</tr>
				</table>
				</form>
				~;
	$substitle = $txt{'222'};
	&template;
}

sub ModifySettings2 {
	&is_admin;

	my @onoff = qw/
		 logging shownotauthorized defaultmemberstate sendwelcome allonregister shownewsfader hidememberlist faketruncation traperrors disableregister allowdoubleemail personalemail useaddon/;
  #push(@onoff, "tsreverse");

	# Set as 0 or 1 if box was checked or not
	my $fi;
	map { $fi = lc $_; ${$_} = $FORM{$fi} eq 'on' ? 1 : 0; } @onoff;

	# If empty fields are submitted, set them to default-values to save yabb from crashing
	$servertype = $FORM{'servertype'} || 0;
	$ItemsPerPage = $FORM{'ItemsPerPage'} || 20;
	$timeoffset = $FORM{'timeoffset'} || 0;
	$use_flock = $FORM{'use_flock'} || 0;
	$Cookie_Length = $FORM{'cookielength'} || 60;
	$cookieusername = $FORM{'cookieusername'} || 'cookieUsername';
	$cookiepassword = $FORM{'cookiepassword'} || 'cookiePassword';
	if ($cookieusername eq $cookiepassword) {$cookieusername = 'cookieUsername'; $cookiepassword = 'cookiePassword';}
	$language = $FORM{'language'} || 'english.lng';
	$databasedriver = $FORM{'databasedriver'} || 'TextFiles.dbd';
	$mbname = $FORM{'mbname'} || 'My Member Control Centre';
	$mbname =~ s/\"/\'/g;
	$rooturl = $FORM{'rooturl'} || &GetMccURL;
	$rootdir = $FORM{'rootdir'} || &GetDirPath;
	$datadir = $FORM{'datadir'} || "$rootdir/data";
	$addondir = $FORM{'addondir'} || "$rootdir/addon";
	$sourcedir = $FORM{'sourcedir'} || "$rootdir/sources";
	$imagesdir = $FORM{'imagesdir'} || "$rooturl/images";
	$helpfile = $FORM{'helpfile'} || "$rooturl/help/index.html";
	$mailprog = $FORM{'mailprog'} || '/usr/lib/sendmail';
	$smtp_server = $FORM{'smtp_server'} || '127.0.0.1';
	$maxmemberlength = $FORM{'maxmemberlength'} || 30;
	$webmaster_email = $FORM{'webmaster_email'} || 'webmaster@mysite.com';
	$mailtype = $FORM{'mailtype'} || 0;
	$color{'titlebg'} = $FORM{'titlebg'} || '#6E94B7';
	$color{'titletext'} = $FORM{'titletext'} || '#FFFFFF';
	$color{'windowbg'} = $FORM{'windowbg'} || '#AFC6DB';
	$color{'windowbg2'} = $FORM{'windowbg2'} || '#F8F8F8';
	$color{'windowbg3'} = $FORM{'windowbg3'} || '#6394BD';
	$color{'catbg'} = $FORM{'catbg'} || '#DEE7EF';
	$color{'bordercolor'} = $FORM{'bordercolor'} || '#6394BD';
	$color{'fadertext'} = $FORM{'fadertext'} || '#D4AD00';
	$faderpath = $FORM{'faderpath'} || "$rooturl/fader.js";
	$membersFile = $FORM{'membersFile'} || "$datadir/members.db";
  $areasFile = $FORM{'areasFile'} || "$datadir/areas.db";
  $groupsFile = $FORM{'groupsFile'} || "$datadir/groups.db";
 	$fadertime = $FORM{'fadertime'} || 5000;
 	$defaultgroups = $FORM{'defaultgroups'};
	$maxinactive = $FORM{'maxinactive'} || 30;
	$smalltarget = $FORM{'smalltarget'} || '_blank';
	my $old_method = $crypt_method;
  $crypt_method = $FORM{'cryptmethod'} || 0;
  if ($old_method != $crypt_method) {
		#Create all password and protected areas again
		update_all_member_password();
  }
	$passwordfilter = $FORM{'passwordfilter'} || q<\A[\s0-9A-Za-z!@#$%\^&*\(\)_\+|`~\-=\\:;\'\",\.\/?\[\]\{\}]{5,15}\Z>;
  $passwordtext = $FORM{'passwordtext'};
  SaveSettings();
	redirectexit("$cgi;action=admin");
}

sub SaveSettings {
	$addonsettings = '';
	addOnExecute("SettingsSave");
	my $filler = q~                                                                               ~;
	my $setfile = << "EOF";
###############################################################################
# Settings.pl                                                                 #
###############################################################################

\$language = "$language";				# Change to language pack you wish to use
\$mbname = "$mbname";					# The name of your site
\$databasedriver = "$databasedriver"; #The name of the database driver

########## Files ##########
#
\$membersFile ="$membersFile";      # Name of the file containing the members
\$areasFile  ="$areasFile";      		# Name of the file containing the areas
\$groupsFile ="$groupsFile";				# Name of the file containing the groups

########## Cookie ##########
#
\$Cookie_Length = $Cookie_Length;				# Default minutes to set login cookies to stay for
\$cookieusername = "$cookieusername";			# Name of the username cookie
\$cookiepassword = "$cookiepassword";			# Name of the password cookie

########## Mail ##########
#
\$mailprog = "$mailprog";				# Location of your sendmail program
\$smtp_server = "$smtp_server";				# Address of your SMTP-Server
\$webmaster_email = q^$webmaster_email^;		# Your email address. (eg: \$webmaster_email = q^admin\@host.com^;)
\$mailtype = $mailtype;					# Mail program to use: 0 = sendmail, 1 = SMTP, 2 = Net::SMTP
\$sendwelcome = $sendwelcome;		# Send welcome message to new users

########## Directories ##########
# Note: directories other than \$imagesdir do not have to be changed unless you move things
\$rootdir = "$rootdir"; 				# The server path to the MCC folder (usually can be left as '.')
\$sourcedir = "$sourcedir";     # Directory with source files
\$imagesdir = "$imagesdir";				# URL to your images folder
\$faderpath = "$faderpath";				# URL to your 'fader.js'
\$helpfile = "$helpfile";				# URL to your help file
\$rooturl = "$rooturl";					# URL to CGI directory with MCC.PL
\$datadir = "$datadir";					# The server path to editable data files
\$addondir = "$addondir";       # The server path to add-on

########## Colors ##########
# Note: equivalent to colors in CSS tag of template.html, so set to same colors preferrably
# for browsers without CSS compatibility and for some items that don't use the CSS tag
\$color{'titlebg'} = "$color{'titlebg'}";		# Background color of the 'title-bar'
\$color{'titletext'} = "$color{'titletext'}";		# Color of text in the 'title-bar' (above each 'window')
\$color{'windowbg'} = "$color{'windowbg'}";		# Background color for messages/forms etc.
\$color{'windowbg2'} = "$color{'windowbg2'}";		# Background color for messages/forms etc.
\$color{'windowbg3'} = "$color{'windowbg3'}";		# Color of horizontal rules in posts
\$color{'catbg'} = "$color{'catbg'}";			# Background color for category (at Board Index)
\$color{'bordercolor'} = "$color{'bordercolor'}";	# Table Border color for some tables
\$color{'fadertext'}  = "$color{'fadertext'}";		# Color of text in the NewsFader (news color)

########## Layout ##########
\$shownewsfader = $shownewsfader;			# 1 to allow or 0 to disallow NewsFader javascript on the Board Index
\$servertype = $servertype;				# Set to 0 if you are running on a Unix/Linux webhost, set to 1 if you are running on windows.

########## Feature Settings ##########
\$ItemsPerPage = $ItemsPerPage;			# No. of items to display per page of  List - All
\$fadertime = $fadertime;				# Length in milliseconds to delay between each item in the news fader
\$logging = $logging;            #0=Disabled,1=Enabled logging is added to end of html page
\$defaultgroups = "$defaultgroups"; #These groups are assigned to each new member
\$shownotauthorized = $shownotauthorized; #show the not authorized at bottom of default page
\$defaultmemberstate = $defaultmemberstate; #is member active on load
\$maxinactive = $maxinactive;	#Number of days that a member is allowed to stay inactive before removal
\$smalltarget = "$smalltarget";	#Target frame used in small mode for full page options
\$passwordtext="$passwordtext"; #password text
\$passwordfilter= q<$passwordfilter>; #password filter
\$allonregister = $allonregister; #Show all flexfields on register
\$crypt_method  = $crypt_method;  #0- simple crypt,1 - MD5 method (recomended)
\$hidememberlist = $hidememberlist; #Hide memberlist when 1
\$disableregister = $disableregister; #1 will trap errors into html window
\$allowdoubleemail = $allowdoubleemail; #1 if we allow double email address
\$personalemail = $personalemail; #1 if we want to sent personalized emails
\$timeoffset = $timeoffset;          #Timeoffset in hours
\$maxmemberlength = $maxmemberlength;          #Maximum length of member ID
\$useaddon = $useaddon; #Use addon within ModPerl

########## Add On Settings ##########
$addonsettings

########## File Locking ##########
\$use_flock = $use_flock;				# Set to 0 if your server doesn't support file locking,
							# 1 for Unix/Linux and WinNT, and 2 for Windows 95/98/ME
\$faketruncation = $faketruncation;			# Enable this option only if fails with the error:
							# "truncate() function not supported on this platform."
							# 0 to disable, 1 to enable.

########## Error Handling #########
\$traperrors = $traperrors; #1 will trap errors into html window

1;
EOF

	$setfile =~ s~(.+\;)\s+(\#.+$)~$1 . substr( $filler, 0, (70-(length $1)) ) . $2 ~gem;
	$setfile =~ s~(.{64,}\;)\s+(\#.+$)~$1 . "\n   " . $2~gem;
	$setfile =~ s~^\s\s\s+(\#.+$)~substr( $filler, 0, 70 ) . $1~gem;

	if (fopen(SETSH,">$rootdir/Settings.pl")) {
		print SETSH $setfile;
		fclose(SETSH);
	} else {
		 fatal_error("$mcctxt{'1'} $rootdir/Settings.pl $!");
  }
}

1;