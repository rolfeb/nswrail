<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

This a Google <a href="http://www.google.com/cse/">Custom Search</a> page.
Basically it is a subset of Google's search that only searches pages that
are relevant to NSW railways.  It does this by restricting searches to
just a customisable list of sites.

<div id="cse" style="width: 100%;">Loading</div>
<script src="http://www.google.com/jsapi" type="text/javascript"></script>
<script type="text/javascript">
  google.load('search', '1');
  google.setOnLoadCallback(function() {
    var customSearchControl = new google.search.CustomSearchControl('015811549446532285071:_6o5bjxzw2e');
    customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
    var options = new google.search.DrawOptions();
    options.setAutoComplete(true);
    customSearchControl.draw('cse', options);
  }, true);
</script>
<link rel="stylesheet" href="http://www.google.com/cse/style/look/default.css" type="text/css" />
<!-- END CONTENT -->
