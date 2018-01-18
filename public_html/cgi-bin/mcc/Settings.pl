###############################################################################
# Settings.pl                                                                 #
###############################################################################

$language = "english.lng";                                            # Change to language pack you wish to use
$mbname = "Member Control Centre";                                    # The name of your site
$databasedriver = "TextFiles.dbd";

########## Files ##########
#
$membersFile ="data/members.db";                                           # Name of the file containing the members
$areasFile  ="data/areas.db";                                              # Name of the file containing the areas
$groupsFile ="data/groups.db";                                             # Name of the file containing the groups

########## Cookie ##########
#
$Cookie_Length = 360;                                                 # Default minutes to set login cookies to stay for
$cookieusername = "cookieUsername";                                   # Name of the username cookie
$cookiepassword = "cookiePassword";                                   # Name of the password cookie

########## Mail ##########
#
$mailprog = "/usr/lib/sendmail";                                      # Location of your sendmail program
$smtp_server = "smtp.yoursite.com";                                   # Address of your SMTP-Server
$webmaster_email = q^dummy@yoursite.com^;                             # Your email address. (eg: $webmaster_email = q^admin@host.com^;)
$mailtype = 0;                                                        # Mail program to use: 0 = sendmail, 1 = SMTP, 2 = Net::SMTP

########## Directories ##########
# Note: directories other than $imagesdir do not have to be changed unless you move things
$rootdir = ".";                             # The server path to the board's folder (usually can be left as '.')
$sourcedir = "source";                                                # Directory with source files
$datadir = "data";
$addondir = "addon";
$imagesdir = "";                  # URL to your images folder
$faderpath = "";        # URL to your 'fader.js'
$helpfile = "";          # URL to your help file
$rooturl = "";                   # URL to CGI directory with MCC.PL
$smalltarget="_blank";

########## Colors ##########
# Note: equivalent to colors in CSS tag of template.html, so set to same colors preferrably
# for browsers without CSS compatibility and for some items that don't use the CSS tag
$color{'titlebg'} = "#6E94B7";                                        # Background color of the 'title-bar'
$color{'titletext'} = "#FFFFFF";                                      # Color of text in the 'title-bar' (above each 'window')
$color{'windowbg'} = "#AFC6DB";                                       # Background color for messages/forms etc.
$color{'windowbg2'} = "#F8F8F8";                                      # Background color for messages/forms etc.
$color{'windowbg3'} = "#6394BD";                                      # Color of horizontal rules in posts
$color{'catbg'} = "#DEE7EF";                                          # Background color for category (at Board Index)
$color{'bordercolor'} = "#6394BD";                                    # Table Border color for some tables
$color{'fadertext'}  = "#D4AD00";                                     # Color of text in the NewsFader (news color)

########## Layout ##########
$shownewsfader = 0;                                                   # 1 to allow or 0 to disallow NewsFader javascript on the Board Index
$servertype = 0;                                                      # Set to 0 if you are running on a Unix/Linux webhost, set to 1 if you are running on windows.

########## Feature Settings ##########
$ItemsPerPage = 30;                                                  # No. of items to display per page of  List - All
$fadertime = 5000;                                                    # Length in milliseconds to delay between each item in the news fader
$maxinactive = 30;                                                    #Number of days that a member is allowed to stay inactive before removal
$logging = 0;                                                         #0=Disabled,1=Enabled logging is added to end of html page
$defaultgroups = "";                                                  #These groups are assigned to each new member
$shownotauthorized = 1;                                               #show the not authorized at bottom of default page
$defaultmemberstate = 1;                                              #is member active on load
$passwordtext="(Min. length 5, max length 15)";
$passwordfilter= q<\A[\s0-9A-Za-z!@#$%\^&*\(\)_\+|`~\-=\\:;\'\",\.\/?\[\]\{\}]{5,15}\Z>;
$crypt_method=0;
$disableregister=0;                                                   #1 will trap errors into html window
$timeoffset = 0;                                                      #Timeoffset in hours

########## File Locking ##########
$use_flock = 1;                                                       # Set to 0 if your server doesn't support file locking,
                                                                      # 1 for Unix/Linux and WinNT, and 2 for Windows 95/98/ME
$traperrors = 1;																											# 1 Error trapping is on

$faketruncation = 0;                                                  # Enable this option only if fails with the error:
                                                                      # "truncate() function not supported on this platform."
                                                                      # 0 to disable, 1 to enable.

1;
