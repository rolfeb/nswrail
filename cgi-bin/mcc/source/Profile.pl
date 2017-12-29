require "$sourcedir/ListIndex.pl";

$maxmemberlength = 30 if (!$maxmemberlength);

sub ModifyProfile {
	my ($add_mode) = @_;
	if ($INFO{'username'} =~ m~/~){ &fatal_error($txt{'224'}); }
	if ($INFO{'username'} =~ m~\\~){ &fatal_error($txt{'225'}); }
	if($memberData[0] ne $INFO{'username'} && $memberData[7]==0) { &fatal_error($txt{'80'}); }
  my $checked = $memberData[3]==0 ? '' : 'CHECKED';
  my $mode = $add_mode==1 ? "text" : "hidden";
	my @memsettings = get_member_info($INFO{'username'});

	$subsmain .= qq~
	<form action="$cgi;action=profile2" method="post" name="creator">
		<input type="hidden" name="addmode" value=$add_mode>
		<table border=0 width=720 cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
  	<tr>
  	  <td class="titlebg" bgcolor="$color{'titlebg'}" height="30">
  		  &nbsp;<img src="$imagesdir/profile.gif" alt="" border="0">&nbsp;
  	  <font size=2 class="text1" color="$color{'titletext'}"><b>$txt{'79'}</b></font></td>
  	</tr><tr>
  	  <td class="windowbg" bgcolor="$color{'windowbg'}" height="25"><BR><font size="1">$txt{'698'}</font><BR><BR></td>
  	</tr><tr>
  	  <td class="catbg" bgcolor="$color{'catbg'}" height="25"><font size=2><b>$txt{'517'}</b></font></td>
  	</tr><tr>
  	  <td class="windowbg" bgcolor="$color{'windowbg'}"><font size=2>
  	  <table border=0 width="100%" cellpadding="3">
  	    <tr>
					<td width="320"><font size=2><b>* $txt{'35'}: </b></font></td>
					<td><font size=2><input type="$mode" name="username" value="$INFO{'username'}">$INFO{'username'}</font></td>
		    </tr><tr>
					<td width="320"><font size=2><b>* $txt{'81'}: </b></font><BR>
						<font size="1">$txt{'596'}</font></td>
					<td><input type="password" maxlength="30" name="passwrd1" size="20" value=""></td>
	      </tr><tr>
					<td width="320"><font size=2><b>* $txt{'82'}: </b></font></td>
					<td><input type="password" maxlength="30" name="passwrd2" size="20" value=""></td>
	      </tr><tr>
					<td width="320"><font size=2><b>* $txt{'68'}: </b></font><BR>
						<font size="1">$txt{'518'}</font></td>
					<td><input type="text" maxlength="30" name="name" size="30" value="$memsettings[8]"></td>
      </tr><tr>
					<td width="320"><font size=2><b>* $txt{'69'}: </b></font><BR>
		        <font size="1">$txt{'679'}</font></td>
					<td><input type="text" maxlength="40" name="email" size="30" value="$memsettings[9]"></td>
      </tr>
  ~;
  AddFlexProfile(0,$INFO{'username'});
  addOnExecute("ProfilePage");
  if($memberData[7]>0) {
		$confdel_text = "$txt{'775'} $txt{'777'} $INFO{'username'} $txt{'778'}";
		$checked = $memsettings[7]==0 ? '' : 'CHECKED';
		$active = $memsettings[3]==0 ? '' : 'CHECKED';
		$subsmain .= qq~
		<tr>
			<td width="320"><font size=2><b>$txt{'2'}: </b></font>
			<td><input type="checkbox" name="admin" value=1 $checked></td>
      </tr><tr>
			<td width="320"><font size=2><b>$mcctxt{'36'}: </b></font>
			<td><input type="checkbox" name="active" value=1 $active></td>
      </tr>
		~;
		if ($INFO{'requestlist'} || $massmember) {
			#Show area and member mutations
			$subsscripts .= qq~$listbox_script~;

			my $active ='';
			my $free = '';
			my @akey = (get_area_sort_keys(1,0));
			my @hash = ();
			foreach my $key (@akey) {
				my @area = get_area_info($key);
				if (includes($memsettings[0],$area[7])!=-1) {
					$active .= qq~<option value="$key">$key</option>~;
				} else {
					unshift @hash,$key;
				}
			}
			foreach my $key (@hash) {
			 $free .= qq~<option value="$key">$key</option>~;
			}
			my $line = $memsettings[6];
			$line =~s~\+~ ~;
			$subsmain .= qq~
			   <tr>
			    <td colspan="8" class="catbg" bgcolor="$color{'catbg'}" height="25"><font size=2><b>$mcctxt{'72'}: </b>$line</font></td>
				</tr><tr><td colspan="8">
				<table border=0 width=720 cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
				<tr><td>
				<input type="hidden" name="yes_areas">
				<input type="hidden" name="yes_groups">
				<input type="hidden" name="no_areas">
				<input type="hidden" name="no_groups">
				<input type="hidden" name="requestlist" value="$INFO{'requestlist'}">
				<input type="hidden" name="massmember" value="$massmember">
				<table border=0 with="100%" cellpadding=1 cellspacing=4>
				  <tr>
					<td colspan="3" class="windowbg" bgcolor="$color{'windowbg'}" height="15">
						<font size=2 ><b>$mcctxt{'35'}</b></font><br>
					</tr><tr>
						<td valign="top">$mcctxt{'36'}<br>
							<select multiple name="areaitems" size="8">
									$active
							</select>
						</td>
						<td align="center">	<input type="button" name="addbtn" value="    >    "
									onclick="addItems(this.form.areaitems, this.form.areasfree); removeItems(this.form.areaitems);">
							<br><br>
							<input type="button"
									name="removebtn"
									value="    <    "
									onclick="addItems(this.form.areasfree, this.form.areaitems); removeItems(this.form.areasfree);">
							<br>
						</td>
						<td valign="top">
							$mcctxt{'37'}<br>
							<select multiple name="areasfree" size="8">
								$free
							</select>
						</td>
					</tr>
				</table>
				</td>
      ~;

			$active ='';
			$free = '';
			@akey = (get_group_sort_keys(1,0));
			@hash = ();
			foreach my $key (@akey) {
				my @group = get_group_info($key);
				if (includes($memsettings[0],$group[4])!=-1) {
					$active .= qq~<option value="$key">$key</option>~;
				} else {
					unshift @hash,$key;
				}
			}
			foreach my $key (sort @hash) {
				 $free .= qq~<option value="$key">$key</option>~;
			}
			$subsmain .= qq~
				<td>
				<table border=0 with="100%" cellpadding=1 cellspacing=4>
					<tr>
					<td colspan="3" class="windowbg" bgcolor="$color{'windowbg'}" height="15">
						<font size=2 ><b>$mcctxt{'25'}</b></font>
					</tr><tr>
						<td valign="top">$mcctxt{'36'}<br>
							<select multiple name="groupitems" size="8">
									$active
							</select>
						</td>
						<td align="center">
							<input type="button"
									name="addbtn2" value="    >    "
									onclick="addItems(this.form.groupitems, this.form.groupsfree); removeItems(this.form.groupitems);">
							<br><br>
							<input type="button"
									name="removebtn2"
									value="    <    "
									onclick="addItems(this.form.groupsfree, this.form.groupitems); removeItems(this.form.groupsfree);">
							<br>
						</td>
						<td valign="top">
							$mcctxt{'37'}<br>
							<select multiple name="groupsfree" size="8">
								$free
							</select>
						</td>
					</tr>
				</table>
				</td>
				</tr>~;
			if ($INFO{'requestlist'}) {
				$subsmain .= qq~<tr>
					<td><INPUT type="radio" name="raction" value="continue" CHECKED> $mcctxt{'95'}<BR>
					<INPUT type="radio" name="raction" value="accept"> $mcctxt{'96'}<BR>
					<INPUT type="radio" name="raction" value="reject"> $mcctxt{'97'}
					</td><td>
					$mcctxt{'98'}<BR>
					<TEXTAREA name="message" cols=80 rows=5></TEXTAREA>
			  </td>
				</tr>~;
		 }
		 $subsmain .= qq~</table>	~;


	  }
	} else {
		$confdel_text = "$txt{'775'} $txt{'776'} $txt{'778'}";
		$subsmain .= qq~
		  <input type="hidden" name="admin" value=$memberData[7]>
		  <input type="hidden" name="active" value=$memberData[3]>
		~;
	}

	$subsmain .= qq~
		</table>
		<table border=0 width=720 cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
		  <tr>
		    <td class="catbg" bgcolor="$color{'catbg'}" height="25" align="center"><font size=2><BR>
			    <script language="JavaScript"> <!--
				    function confirmDel() {if (confirm("$confdel_text")) {return true;} else {return false;}}
				    function moveMember() {
							document.creator.yes_areas.value = getSelectedString(document.creator.areaitems);
						 	document.creator.yes_groups.value = getSelectedString(document.creator.groupitems);
							document.creator.no_areas.value = getSelectedString(document.creator.areasfree);
		   	      document.creator.no_groups.value = getSelectedString(document.creator.groupsfree);
		   	      return true;
						}
				    // --> </script>
	~;
	if ($INFO{'requestlist'} || $massmember) {
		$subsmain .= qq~<input type=submit name=moda value="$txt{'88'}"
		   onclick="return moveMember();"> ~
  } else {
	  $subsmain .= qq~<input type=submit name=moda value="$txt{'88'}"> ~;
  }
  if ($add_mode==0) {
		$subsmain .= qq~
      <input type=submit name=moda value="$txt{'89'}" onClick="return confirmDel()"><BR>
    ~;
  }
  $subsmain .= qq~
    </font></td>
  </tr>
  </table>
</font></table>
</form>
  <center><BR><a href="javascript:history.go(-1)">$txt{'250'}</a></center>
  ~;
	$substitle = $txt{'79'};
	&template;
}

#######################################

sub ModifyProfile2 {
	my( %member, $key, $value, @memberlist, $a, $umail, @members, $tempname );
	while( ($key,$value) = each(%FORM) ) {
		$value =~ s~\A\s+~~;
		$value =~ s~\s+\Z~~;
		$value =~ s~[\n\r]~~g;
		$member{$key} = $value;
	}

	# make sure this person has access to this profile

	if($member{'moda'} eq $txt{'88'}) {
		if($username ne $member{'username'} && $memberData[7]==0) { &fatal_error($txt{'80'}); }

		if ($member{'username'} =~ /\//){ &fatal_error($txt{'224'}); }
		if ($member{'username'} =~ /\\/){ &fatal_error($txt{'225'}); }
		$INFO{'username'} = $member{'username'};
		fatal_error("$txt{'75'}") if($member{'username'} eq '');
		fatal_error("$txt{'75'}") if($member{'name'} eq '');
		fatal_error("$txt{'240'} $txt{'68'} $txt{'241'}") if($member{'name'} !~ /^[\s0-9A-Za-zöäüÖÄÜßñ\[\]#%+,-|\.:=?@^_]+$/);
		fatal_error("$txt{'75'}") if($member{'name'} eq '|');
		fatal_error("$txt{'568'}") if(length($member{'name'}) > $maxmemberlength);
		fatal_error("$txt{'76'}") if($member{'email'} eq '');
		fatal_error("$txt{'240'} $txt{'69'} $txt{'241'}") if($member{'email'} !~ /^[0-9A-Za-z@\._\-]+$/);
		fatal_error("$txt{'500'}") if(($member{'email'} =~ /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)|(\.$)/) || ($member{'email'} !~ /^.+@\[?(\w|[-.])+\.[a-zA-Z]{2,4}|[0-9]{1,4}\]?$/));
		fatal_error("$member{'email'} $mcctxt{'78'}") if (exists_email($member{'email'},$member{'username'}));

    addOnExecute("ProfileSave");
    my @flex = GetFlexProfile();
		# store the name temorarily so we can restore any _'s later
		$tempname = $member{'name'};
		$member{'name'} =~ s/\_/ /g;
		&ToHTML($member{'email'});
		&ToHTML($member{'name'});
		if ($member{'addmode'}==1) {
			ValidNewMember($member{'username'},$member{'email'},$member{'passwrd1'},$member{'passwrd2'});
			set_member_info($member{'username'},$member{'passwrd1'},'','','','0',$member{'name'},$member{'email'},@flex);
	    my @grp = split(/\+/, $defaultgroups);
			foreach my $key (@grp) {
				add_group_members($key,$member{'username'});
		  }
		} else {
		  set_profile_info($member{'username'},$member{'name'},$member{'email'},@flex);
			if($member{'passwrd1'} ne '') {
					&fatal_error("($member{'username'}) $txt{'213'}") if($member{'passwrd1'} ne $member{'passwrd2'});
					&fatal_error("$txt{'240'} $txt{'36'} $txt{'241'} $passwordtext") if($member{'passwrd1'} !~ /$passwordfilter/);
					change_password($member{'username'},"",$member{'passwrd1'},$member{'passwrd1'},1);
			}
	  }
	  if ($memberData[7]>0) {
	    set_admin_site($member{'username'},$member{'admin'});
		}
	  set_member_state($member{'username'},$member{'active'});
	  #Check if we where working on requestlist
	  if ($member{'requestlist'} || $member{'massmember'}) {
			is_admin();
			info($member{'yes_groups'},$member{'no_groups'},$member{'yes_areas'},$member{'no_areas'})if ($logging);
			my (@list) = split(/\+/,$member{'yes_groups'});
			foreach my $key (@list) {
				add_group_members($key,$member{'username'});
			}
			@list = split(/\+/,$member{'no_groups'});
			foreach my $key (@list) {
				remove_group_members($key,$member{'username'});
			}
			(@list) = split(/\+/,$member{'yes_areas'});
			foreach my $key (@list) {
				add_area_members($key,$member{'username'});
			}
			@list = split(/\+/,$member{'no_areas'});
			foreach my $key (@list) {
				remove_area_members($key,$member{'username'});
			}
			if ($member{'requestlist'}) {
				if ($member{'message'} ne '') {
					my $subject = "$mbname\: ";
					if ($member{'raction'} eq 'accept') {
						$subject .= "$mcctxt{'96'}";
					} elsif ($member{'raction'} eq 'reject') {
						$subject .= "$mcctxt{'97'}";
					} else {
						$subject .= "$mcctxt{'94'}";
					}
					sendmail($member{'email'},"$subject $mcctxt{'99'}",$member{'message'});
				}
				if ($member{'raction'} ne 'continue') {
					#remove request
					set_areas_request($member{'username'},'');
				}
		  }
		}

		commit_database();

		if ($member{'username'} eq $memberData[0] and $member{'passwrd1'} ne '') {
			clearUserSettings();
			redirectexit(qq~$cgi;action=logout~);
		}
  } elsif ($member{'moda'} eq $txt{'89'}){
		addOnExecute("ProfileSave");
		delete_members($member{'username'});
		remove_member_from_group($member{'username'});
		commit_database();
		if ($member{'username'} eq $memberData[0]) {
			clearUserSettings();
			redirectexit(qq~$cgi;action=logout~);
		}
	}
	require "$sourcedir/ListIndex.pl";
	if ($member{'requestlist'}) {
		  redirectexit("$cgi;action=managerequests");

	}
	if ($hidememberlist && $memberData[7]==0){ #hide and no admin
	  redirectexit("$cgi");
  }
	#Go back to memberlist
  my $page = page_location($member{'username'}, (get_member_sort_keys(1,0)));
  redirectexit("$cgi;action=memberlist;page=${page}");
}

sub Activate2 {
	my( %member, $key, $value,  );
	while( ($key,$value) = each(%FORM) ) {
		$value =~ s~\A\s+~~;
		$value =~ s~\s+\Z~~;
		$value =~ s~[\n\r]~~g;
		$member{$key} = $value;
	}

  my $mb = $INFO{'member'} || $member{'member'};
  my (@record) = get_member_info($mb);
  my $code = $member{'code'} || $INFO{'code'};
  if (defined @record) {
		if ($record[4] eq $code and $code ne '') {
			set_activation_code($record[0],'');
			set_member_state($record[0],1);
			commit_database();
			redirectexit("$cgi");
  	} else {
			fatal_error("$mcctxt{'67'}");
	  }
	} else {
		fatal_error("$mcctxt{'3'} $mb");
  }
}

sub MailActivationCode {
	$user = $FORM{'member'} || $INFO{'member'};

	my @member = get_email_member_info($user,1);
	if (defined @member) {
		$name = $member[8];
		$email = $member[9];
		$code = salt(8);
		set_activation_code($user,$code);
		commit_database();

		chomp($name);
		chomp($email);
		chomp($code);

		my $line = qq~$txt{'711'} $name,\n\n$mbname ==>\n\n$txt{'35'}: $user\n$mcctxt{'65'}: $code\n\n$txt{'130'}\n\n$cgi;action=activate;member=$member[0];code=$code~;

		if (fopen(FILE,"$datadir/activationcode.txt")) {
			my @lines = <FILE>;
			fclose(FILE);
			$line = join('',@lines);
			$line =~ s~<field\s+(\w+)>~${"$1"}~g;
		}

		$subject = "$mcctxt{'65'} $mbname : $name";
		&sendmail($email, $subject,$line);

		$subsmain .= qq~
		<BR><BR><table border=0 width="80%" cellspacing=1 bgcolor="$color{'bgcolor'}" align="center">
			<tr>
				<td class="titlebg" bgcolor="$color{'titlebg'}">
				<font size=2 class="text1" color="$color{'titletext'}"><b>$mbname $mcctxt{'65'}</b></b></font></td>
			</tr><tr>
				<td class="windowbg" bgcolor="$color{'windowbg'}">
				<table border=0 align="center">
					<tr>
						<td align="center"><font size="2"><b>$txt{'192'}: $user</b></font></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<br><center><a href="javascript:history.back(-2)">$txt{'193'}</a></center><br>
		~;
		$substitle = "$mcctxt{'64'}";
		&template;
 } else {
    fatal_error("$mcctxt{'3'} $user");
 }
}

sub AddFlexProfile{
	my ($requiredonly,$member) = @_;
	fopen(FILE, "$datadir/flexprofile.cfg");
	my $count = 9;
	my @record = get_member_info($member);
	while($line = <FILE>) {
		chomp($line);
		if ($line ne '' and substr($line,0,1) ne '#') {
			my @data = split(/\~/, $line);
			$count++;
			my $req = $data[0]==2 ? '*' : ' ';
			my $html = qq~<tr><td width="40%"><font size=2><b>$req $data[2]:</b></font>
			        <BR><font size="1">$data[3]</font></td>
			        <td><input type="text" name="flex${count}" size=$data[1] value="$record[$count]"></td></tr>~;
			if ($requiredonly and $data[0]==2) {
			  $subsmain .= $html;
		  }elsif (!$requiredonly and ($data[0]>0 or $memberData[7]!=0)) {
			  $subsmain .= $html;
		  }else {
				$subsmain .= qq~<input type="hidden" name="flex${count}" value="$record[$count]">~;
			}
		}
	}
  fclose(FILE);
}

#Return an array with all flex fields
sub GetFlexProfile{
	my( %profile, $key, $value);
	while( ($key,$value) = each(%FORM) ) {
			$value =~ s~\A\s+~~;
			$value =~ s~\s+\Z~~;
			$value =~ s~[\n\r]~~g;
			if (substr($key,0,4) eq "flex") {
			  $profile{substr($key,4)} = $value;
		  }
	}
	fopen(FILE, "$datadir/flexprofile.cfg");
	my @flex = ();
	my $count = 9;
	while($line = <FILE>) {
		chomp($line);
		if ($line ne '' and substr($line,0,1) ne '#') {
			$count++;
			my @data = split(/\~/, $line);
			$value = $profile{"$count"};
			@flex = (@flex, "$value");
			if ($data[0] == 2 and $value eq '') {
				fclose(FILE);
				fatal_error("$mcctxt{'6'} $data[2]");
		  }
		  my $invalid=0;
		  #No validation for admin
		  if ($memberData[7]==0) {
		    $invalid |=  ($data[0]>1 and $value eq '');
		    $invalid |=  ($data[0]>1 and $data[4] ne '' and ($value !~ /$data[4]/));
		    $invalid |=  ($data[0]==1 and $value ne '' and $data[4] ne '' and ($value !~ /$data[4]/));
		  }
		  if ($invalid) {
				fclose(FILE);
				fatal_error("$txt{'240'} $data[2] $txt{'241'} $data[5]");
		  }
	  }
  }
  fclose(FILE);
	return @flex;
}

sub Register {
if ($disableregister) {fatal_error($mcctxt{'125'})};
my $line = "";
 if (fopen(FILE, "$datadir/register.txt")) {
   my @lines = <FILE>;
   fclose(FILE);
   my $e =join("<BR>",@lines);
   if ($e ne '') {
			 $subsmain .= qq~<table border=0 width="100%" cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center" cellpadding="4">
			<tr>
				<td class="titlebg" bgcolor="$color{'titlebg'}"><font size=2 class="text1" color="$color{'titletext'}"><b>$mcctxt{'110'}</b></font></td>
			</tr><tr>
				<td class="windowbg" bgcolor="$color{'windowbg'}"><BR><font size=2>$e</font><BR></td>
			</tr>
		</table>
		<BR>
		~;
   }
 }
	$subsmain .= qq~
<table border=0 width=100% cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" cellpadding="2">
  <tr>
    <td class="titlebg" bgcolor="$color{'titlebg'}">
    <img src="$imagesdir/register.gif" alt="$txt{'97'}" border="0"> <font size=2 class="text1" color="$color{'titletext'}"><b>$txt{'97'}</b> $txt{'517'}</font></td>
  </tr><tr>
    <td class="windowbg" bgcolor="$color{'windowbg'}" width="100%"><font size=2>
    <form action="$cgi;action=register2" method="post" name="creator">
    <table cellpadding="3" cellspacing="0" border=0 width="100%">
      <tr>
        <td width="40%"><font size=2><b>* $txt{'98'}:</b></font>
        <BR><font size="1">$txt{'520'}</font></td>
        <td><input type=text name=username size=20 maxlength="18"></td>
      </tr><tr>
				<td width="40%"><font size=2><b>* $mcctxt{'30'}:</b></font>
				<td><input type=text name=fullname size=80 ></td>
      </tr><tr>
        <td width="40%"><font size=2><b>* $txt{'69'}:</b></font>
        <BR><font size="1">$txt{'679'}</font></td>
	   <td><input type=text name=email size=30>
     </tr>
     <tr>
        <td width="40%"><font size=2><b>* $txt{'81'}:</b></font></td>
        <td><font size=2><input type=password maxlength="30" name=passwrd1 size=30></font></td>
      </tr><tr>
        <td width="40%"><font size=2><b>* $txt{'82'}:</b></font></td>
        <td><font size=2><input type=password maxlength="30" name=passwrd2 size=30></font></td>
      </tr>
     ~;
    AddFlexProfile(!$allonregister,'');
    addOnExecute("RegisterPage");
    $subsmain .= qq~
    </table>
    </td>
    </tr>
    </table>
   ~;
$subsmain .= qq~
<BR><center><input type=submit value="$txt{'97'}"></center>
</form>
<script language="JavaScript"> <!--
	document.creator.username.focus();
//--> </script>
~;
	$substitle = "$txt{'97'}";
	&template;
}


sub ValidNewMember {
	  my ($username,$email,$passwrd1,$passwrd2) = @_;
		fatal_error("($username) $txt{'37'}") if($username eq '');
		fatal_error("($username) $txt{'99'}") if($username eq '_' || $username eq '|');
		fatal_error("$txt{'244'} $username") if($username =~ /guest/i);
		fatal_error("$txt{'240'} $txt{'35'} $txt{'241'}") if($username !~ /\A[0-9A-Za-z#%+-\.@^_]+\Z/);
		fatal_error("$txt{'240'}") if($username =~ /,/);
		fatal_error("($username) $txt{'76'}") if($email eq "");
		my ($found,@record) = find_member($username);
		fatal_error("($username) $txt{'100'}") if($found);
		fatal_error("$txt{'240'} $txt{'69'} $txt{'241'}") if($email !~ /\A[0-9A-Za-z@\._\-]+\Z/);
		fatal_error("$txt{'500'}") if(($email =~ /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)|(\.$)/) || ($email!~ /\A.+@\[?(\w|[-.])+\.[a-zA-Z]{2,4}|[0-9]{1,4}\]?\Z/));
    fatal_error("$email $mcctxt{'78'}") if (exists_email($email));
    fatal_error("($username) $txt{'213'}") if($passwrd1 ne $passwrd2);
		fatal_error("$txt{'240'} $txt{'36'} $txt{'241'} $passwordtext") if($passwrd1 !~ /$passwordfilter/);
  	fatal_error("($username) $txt{'91'}") if($passwrd1 eq '');
}

sub Register2 {
	if ($disableregister) {fatal_error($mcctxt{'125'})};
	my %member;
	while( ($key,$value) = each(%FORM) ) {
		$value =~ s~\A\s+~~;
		$value =~ s~\s+\Z~~;
		$value =~ s~[\n\r]~~g;
		$member{$key} = $value;
	}
	$member{'username'} =~ s/\s/_/g;
	if (length($member{'username'}) > $maxmemberlength) { $member{'username'} = substr($member{'username'},0,25); }
  ValidNewMember($member{'username'},$member{'email'},$member{'passwrd1'},$member{'passwrd2'});

  my @flex = GetFlexProfile();
  addOnExecute("RegisterSave");
	info("Adding new Member","$member{'username'}","$member{'passwrd1'}","","","",0,"$member{'fullname'}", "$member{'email'}",@flex)if ($logging);
	set_member_info("$member{'username'}","$member{'passwrd1'}","","","",0,"$member{'fullname'}", "$member{'email'}",@flex);
	my @grp = split(/\+/, $defaultgroups);
	foreach my $key (@grp) {
		add_group_members($key,$member{'username'});
	}
	commit_database();
  if ($sendwelcome) {
		my @record = get_member_info($member{'username'});
		$newmember = $record[0];
		$newname = $record[8];
		$newcode = $record[4];
		$newpassword = $record[1];
		fopen(FILE, "$datadir/welcome.txt");
		my ($msg,$line);
		while($line = <FILE>) {
			$line =~ s~<field\s+(\w+)>~${"$1"}~g;
			$msg .= qq~$line~;
		}
	  fclose(FILE);
	  info($msg)if ($logging);
	  sendmail($member{'email'},"$txt{'248'} $member{'username'} $txt{'311'} $mbname",$msg);
  }
  $subsmain .= qq~
		<BR><BR>
		<table border=0 width=300 cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center">
			<tr>
				<td class="titlebg" bgcolor="$color{'titlebg'}">
				<img src="$imagesdir/register.gif" alt="$txt{'97'}" border="0"> <font size=2 class="text1" color="$color{'titletext'}"><b>$txt{'97'}</b></font></td>
			</tr><tr>
				<td class="windowbg" bgcolor="$color{'windowbg'}" align="center"><font size=2><form action="$cgi;action=login2" method="post">
				<BR>$txt{'431'}<BR><BR>
				<input type=hidden name="username" value="$member{'username'}">
				<input type=hidden name="passwrd" value="$member{'passwrd1'}">
				<input type=hidden name="cookielength" value="$Cookie_Length">
				<input type=submit value="$txt{'34'}">
				</form></font></td>
			</tr>
		</table>
		<BR><BR>
	~;
	$substitle="$txt{'245'}";
	&template;
}

sub RemoveProfile {
	my $member = $FORM{'member'} || $INFO{'member'};
	my $password = $FORM{'member'} ? crypt($FORM{'password'},$pwseed) : $INFO{'password'};
	my @record = validate_member($member,$password,1);
	if (defined @record) {
		delete_members($member);
		remove_member_from_group($member);
		commit_database();
		if ($member{'username'} eq $memberData[0]) {
			clearUserSettings();
			redirectexit(qq~$cgi;action=logout~);
	  }
	  message_page("$mcctxt{'148'} $member");
	}
	fatal_error("$mcctxt{'147'} $member");
}


1;