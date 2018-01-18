#!/usr/bin/perl


#TODO Remove Group from AREA after remove Group
#TODO Remove Members form AREA after Remove Member

$mccversion="1.09.0706";
$pushlocalpath = 0;  #Try putting this to 1 if you encounter a @INC error

if( $pushlocalpath ) {
	$0 =~ m~(.*)(\\|/)~;
	$path = $1;
	$path =~ s~\\~/~g;
	chdir($path);
	push(@INC,$path);
}

# Protected Directory Manager
use CGI qw(header cookie);	# so we can use the header and cookie printing
use CGI qw/:standard/;

$CGI::HEADERS_ONCE = 1;	# Kill redundant headers

require "Settings.pl";
require "$language";
require "$sourcedir/Subs.pl";
require "$sourcedir/Template.pl";
if ($traperrors) {
	$SIG{__WARN__} = sub { &fatal_error( @_ ); };
}

$databasedriver = "TextFiles.dbd" if ($databasedriver eq '');
require "$sourcedir/$databasedriver";


eval { &main; };
if ($@) { &fatal_error("$mcctxt{'124'}<BR>$@"); }

sub main {
	if ($rooturl eq '') {$rooturl = GetMccURL();$loadsettings=1;}
	if ($rootdir eq '') {$rootdir = GetDirPath();$loadsettings=1;}
	$scripturl = qq~$rooturl/mcc.pl~;
	$cgi = qq~$scripturl?~;

	require "$sourcedir/AccessControl.pl";
	require "$sourcedir/Load.pl";

	loadAddOns();
  loadCookies();
	loadUserSettings();
	readform();
	$cgi = "$INFO{'cgi'}?" if ($INFO{'cgi'});
	$smalltarget = "$INFO{'smalltarget'}" if ($INFO{'smalltarget'});
	$smalltemplate= "$INFO{'small'}" eq '1' if ($INFO{'small'}) ;
	$directload= "$INFO{'direct'}" eq '1' if ($INFO{'direct'});

	$cgitarget = $cgi;
	$cgi .= ";small=1" if ($smalltemplate);
	$cgi .= ";direct=1;areasequence=$INFO{'areasequence'}" if ($directload);
	#Admin only
	$orghelpfile = $helpfile;
	if ($memberData[7]>0) {
	  $helpfile =~ s~index\.html~adminindex\.html~g; #Change to complex help menu
	}

	addOnExecute("Init");

  my $fastfind = $action ? substr($action,0,1) : '.';
  info("Action=$action") if ($logging);
	#BEGIN FASTFIND IF STATEMENT
	if ($fastfind eq 'i' )  {
		info("Inline");
		if ($action eq 'inline') { inline();}
	} elsif ( $fastfind eq 'l' ) {
		if ($action eq 'login') { require "$sourcedir/LogInOut.pl"; Login(); }
		elsif ($action eq 'login2') { require "$sourcedir/LogInOut.pl"; Login2(); }
		elsif ($action eq 'logout') { require "$sourcedir/LogInOut.pl"; Logout(); }
	#END FASTFIND L*
	} elsif ($fastfind eq 'r') {
		if ($action eq 'register') { require "$sourcedir/Profile.pl"; Register(); }
		elsif ($action eq 'register2') { require "$sourcedir/Profile.pl"; Register2(); }
		elsif ($action eq 'reminder') { require "$sourcedir/LogInOut.pl"; Reminder(); }
		elsif ($action eq 'reminder2') { require "$sourcedir/LogInOut.pl"; Reminder2(); }
		elsif ($action eq 'requestcode') { require "$sourcedir/Profile.pl"; MailActivationCode(); }
		elsif ($action eq 'removeprofile') { require "$sourcedir/Profile.pl"; RemoveProfile(); }
	} elsif ($fastfind eq 'a') {
		if ($action eq 'activate') { require "$sourcedir/Profile.pl"; Activate2(); }
  }

	#A member should here be available otherwise login
	if (!$memberData[0]) {
		require "$sourcedir/LogInOut.pl";
		Login();
	}

	#Call addon Library, Placed here so it is posible to overrule default actions
	addOnExecute("Main",$action);

  #No Action is area
	if (!$action) {
		if ($loadsettings and $memberData[7]>=1) { #When root dir is empty and admin redirect to settings
		     require "$sourcedir/AdminEdit.pl";
		     ModifySettings();
	  }
		if ($directload && $smalltemplate) {
			#We are going to redirect to area member
			is_authorized($memberData[0],''); #Just load no checking
			@directareas = (split(/\|/,$INFO{'areasequence'}),sort keys %authorized_areas);
			info("Areasequence $INFO{'areasequence'}",@directareas);
			foreach my $key (@directareas){
				if (is_authorized($memberData[0],$key)) {
					#We found authorized area, so redirect,remove direct statement for cgi
					$cgi = $cgitarget;
					$cgi .= "small=1";
					$INFO{'area'} = $key;
					require "$sourcedir/AreaIndex.pl";
					GotoArea();
				}
			}
	  }
		require "$sourcedir/AreaIndex.pl";
	  AreaIndex();
  }
	#Continue with FASTFIND
	if( $fastfind eq 'm' ) {
			if ($action eq 'memberlist') { require "$sourcedir/ListIndex.pl"; MemberList(); }
			elsif ($action eq 'modtemplate') { require "$sourcedir/AdminEdit.pl"; ModifyTemplate(); }
			elsif ($action eq 'modsettings') { require "$sourcedir/AdminEdit.pl"; ModifySettings(); }
			elsif ($action eq 'modsettings2') { require "$sourcedir/AdminEdit.pl"; ModifySettings2(); }
			elsif ($action eq 'managegroups') { require "$sourcedir/ListIndex.pl"; GroupList(); }
			elsif ($action eq 'manageareas') { require "$sourcedir/ListIndex.pl"; AreaList(); }
			elsif ($action eq 'managerequests') { require "$sourcedir/ListIndex.pl"; RequestList(); }
			elsif ($action eq 'mail') { require "$sourcedir/Admin.pl"; MailAll(); }
			elsif ($action eq 'mailgroup') { require "$sourcedir/Admin.pl"; MailGroup(); }
		#END FASTFIND M*
	}elsif( $fastfind eq 'a' ) {
			if ($action eq 'admin') { require "$sourcedir/Admin.pl"; Admin(); }
			elsif ($action eq 'addgroup') { require "$sourcedir/GroupEdit.pl"; ModifyGroup(0);}
		#END FASTFIND A*
	}elsif( $fastfind eq 'g' ) {
			if ($action eq 'generateareas') { require "$sourcedir/Admin.pl"; GenerateAreas();}
			elsif ($action eq 'goto') { require "$sourcedir/AreaIndex.pl"; GotoArea(); }
		#END FASTFIND G*
	}elsif( $fastfind eq 'p' ) {
			if ($action eq 'profile') { require "$sourcedir/Profile.pl"; ModifyProfile(0); }
			elsif ($action eq 'profile2') { require "$sourcedir/Profile.pl"; ModifyProfile2(); }
		#END FASTFIND P*
	} elsif( $fastfind eq 'e' ) {
			if ($action eq 'editgroup') { require "$sourcedir/GroupEdit.pl"; ModifyGroup(0); }
			elsif ($action eq 'editgroup2') { require "$sourcedir/GroupEdit.pl"; ModifyGroup2(); }
			elsif ($action eq 'editarea') { require "$sourcedir/AreaEdit.pl"; ModifyArea(0); }
			elsif ($action eq 'editarea2') { require "$sourcedir/AreaEdit.pl"; ModifyArea2(); }
			elsif ($action eq 'editnews') { require "$sourcedir/AdminEdit.pl"; EditNews(); }
			elsif ($action eq 'editor2') { require "$sourcedir/AdminEdit.pl"; Editor2(); }
			elsif ($action eq 'editwelcome') { require "$sourcedir/AdminEdit.pl"; EditWelcome(); }
			elsif ($action eq 'editflexprofile') { require "$sourcedir/AdminEdit.pl"; EditFlexprofile(); }
			elsif ($action eq 'editredirectscript') { require "$sourcedir/AdminEdit.pl"; EditRedirectScript(); }
			elsif ($action eq 'editregistertext') { require "$sourcedir/AdminEdit.pl"; EditRegisterText(); }
			elsif ($action eq 'edithtaccess') { require "$sourcedir/AdminEdit.pl"; EditHtaccess(); }
			elsif ($action eq 'editactivationcode') { require "$sourcedir/AdminEdit.pl"; EditActivationCode(); }
			elsif ($action eq 'editreminder') { require "$sourcedir/AdminEdit.pl"; EditReminder(); }
		#END FASTFIND E*
	} elsif( $fastfind eq 'n' ) {
		if ($action eq 'newgroup') { require "$sourcedir/GroupEdit.pl"; ModifyGroup(1);  }
		elsif ($action eq 'newarea') { require "$sourcedir/AreaEdit.pl"; ModifyArea(1);  }
		elsif ($action eq 'newmember') { require "$sourcedir/Profile.pl"; ModifyProfile(1);  }
		#END FASTFIND N*
	}elsif( $fastfind eq 'u' ) {
		if ($action eq 'uploadmembers') { require "$sourcedir/Admin.pl"; ImportMembers();  }
		elsif ($action eq 'uploadmembers2') { require "$sourcedir/Admin.pl"; ImportMembers2();  }
		#END FASTFIND u*
	}elsif( $fastfind eq 'c' ) {
		if ($action eq 'createmail') { require "$sourcedir/Admin.pl"; CreateMail();  }
		elsif ($action eq 'changeaccess') { require "$sourcedir/Profile.pl"; ChangeAccess();  }
		elsif ($action eq 'changeaccess2') { require "$sourcedir/Profile.pl"; ChangeAccess2();  }
		#END FASTFIND c*
	}elsif( $fastfind eq 'r' ) {
		if ($action eq 'requestaccess') { require "$sourcedir/AreaIndex.pl"; RequestAccess();  }
		elsif ($action eq 'removeinactive') { require "$sourcedir/Admin.pl"; RemoveInactive();  }
		#END FASTFIND r*
	}elsif( $fastfind eq 'd' ) {
		if ($action eq 'deactivatemembers') { require "$sourcedir/AdminEdit.pl"; DeactivateMembers();  }
		elsif ($action eq 'deactivatemembers2') { require "$sourcedir/AdminEdit.pl"; DeactivateMembers2();  }
		#END FASTFIND d*
	}elsif( $fastfind eq 'b' ) {
			if ($action eq 'browsedir') { BrowseDir();  }
			#END FASTFIND b*
		}


	#Unclear Action
	message_page($mcctxt{'31'},"$mcctxt{'32'}, [$action]");
}


1;
