#!/bin/sh

montage nsw-network-1933-{nw,ne,sw,se}.jpg -geometry 400x300+5+5 -tile 2x2 -border 1 -bordercolor '#ff0000' nsw-network-1933-overview.png

montage sydney-network-1974-{nw,ne,sw,se}.jpg -geometry 287x310+5+5 -tile 2x2 -border 1 -bordercolor '#ff0000' sydney-network-1974-overview.png
