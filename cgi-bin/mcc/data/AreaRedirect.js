<script language="JavaScript1.2" type="text/javascript">
<!--
//Special functions are added to speed-up redirects in same frame.
//Use word .all to turn all redirects on or off see example below
//Replacing all by area name allows you to turn a specfic area on or off
//redirect.all=off
function AreaRedirect(action,area,uri,title,targetframe) {
 if (action == "popup") {
  window.open(uri);
 } else if (action == "redirect") {
  window.location=uri;
 } else if (action == "targetredirect") {
    i = 0;
    while (i < parent.length) {
       win = parent.frames[i];
       if (win.name == targetframe) {
           win.location=uri;
           return;
       }
       i = i + 1;
    }
    window.location=uri;
 } else {
   window.location=uri;
 }
}
// -->
</script>