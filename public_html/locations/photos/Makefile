# vi:noet

DEST		=	small

PHOTOS		=	$(wildcard *.jpg)
THUMBNAILS	=	$(addprefix $(DEST)/,$(PHOTOS))

GEN_THUMBNAIL	=	/work/websrc/bin/mkthumbnail
UPD_SIZES	=	/work/nswrail/bin/rebuild-photo-sizes

$(DEST)/%.jpg : %.jpg
	@$(RM) $@
	$(GEN_THUMBNAIL) $? $@
	@chmod 444 $@

all		:	$(THUMBNAILS)
	$(UPD_SIZES) .

clobber		:	clean
	$(RM) $(THUMBNAILS)

clean		:
