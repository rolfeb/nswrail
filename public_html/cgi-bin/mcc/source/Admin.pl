sub Admin {
	is_admin();

	$subsmain .= qq~
	<script type="text/javascript">
			function ConfirmedAction(msg,action) {
				if (confirm("$mcctxt{'150'}" + msg )) {
					 this.location=action;
				}
		}
	</script>
	<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
		<tr>
			<td valign="top" colspan="3" align="center">
			<table border="0" cellpadding="4" cellspacing="1" bgcolor="$color{'bordercolor'}" class="bordercolor" width="100%">
				<tr>
					<td bgcolor="$color{'titlebg'}" height="24" class="titlebg" align="center"><font size="3"><B>$txt{'208'}</B></font></td>
				</tr>
			</table>
			</td>
		</tr><tr>
			<td valign="top" width="25%"><BR>

			<table border="0" cellpadding="4" cellspacing="1" bgcolor="$color{'bordercolor'}" class="bordercolor" width="100%">
				<tr>
					<td bgcolor="$color{'catbg'}" height="19" class="catbg"><img src="$imagesdir/board.gif" alt="" border="0"> <font size="2"><b>$txt{'428'}</b></font></td>
				</tr><tr>
					<td bgcolor="$color{'windowbg2'}" class="windowbg2"><font size="1">
					- <a href="$cgi;action=modsettings">$txt{'222'}</a><br>
					- <a href="$cgi;action=modtemplate">$mcctxt{'20'}</a><br>
					- <a href="$cgi;action=modtemplate;type=small">$mcctxt{'105'}</a><br>
					</font></td>
				</tr>
			</table><BR>


			<table border="0" cellpadding="4" cellspacing="1" bgcolor="$color{'bordercolor'}" class="bordercolor" width="100%">
				<tr>
					<td bgcolor="$color{'catbg'}" height="19" class="catbg"><img src="$imagesdir/board.gif" alt="" border="0"> <font size="2"><b>$mcctxt{'140'}</b></font></td>
				</tr><tr>
					<td bgcolor="$color{'windowbg2'}" class="windowbg2"><font size="1">~;
			addOnExecute("AdminLine");
			$subsmain .= qq~
					</font></td>
				</tr>
			</table><BR>

			<table border="0" cellpadding="4" cellspacing="1" bgcolor="#6394BD" class="bordercolor" width="100%">
				<tr>
					<td bgcolor="$color{'catbg'}" height="19" class="catbg"><img src="$imagesdir/board.gif" alt="" border="0"> <font size="2"><b>$txt{'426'}</b></font></td>
				</tr><tr>
					<td bgcolor="$color{'windowbg2'}" class="windowbg2"><font size="1">
					- <a href="$cgi;action=memberlist">$txt{'4'}</a><br>
					- <a href="$cgi;action=managegroups">$txt{'3'}</a><br>
		   		- <a href="$cgi;action=managerequests">$txt{'8'}</a><br>
		   		- <a href="$cgi;action=createmail">$txt{'6'}</a><br>
					- <a href="$cgi;action=uploadmembers">$mcctxt{'45'}</a><br>
					- <a href="$cgi;action=removeinactive">$mcctxt{'79'}</a><br>
					- <a href="$cgi;action=deactivatemembers">$mcctxt{'81'}</a><br>
					</font></td>
				</tr>
			</table><BR>

			<table border="0" cellpadding="4" cellspacing="1" bgcolor="$color{'bordercolor'}" class="bordercolor" width="100%">
				<tr>
					<td bgcolor="$color{'catbg'}" height="19" class="catbg"><img src="$imagesdir/board.gif" alt="" border="0"> <font size="2"><b>$txt{'427'}</b></font></td>
				</tr><tr>
					<td bgcolor="$color{'windowbg2'}" class="windowbg2"><font size="1">
					- <a href="$cgi;action=manageareas">$txt{'5'}</a><br>
					- <a href="$cgi;action=generateareas">$mcctxt{'17'}</a><br>
					</font></td>
				</tr>
			</table><BR>

				<table border="0" cellpadding="4" cellspacing="1" bgcolor="$color{'bordercolor'}" class="bordercolor" width="100%">
					<tr>
						<td bgcolor="$color{'catbg'}" height="19" class="catbg"><img src="$imagesdir/board.gif" alt="" border="0"> <font size="2"><b>$mcctxt{'141'}</b></font></td>
					</tr><tr>
						<td bgcolor="$color{'windowbg2'}" class="windowbg2"><font size="1">
						- <a href="$cgi;action=editnews">$txt{'7'}</a><br>
						- <a href="$cgi;action=editwelcome">$mcctxt{'86'}</a><br>
						- <a href="$cgi;action=editflexprofile">$mcctxt{'92'}</a><br>
						- <a href="$cgi;action=editredirectscript">$mcctxt{'102'}</a><br>
						- <a href="$cgi;action=editregistertext">$mcctxt{'111'}</a><br>
						- <a href="$cgi;action=edithtaccess">$mcctxt{'117'}</a><br>
						- <a href="$cgi;action=editreminder">$mcctxt{'136'}</a><br>
						- <a href="$cgi;action=editactivationcode">$mcctxt{'138'}</a><br>
			~;
				 addOnExecute("AdminEditorLine");
				 $subsmain .= qq~</font></td>
					</tr>
  				</table><BR>
			~;

			addOnExecute("AdminMenu");

			$subsmain .= qq~
			</td>

			<td width="6">&nbsp;</td>
			<td valign="top"><BR>

			<table border="0" cellpadding="5" cellspacing="1" align="center" bgcolor="$color{'bordercolor'}" class="bordercolor" width="100%">
				<tr>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}" width="100%">
					<table width="100%" cellpadding="4">
						<tr>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}" valign="middle" align="center" width="50"><img src="$imagesdir/administrator.gif" border="0" alt="" usemap="#egg"></td>
							<td class="windowbg2" bgcolor="$color{'windowbg2'}">
							<font size="1"><B>$txt{'248'} $settings[1] ($username)!</B><BR><BR>
							$txt{'644'}($mccversion)</font></td>
						</tr>
					</table>
					</td>
				</tr>
			</table>

			<BR>~;

      addOnExecute("AdminPanel");

			$subsmain .= qq~<table border="0" cellpadding="5" cellspacing="1" align="center" bgcolor="$color{'bordercolor'}" class="bordercolor" width="100%">
				<tr>
				<td class="catbg" bgcolor="$color{'catbg'}" colspan="2"><img src="$imagesdir/info.gif" alt="" border="0"> <font size="2"><B>$txt{'571'}</B></font></td>
				</tr><tr>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}" colspan="2">
					<font size="1">$mcctxt{'104'}</font>
					</td>
				</tr>
			</table>~;

			$subsmain .= qq~<BR><table border="0" cellpadding="5" cellspacing="1" align="center" bgcolor="$color{'bordercolor'}" class="bordercolor" width="100%">
				<tr>
				<td class="catbg" bgcolor="$color{'catbg'}" colspan="2"><img src="$imagesdir/info.gif" alt="" border="0"> <font size="2"><B>$mcctxt{'155'}</B></font></td>
				</tr><tr>
					<td class="windowbg2" bgcolor="$color{'windowbg2'}" colspan="2">
					<font size="1">$addonMsg</font>
					</td>
				</tr>
			</table>~ if ($addonMsg);

			$subsmain .= qq~</td></tr></table>~;
	$substitle = "$txt{'208'}";
	template();
}

sub GenerateAreas {
	is_admin();
	update_all_member_password();
	message_page($mcctxt{'18'},$mcctxt{'19'});
}

sub RemoveInactive {
	is_admin();
	remove_inactive($maxinactive);
	redirectexit("$cgi;action=admin");
}

sub ImportMembers {
	is_admin();
	$subsmain .=  qq~
		<form method="post" action="$cgi;action=uploadmembers2" enctype="multipart/form-data">
	<table border=0 cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
	  <tr>
	    <td class="titlebg" bgcolor="$color{'titlebg'}" height="30">
	    &nbsp;<img src="$imagesdir/profile.gif" alt="" border="0">&nbsp;
	    <font size=2 class="text1" color="$color{'titletext'}"><b>$mcctxt{'45'}</b></font></td>
  </tr><td class="windowbg" bgcolor="$color{'windowbg'}">
	<p>$mcctxt{'52'}</p>
		<p><input type=file name="memberfile" accept="text/html" size=80></p>
		<p><center><input type=submit value=$mcctxt{'51'}></center></p>
	</td>
	</table>
		</form>
	~;
	$substitle = "$mcctxt{'45'}";
	template();
}

sub ImportMembers2 {
	is_admin();
	my $file = param('memberfile');
  load_members_from_file($file);
  $substitle = "$mcctxt{'45'}";
	message_page($mcctxt{'18'},$mcctxt{'50'});
}

sub CreateMail {
	my ($line);
	foreach $curmem (get_member_keys()) {
		my @record = get_member_info($curmem);
		$email = $record[9];
		$email =~ tr/\r//d;
		$email =~ tr/\n//d;
		$line .= "$email; ";
	}
	CreateMailForm("$line","\n\n$txt{'130'}\n\n$scripturl");
}

sub MailGroup {
	my ($line);
	my @group = get_group_info($INFO{'group'});
	if (defined @group) {
		foreach my $key (split(/\+/, $group[4])) {
			my @record = get_member_info($key);
			my $email = $record[9];
			$email =~ tr/\r//d;
			$email =~ tr/\n//d;
			$line .= "$email; ";
		}
		CreateMailForm("$line","\n\n$txt{'130'}\n\n$scripturl");
  }
  redirectexit("$cgi;action=admin");
}

sub CreateMailForm {
	my ($line,$message) = @_;
	is_admin();
	$subsmain .= qq~
	<form action="$cgi;action=mail" method="post">
	<table border="0" width="600" cellspacing="1" cellpadding="4" bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
  <tr>
    <td class="titlebg" bgcolor="$color{'titlebg'}">
    &nbsp;<img src="$imagesdir/email.gif" alt="" border="0">
    <font size=2 class="text1" color="$color{'titletext'}"><b>$txt{'6'}</b></font></td>
  </tr><tr>
    <td class="windowbg" bgcolor="$color{'windowbg'}">
    <BR><font size="1">$txt{'735'}</font><BR><BR></td>
  </tr><tr>
    <td class="windowbg2" bgcolor="$color{'windowbg2'}">
    <textarea cols="110" rows="7" name="emails">$line</textarea><BR><BR></td>
  </tr><tr>
    <td bgcolor="$color{'titlebg'}"><font size=2 cols="120" color="$color{'titletext'}"><b>$txt{'338'}</b></font></td>
  </tr><tr>
    <td bgcolor="$color{'windowbg2'}" class="windowbg2"><b>$mcctxt{'89'} : </b><input type=text name="subject" size=99 value="$txt{'70'}"><br><br>
    <textarea cols=110 rows=9 name=message>$txt{'72'} $message</textarea><br><br>
    <center><input type=submit value="$txt{'339'}"></center></td>
  </tr>
</table></form>
~;
	$substitle = "$txt{'6'}";
	template();
}

sub MailAll {
 $FORM{'emails'} = "; " . $FORM{'emails'};
 @emails = split(/;\s*/, $FORM{'emails'});
 my $subject = "$FORM{'subject'}";
 my $msg = "$FORM{'message'}";
 my $line;
 my $count=0;
 foreach my $email (@emails) {
  $line = $msg;
  get_email_member_info($email,0) if ($personalemail);
  $line =~ s~<field\s+(\w+)>~${"$1"}~g;
  while ($line =~ m~<crypt\s+(\w+)>~) {
		my $s = "$1";
		$s = crypt($s,$pwseed);
		$line =~ s~<crypt\s+(\w+)>~$s~;
  }
  if (sendmail( $email, $subject, $line)==1) {
		$count++;
		info("Send mail $email ok") if ($logging);
	}
 }
 message_page("$mcctxt{'62'}","($count) $mcctxt{'90'}");
 #redirectexit("$cgi;action=admin");
}


1;