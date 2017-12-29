<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="rail.css">
<title>Location Search</title>
</head>
<body class="body">

<?php
if (($loc = $_POST['location']) == "")
	$loc = $_GET['location'];

if (($nodiags = $_POST['nodiags']) == "")
	$nodiags = $_GET['nodiags'];

if (($exactmatch = $_POST['exactmatch']) == "")
	$exactmatch = $_GET['exactmatch'];

function locn_to_file($name)
{
	$s = str_replace(" ", "_", $name);
	$s = str_replace("(", "", $s);
	$s = str_replace(")", "", $s);
	$s = strtolower($s);

	return $s;
}

?>

<h1>Location Search</h1>

<form action="lsearch.php" method="GET">
<table border=0>
<tr valign=middle>
<td><span class=label>New Search:</span></td>

<?php
echo "<td><input type=\"text\" name=\"location\" size=\"15\" value=\"$loc\"></td>\n";
?>
<td><input type=submit value="go"></td>
</tr>
</table>
</form>

<hr noshade>

<?php

echo "<!-- location=$loc -->\n";
echo "<!-- nodiags=$nodiags -->\n";

if ($loc)
{
	echo "<div class=\"normal\">\n";

	if (($fd1 = fopen("./locdb.idx", "r"))
		&& ($fd2 = fopen("./locdb.dat", "r")))
	{
		#
		# Convert location to a suitable regular expression
		#
		$re = preg_quote($loc);
		$re = preg_replace(array("/%/", "/_/"), array(".*", "."), $re);

		if ($exactmatch)
			$re = "^$re\$";

		echo "<table border=0>\n";
		echo "<tr><td><b>Location</b></td> <td width=\"10\">&nbsp;</td> <td><b>Line[s]</b></td></tr>\n";
		
		$count = 0;

		while ($line = fgets($fd1, 128))
		{
			list($name, $offset) = explode(":", chop($line));

			if (preg_match("/$re/i", $name))
			{
				#
				# Get the data
				#
				fseek($fd2, $offset);
				$data = fgets($fd2, 4096);

				$count++;

				$line_hrefs = array();
				$line0 = "";

				list($lines) = explode("|", rtrim($data));

				$lines_arr = explode(",", $lines);

				for ($i = 0; $i < count($lines_arr); $i++)
				{
					list($lname, $ldesc) = explode(":", $lines_arr[$i]);

					$href = "lines/$lname/index.html";

					$line_hrefs[] = "<a href=\"$href\">$ldesc</a>";

					if ($line0 == "")
						$line0 = $lname;
				}


				$lines = join(", ", $line_hrefs);

				$fname = locn_to_file($name);

				echo "<tr valign=\"top\"><td><a href=\"lines/$line0/$fname.html\">$name</a></td> <td width=\"10\">&nbsp;</td> <td>$lines</td> </tr>\n";
			}
		}

		echo "</table>\n";

	}
	else
	{
		echo "ERROR: failed to open database<br>\n";
	}
	if ($fd1)
		fclose($fd1);
	if ($fd2)
		fclose($fd2);

	echo "</div>\n";
}
?>

</body>
</html>
