<!-- BEGIN MAIN -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<title>{TITLE}</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="/lib/menu.js"></script>
</head>
<body onload="fixmenu()">

<!-- BEGIN CONTROLS -->
{TOP}
{MENU}
<!-- END CONTROLS -->

<div class="mbcontent">

<h1>{TITLE}</h1>

The following search form can be used to search for articles from the
<span class="journal-title">ARHS Bulletin</span> magazine (now called
<span class="journal-title">Australian Railway History</span>).
Note that the articles themselves are not on line. The index covers the years
from 1937 to {LATEST} (some entries are missing for the early 1990s).
<p/>
The data is almost completely the responsibility of the following people: 

<ul>
<li>Howard Quinlan (compiled original data from 1937 to 1987)</li>
<li>David Virgo (converted to electronic form)</li>
<li>Geoff Lambert (corrected data and added data up to Dec 2000)</li>
<li>Rolfe Bozier (corrected data and added data up to {LATEST})</li>
</ul>

<form method="get" action="bulletin_results.php" enctype="multipart/form-data">
<table class="simple">
<tr>
    <td><b>Title:</b></td>
    <td>
        <input type="text" name="titlekeywords" tabindex="1"  size="60" maxlength="60" />
        <select name="titlejoin" tabindex="2">
            <option selected="selected" value="all">all of</option>
            <option value="any">any of</option>
        </select>
    </td>
</tr>
<tr>
    <td>
    <b>Author:</b></td>
    <td>
        <input type="text" name="authorkeywords" tabindex="3"  size="40" maxlength="40" />
    </td>
</tr>
<tr>
    <td><b>Description:</b></td>
    <td>
        <input type="text" name="synopsiskeywords" tabindex="4"  size="60" maxlength="60" />
        <select name="synopsisjoin" tabindex="5">
            <option selected="selected" value="all">all of</option>
            <option value="any">any of</option>
        </select>
    </td>
</tr>
<tr>
    <td><b>Volume:</b></td>
    <td>
        <input type="text" name="volume" tabindex="6"  size="4" maxlength="4" />
        <select name="volumetype" tabindex="7">
            <option selected="selected" value="new">New series</option>
            <option value="old">Old series</option>
        </select>
    </td>
</tr>
<tr>
    <td><b>Number:</b></td>
    <td>
        <input type="text" name="issue" tabindex="8"  size="4" maxlength="4" />
    </td>
</tr>
<tr>
    <td><b>Month:</b></td>
    <td>
        <select name="month" tabindex="9">
            <option selected="selected" value=""></option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
    </td>
</tr>
<tr>
    <td><b>Year:</b></td>
    <td>
        <input type="text" name="year" tabindex="10"  size="4" maxlength="4" />
    </td>
</tr>
</table>
<p/>
<div>
<input type="reset" tabindex="11" name="Reset Form" value="Reset Form" />
<input type="submit" tabindex="12" name="Perform Search" value="Perform Search" />
<input type="hidden" name="searchmode" value="1"  />
</div>
</form>

</div>

</body>
</html>
<!-- END MAIN -->
