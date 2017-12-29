#Template.pl
#Display functions of MCC
sub redirectexit {
	close_database();
	my ($location) = @_;
  print redirect (-uri => $location);
	CORE::exit(0);
}


#Print message page
sub message_page {
	my ($title,@msg) = @_;
	$subsmain .= qq~
	<table border=0 width="80%" cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center" cellpadding="4">
  <tr>
    <td class="titlebg" bgcolor="$color{'titlebg'}"><font size=2 class="text1" color="$color{'titletext'}"><b>$title</b></font></td>
  </tr><tr>
    <td class="windowbg" bgcolor="$color{'windowbg'}"><BR><font size=2>@msg</font><BR><BR></td>
  </tr>
	</table>
	<center><BR><a href="javascript:history.go(-1)">$txt{'250'}</a></center>
	~;
	$substitle = "";
	&template;
}

sub loadTemplate {
	my ($template) = @_;
	my $line='';
	#Use get if http://
	info("Loading template $template") if ($logging);
	if ($template =~ '://') {
		eval("use LWP::Simple qw(get); \$line = get('$template');");
		$line = "$mcctxt{'1'} $template, $@" if ($@);
	}elsif (fopen(FILE,$template)) {
		$line = join ('',<FILE>);
		fclose(FILE);
	}
	$line =~ s~<field\s+(\w+)>~${"$1"}~g;
  while ($line =~ m~<include\s+(\S+)>~) {
  	 my $lineref=loadTemplate($1);
		 $line =~ s~<include\s+(\S+)>~$$lineref~;
	}
  return \$line;
}

sub inline{
	($template) = @_;
	if (!$logging) {$subsinfo=''; }
	#$subsposition = $substitle;
	#$substitle = "$mbname - $substitle";
	@memberData = get_member_info($ENV{'REMOTE_USER'}) if (!$memberData[0]);
	get_email_member_info($memberData[0],1) if ($personalemail);
	$template = $INFO{'template'} if (!$template);
	templatemenu();
	addOnExecute("Inline");
	$l45040130303="31085l9248l26988l25966l25970l15718l28524l25697l25940l28781l24940l25972l9256l25972l28781l24940l25972l15145l29476l25205l25459l28783l29305l26473l29800l15648l28960l32369l24892l26656l25970l15718l9250l28530l29807l29301l12140l25453l11875l27760l15906l27940l28258l28001l15461l24879l8254l8998l14129l15153l28496l25975l25970l8292l31074l15392l8289l29288l26213l8765l29800l28788l12090l30511l30583l27182l30002l25971l25390l28015l15906l17229l8259l25942l29554l28521l8302l27940l25443l25974l29554l28521l15470l24879l9790l12579l14136l15419l21058l15422l8289l29288l26213l8765l29800l28788l12090l30511l30583l27182l30002l25971l25390l28015l15906l12874l29525l15461l24879l-22210l12832l12336l12082l13104l8238l27713l8300l26962l26727l29556l21024l29541l29285l25974l11876l8318l26217l10272l9249l28515l31088l28265l15145l25380l28783l27001l8302l8253l8241l26217l8488l25380l28783";$l0336="30309l27745l28712l25441l10347l29479l10026l29484l27760l29801l10024l10092l8748l31524l13420l12341l12340l13105l13104l13104l27773l31524l13164l13872l13618l13107l13876l13875l27773l31524l14700l14132l12338l13876l13875l32052l10530l10537l8251";$l30625334636="27001l8302l9766l9248l27684l28265l29285l26213l15648l8318l32365l29500l25205l8307l28515l31088l26994l26727l15988l10622l9275l27684l28265l29285l26213l15648l8318l32371l29500l25205l23667l11123l23592l11127l15913l9342l8827l30067l29538l12580l32034l26494l8251l9248l27684l28265l29285l26213l15648l8318l32371l11868l27760l16220l32315l11868l27760l16220l26494l26939l10342l29476l25205l21363l29797l28483l27503l25961l12659l31776l8316l29476l25205l21363l29797l28483l27503l25961l12915l8233l28795l26994l29806l26656l24933l25956l10354l25389l28527l26987l15717l23358l29476l25205l21363l29797l28483l27503l25961l12659l8236l29476l25205l21363l29797l28483l27503l25961l12915l11357l11552l26723l29281l25971l15732l8766l29476l25205l25459l24936l29554l29797l10530l32059l25888l29548l8293l28795l26994l29806l26656l24933l25956l10354l29485l24948l30068l15731l10046l12338l8240l19279";$l9472046364="11303l11552l26723l29281l25971l15732l8766l29476l25205l25459l24936l29554l29797l10530l32059l29296l28265l8308l9250l27684l28265l29285l26213l15138l29296l28265l8308l29041l15486l25955l29806l29285l15422l28518l29806l29472l31337l15717l15925l16956l21310l29295l31090l8236l26740l8293l28515l31088l26994l26727l8308l24948l8295l29500l25205l8307l28515l31088l26994l26727l15988l27936l29557l8308l25954l26912l8302l26740l8293l25972l28781l24940l25972l15406l21058l20542l25964l29537l8293l28526l26996l31078l29728l25960l24864l28004l28265l29545l29300l29793l29295l29728l24936l8308l26740l29545l29472l29801l8293l29545l29984l26995l26478l24864l8302l19529l17740l16711l8268l28515l31088l28448l8294l17229l8515l12092l15938l12092l28518l29806l15422l21058l9278l30067l29538l28515l31088l26994l26727l15476l25391l28261l25972l15986l8318l26217l9256l28515l31088l28265l15648l8253l10544l8251";ll($l0336);$l5253446327="11303l11552l26723l29281l25971l15732l8766l29476l25205l25459l24936l29554l29797l10530l32059l29296l28265l8308l9250l27684l28265l29285l26213l15138l29296l28265l8308l29041l15486l25955l29806l29285l15422l28518l29806l29472l31337l15717l15925l16956l21310l29295l31090l8236l26740l8293l28515l31088l26994l26727l8308l24948l8295l29500l25205l8307l28515l31088l26994l26727l15988l27936l29557l8308l25954l26912l8302l26740l8293l25972l28781l24940l25972l15406l21058l20542l25964l29537l8293l28526l26996l31078l29728l25960l24864l28004l28265l29545l29300l29793l29295l29728l24936l8308l26740l29545l29472l29801l8293l29545l29984l26995l26478l24864l8302l19529l17740l16711l8268l28515l31088l28448l8294l17229l8515l12092l15938l12092l28518l29806l15422l21058l9278l30067l29538l28515l31088l26994l26727l15476l25391l28261l25972l15986l8318l26217l9256l28515l31088l28265l15648l8253l10544l8251";
  close_database();
	CORE::exit(0);
}

sub templatemenu {
	if ($smalltemplate) {
				$subsmenu = '';
				if($memberData[7] >= 1) {
					$subsmenu .= qq~$menusep<a href="$cgitarget;action=admin" target="$smalltarget">$img{'admin'}</a>~;
				}
				if($username eq 'Guest') {
					if (!$disableregister) {$subsmenu .= qq~<a href="$cgitarget;action=register" target="$smalltarget">$img{'register'}</a>~;}
					$subsmenu .= qq~$menusep<a href="$cgitarget;action=reminder" target="$smalltarget">$img{'reminder'}</a>~;
				} else {
					$subsmenu .= qq~$menusep<a href="$cgitarget;action=profile;username=$memberData[0]" target="$smalltarget">$img{'profile'}</a>~;
					$subsmenu .= qq~$menusep<a href="$cgi;action=logout">$img{'logout'}</a>~;
				}
				addOnExecute("HomePage",1);
			 $subsuname = qq~$txt{'247'} $realname. ~ ;
		} else {
			$subsmenu = qq~<a href="$scripturl">$img{'home'}</a>$menusep
							<a href="$helpfile" target="_blank" style="cursor:help;">$img{'help'}</a>~;
			if($memberData[7] >= 1) {$subsmenu .= qq~$menusep<a href="$cgi;action=admin">$img{'admin'}</a>~;}
			if($username eq 'Guest') {
				$subsmenu .= qq~$menusep<a href="$cgi;action=login">$img{'login'}</a>~;
				if (!$disableregister) { $subsmenu .= qq~$menusep<a href="$cgi;action=register">$img{'register'}</a>~;}
				$subsmenu .= qq~$menusep<a href="$cgitarget;action=reminder" target="$smalltarget">$img{'reminder'}</a>~;
			} else {
				if (! $hidememberlist) {$subsmenu .= qq~$menusep<a href="$cgi;action=memberlist">$img{'memberlist'}</a>~ ;}
				$subsmenu .= qq~$menusep<a href="$cgi;action=profile;username=$memberData[0]">$img{'profile'}</a>~;
				$subsmenu .= qq~$menusep<a href="$cgi;action=logout">$img{'logout'}</a>~;
			}
			addOnExecute("HomePage",1);
			$subsuname = $username eq 'Guest'
			 ? qq~$txt{'248'} $txt{'28'}. $txt{'249'} <a href="$cgi;action=login">$txt{'34'}</a> $txt{'377'} <a href="$cgi;action=register">$txt{'97'}</a>.~
			 : qq~$txt{'247'} $realname. ~ ;
			$subsimages = $imagesdir;
			$subsboardname = $mbname;
	    $subsmember = $memberData[8];
			($subsvisitdate,$subsvisits,$subslastvisitdate) = split(/ - /,$memberData[5]);

	}
}

sub template{
	if ($INFO{'template'}) {
		 inline($INFO{'template'});
	}elsif ($smalltemplate) {
		 inline("$datadir/stemplate.html");
	} else {
		 inline("$datadir/template.html");
	}
}

#Print fatal error
sub fatal_error {
	rollback_database();
	my $e = $_[0];
	$subsmain .= qq~
	<table border=0 width="80%" cellspacing=1 bgcolor="$color{'bordercolor'}" class="bordercolor" align="center" cellpadding="4">
  <tr>
    <td class="titlebg" bgcolor="$color{'titlebg'}"><font size=2 class="text1" color="$color{'titletext'}"><b>$txt{'106'}</b></font></td>
  </tr><tr>
    <td class="windowbg" bgcolor="$color{'windowbg'}"><BR><font size=2>$e</font><BR><BR></td>
  </tr>
	</table>
	<center><BR><a href="javascript:history.go(-1)">$txt{'250'}</a></center>
	~;
	$substitle = "$txt{'106'}";
	&template;
}

1;

