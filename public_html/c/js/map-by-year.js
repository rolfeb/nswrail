/*
 * Copyright (c) 2018. Rolfe Bozier
 */

function refresh_year() {
    e = document.getElementById('year')
    if (e)
        e.innerHTML = $('#slider').slider("value")
}
function refresh_map() {
    selected = $('#slider').slider("value")

    i = 1860
    while (i <= 2000)
    {
        e = document.getElementById(i)
        if (e)
        {
            if (i == selected)
                e.style.display = 'block';
            else
                e.style.display = 'none';
        }
        i += 5
    }
    e = document.getElementById('year')
    if (e)
        e.innerHTML = $('#slider').slider("value")
}
function loaded() {
    e = document.getElementById('loading')
    if (e)
        e.style.display = 'none';
    $('#slider').slider("value", "2000");
}
$(function(){
    $('#slider').slider({
        range: 'min',
        min: 1860,
        max: 2000,
        step: 5,
        slide: refresh_year,
        change: refresh_map,
    });
});
