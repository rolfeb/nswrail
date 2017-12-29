function load(wx, wy)
{
    if (GBrowserIsCompatible())
    {
        map = document.getElementById("map");

        var LocationMap = new GMap2(document.getElementById("map"));
        var centre = new GLatLng(wy, wx);

        LocationMap.addControl(new GSmallZoomControl());
        LocationMap.setCenter(centre, 14);

        // custom icon for the location
        var icon = new GIcon();
        icon.image = "images/crosshair.png";
        icon.iconSize = new GSize(63, 63);
        icon.iconAnchor = new GPoint(32, 32);
        LocationMap.addOverlay(new GMarker(centre, icon));

        // allow copyright text to wrap
        divs = map.getElementsByTagName("DIV");
        for (var i = 0; i < divs.length; i++)
        {
            if (divs[i].style.fontFamily != "")
            {
                divs[i].style.whiteSpace = "normal";
                break;
            }
        }

        // move the logo to the top right corner
        imgs = map.getElementsByTagName("IMG");
        for (var i = 0; i < imgs.length; i++)
        {
            src = imgs[i].src;
            if (src.match(".*poweredby.png"))
            {
                imgs[i].style.left = "";
                imgs[i].style.bottom = "";
                imgs[i].style.top = "2px";
                imgs[i].style.right = "2px";
                break;
            }
        }
    }
}

function editStart(name)
{
    if (e = document.getElementById('edit-' + name))
        e.style.display = 'block'
    if (e = document.getElementById('edit-' + name + '-prompt'))
        e.style.display = 'none'
}
function editSaving(name)
{
    if (e = document.getElementById('edit-' + name))
        e.style.display = 'none'
    if (e = document.getElementById('edit-' + name + '-working'))
        e.style.display = 'inline'
}
function editReenable(name)
{
    if (e = document.getElementById('edit-' + name + '-prompt'))
        e.style.display = 'inline'
    if (e = document.getElementById('edit-' + name + '-working'))
        e.style.display = 'none'
}
function editCancel(name)
{
    if (e = document.getElementById('edit-' + name))
        e.style.display = 'none'
    if (e = document.getElementById('edit-' + name + '-prompt'))
        e.style.display = 'inline'
}
function editCommit(name, state, location, version)
{
    var ui;         // UI values that will be updated

    var params1;    // default POST variables
    var params2;    // edit-specific POST variables

    params1 = 'state=' + state + "&location=" + location + "&version=" + version;

    if (name == 'facility')
    {
        e1 = document.getElementById('ev-facility-type');
        typeCode = e1.options[e1.selectedIndex].value;
        typeLabel = e1.options[e1.selectedIndex].text;

        e2 = document.getElementById('ev-facility-status');
        statusCode = e2.options[e2.selectedIndex].value;
        statusLabel = e2.options[e2.selectedIndex].text;

        params2 = "&type=" + typeCode + "&status=" + statusCode;

        ui = new Array();
        ui.push('v-facility-type'); ui.push(typeLabel);
        ui.push('v-facility-status'); ui.push(statusLabel);
    }
    else
    if (name == 'location')
    {
        x = document.getElementById('ev-location-x').value;
        y = document.getElementById('ev-location-y').value;

        params2 = "&location-x=" + x + "&location-y=" + y;

        ui = new Array();
        if (x != 0 && x != '' && y != 0 && y != '')
        {
            x = parseFloat(x);
            y = parseFloat(y);
            text = '(' + x.toFixed(4) + '&deg;, ' + y.toFixed(4) + ')';
        }
        else
            text = 'unknown';
        ui.push('v-location-xy'); ui.push(text);
    }
    else
    if (name == 'distance')
    {
        flt = document.getElementById('ev-distance').value;

        params2 = '&distance=' + flt;

        ui = new Array();

        if (flt != '')
        {
            flt = parseFloat(flt);
            text = flt.toFixed(3);
        }
        else
            text = 'unknown';
        ui.push('v-' + name); ui.push(text);
    }
    else
    if (name == 'desc' || name == 'curr')
    {
        str = document.getElementById('ev-' + name).value;

        params2 = '&' + name + '=' + str;

        ui = new Array();
        ui.push('v-' + name); ui.push(str);
    }
    else
    if (name == 'history')
    {
        for (i = 1; i <= 10; i++)
        {
            seq = i.toString();

            type = document.getElementById('ev-type' + seq).value;
            day = document.getElementById('ev-day' + seq).value;
            month = document.getElementById('ev-month' + seq).value;
            year = document.getElementById('ev-year' + seq).value;
            error = document.getElementById('ev-error' + seq).value;
            name = document.getElementById('ev-name' + seq).value;

            params2 += '&htype' + seq + '=' + type;
            params2 += '&hday' + seq + '=' + day;
            params2 += '&hmonth' + seq + '=' + month;
            params2 += '&hyear' + seq + '=' + year;
            params2 += '&herror' + seq + '=' + error;
            params2 += '&hname' + seq + '=' + name;
        }
    }

    if (params2)
    {
        var r = new AjaxUpdater();
        r.send_update(name, params1 + params2, ui);

        // remove the edit area, and show a "saving..." message
        editSaving(name);
    }
    else
    {
        // remove the edit area and re-enable the edit prompt
        editCancel(name);
    }
}

function AjaxUpdater()
{
    var req;
    var uichanges;
    var url = '/locations/update.php';

    req = _newRequest();
    req.open("POST", url, true);    // asynchronous
    req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

    req.onreadystatechange = function() {
        if (req.readyState == 4)
        {
            if (req.status == 200)
            {
                // retrieve the response from the server
                var xml = req.responseXML;
                var status = xml.getElementsByTagName('status')[0].childNodes[0].nodeValue;
                var text = xml.getElementsByTagName('text')[0].childNodes[0].nodeValue;

                if (status == 0)
                {
                    if (uichanges)
                    {
                        // update the UI with new values
                        for (i = 0; i < uichanges.length; i += 2)
                        {
                            if (e = document.getElementById(uichanges[i]))
                                e.innerHTML = uichanges[i+1];
                        }
                    }
                    else
                    {
                        // too hard to update, so force a reload
                        location.reload(true);
                    }
                }
                else
                    alert("Update failed: " + text);
            }
            else
            if (req.status == 404)
                alert(url + ': not found');
            else
                alert('update failed with status ' + req.status);

            // re-enable the edit prompt
            editReenable(name);
        }
    }

    this.send_update = function(_name, _postvars, _uichanges) {
        name = _name;
        uichanges = _uichanges;

        req.send(encodeURI(_postvars));
    }
}
