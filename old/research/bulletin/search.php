<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../rail.css">
<link rel="shortcut icon" type="image/gif" href="../../images/railicon.gif">
<title>ARHS Bulletin Search Form</title>
</head>
<body class="body">

<?php
$f_title = $HTTP_POST_VARS['f_title'];
$f_titlejoin = $HTTP_POST_VARS['f_titlejoin'];
$f_author = $HTTP_POST_VARS['f_author'];
$f_synopsis = $HTTP_POST_VARS['f_synopsis'];
$f_synopsisjoin = $HTTP_POST_VARS['f_synopsisjoin'];
$f_volume = $HTTP_POST_VARS['f_volume'];
$f_volumetype = $HTTP_POST_VARS['f_volumetype'];
$f_issue = $HTTP_POST_VARS['f_issue'];
$f_month = $HTTP_POST_VARS['f_month'];
$f_year = $HTTP_POST_VARS['f_year'];
$f_searchmode = $HTTP_POST_VARS['f_searchmode'];

echo "<h1>ARHS Bulletin Search Form</h1>\n";
echo "<div class=\"normal\">\n";

#
# The latest version:
#
$LATEST = "April 2002";

$MAXRESULTS = 200;

if ($f_searchmode)
{
	/*
	 * Perform the actual search
	 */
	echo "<h2>Results:</h2>\n";

	if ($fd = fopen("./arhsbull.dat", "r"))
	{
		$matches = 0;

		$curr_issue = 0;

		/*
		 * Read in an entry at a time and match it against the
		 * search parameters
		 */
		while ($line = fgets($fd, 4096))
		{
			list($title, $authors, $synopsis,
				$volume, $issue, $month, $year, $pages)
				= explode("|", chop($line));
			
			if ($f_title)
			{
				$keywords = explode(' ', $f_title);

				if (strcmp($f_titlejoin, "Any of") == 0)
				{
					$any_match = 0;
					for ($i = 0; $i < count($keywords); $i++)
					{
						if (stristr($title, $keywords[$i]))
						{
							$any_match = 1;
							break;
						}
					}

					if (!$any_match)
						continue;
				}
				else
				{
					$all_match = 1;
					for ($i = 0; $i < count($keywords); $i++)
					{
						if (!stristr($title, $keywords[$i]))
						{
							$all_match = 0;
							break;
						}
					}
					if (!$all_match)
						continue;
				}
			}
			if ($f_author)
			{
				$keywords = explode(' ', $f_author);

				$any_match = 0;
				for ($i = 0; $i < count($keywords); $i++)
				{
					if (stristr($authors, $keywords[$i]))
					{
						$any_match = 1;
						break;
					}
				}

				if (!$any_match)
					continue;
			}
			if ($f_synopsis)
			{
				$keywords = explode(' ', $f_synopsis);

				if (strcmp($f_synopsisjoin, "Any of") == 0)
				{
					$any_match = 0;
					for ($i = 0; $i < count($keywords); $i++)
					{
						if (stristr($synopsis, $keywords[$i]))
						{
							$any_match = 1;
							break;
						}
					}

					if (!$any_match)
						continue;
				}
				else
				{
					$all_match = 1;
					for ($i = 0; $i < count($keywords); $i++)
					{
						if (!stristr($synopsis, $keywords[$i]))
						{
							$all_match = 0;
							break;
						}
					}
					if (!$all_match)
						continue;
				}
			}
			if ($f_volume)
			{
				if (strcmp($f_volumetype, "Old") == 0)
					$str = "$volume (Old)";
				else
					$str = "$volume";

				if (strcasecmp($volume, $match) != 0)
					continue;
			}
			if ($f_issue)
			{
				if ($issue != $f_issue)
					continue;
			}
			if ($f_month)
			{
				if (strcmp($month, $f_month) != 0)
					continue;
			}
			if ($f_year)
			{
				if ($year != $f_year)
					continue;
			}


			/*
			 * If we reach this point, the article has matched
			 */
			if ($count > $MAXRESULTS)
			{
				echo "
</table>
<hr noshade>
<p>
<font color=\"#a00000\">
<b>Results truncated</b>: maximum of $MAXRESULTS references will be returned.
</font>
<p>
<hr noshade>
<p>
";
				break;
			}

			if ($count == 0)
				echo "<table cellpadding=0 cellspacing=0 border=0>\n";
			
			if ($issue != $curr_issue)
			{
				echo "<tr><td colspan=2><br><b>$month $year</b> (Vol $volume No. $issue)</td></tr>";
				$curr_issue = $issue;
			}
			
			if (!$authors)
			{
				$authors = "unknown";
			}

			echo "
<tr>
<td width=\"10%\">&nbsp;</td>
<td><b>\"$title\" ($authors)</b></td>
</tr>
";
			if ($synopsis)
			{
				echo "
<tr valign=top>
<td width=\"10%\">&nbsp;</td>
<td class=normal>$synopsis</td>
</tr>
";
			}

			echo "
<tr>
<td width=\"10%\">&nbsp;</td>
<td>page[s]: $pages</td>
</tr>
";

			$count++;
		}

		if ($count <= $MAXRESULTS)
		{
			echo "
</table>
<hr noshade>
<p>
";
		}

		fclose($fd);
	}
	else
	{
		echo "<p><b>Failed to open database</b><p>\n";
	}

	echo "
Perform another <a href=\"search.php3\">search</a>.
</div>
</body>
</html>
";

	exit;

}
else
{
	echo "
The following search form can be used to search for articles from the
ARHS Bulletin magazine (note that the articles themselves are
<em>not</em> on line).  The index covers the years from 1937 to $LATEST
(some entries are missing for the early 1990s).
<p>
The data is almost completely the responsibility of the following people:
<ul>
<li> Howard Quinlan (compiled original data from 1937 to 1987)
<li> David Virgo (converted to electronic form)
<li> Geoff Lambert (corrected data and added data up to Dec 2000)
<li> Rolfe Bozier (corrected data and added data up the current date)
</ul>
";
}

?>


<form action="search.php3" method=post>
<table cellpadding=0 cellspacing=0 border=0>
<tr>
<td><b>Title:</b></td>
<td>
  <table cellpadding=0 cellspacing=0 border=0><tr>
  <td><input name="f_title" size=60 maxlength=60></td>
  <td>
    <select name="f_titlejoin">
      <option selected>All of
      <option>Any of
    </select>
  </td>
  </tr></table>
</td>
</tr>

<tr>
<td><b>Author:</b></td>
<td><input name="f_author" size=40 maxlength=40></td>
</tr>

<tr>
<td><b>Description:</b></td>
<td>
  <table cellpadding=0 cellspacing=0 border=0><tr>
  <td><input name="f_synopsis" size=60 maxlength=60></td>
  <td>
    <select name="f_synopsisjoin">
      <option selected>All of
      <option>Any of
    </select>
  </td>
  </tr></table>
</td>
</tr>

<tr>
<td><b>Volume:</b></td>
<td>
  <table cellpadding=0 cellspacing=0 border=0><tr>
  <td><input name="f_volume" size=4 maxlength=4></td>
  <td>
    <select name="f_volumetype">
      <option selected value="New">New Series
      <option value="Old">Old Series
    </select>
  </td>
  </tr></table>
</td>
</tr>

<tr>
<td><b>Number:</b></td>
<td><input name="f_issue" size=4 maxlength=4></td>
</tr>

<tr>
<td><b>Month:</b></td>
<td>
  <select name="f_month">
    <option selected>
    <option value=Jan>January
    <option value=Feb>February
    <option value=Mar>March
    <option value=Apr>April
    <option value=May>May
    <option value=Jun>June
    <option value=Jul>July
    <option value=Aug>August
    <option value=Sep>September
    <option value=Oct>October
    <option value=Nov>November
    <option value=Dec>December
  </select>
</td>
</tr>

<tr>
<td><b>Year:</b></td>
<td><input name="f_year" size=4 maxlength=4></td>
</tr>
</table>

<br>
<input type=reset value="Reset Form">
<input type=submit value="Perform Search">
<input type=hidden name="f_searchmode" value=1>
</form>
</div>
</body>
</html>
