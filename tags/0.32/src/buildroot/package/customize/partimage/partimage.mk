#############################################################
#
# partimage
#
#############################################################
PARTIMAGE_VERSION:=0.6.9
PARTIMAGE_SOURCE:=partimage-$(PARTIMAGE_VERSION).tar.bz2
PARTIMAGE_SITE:=http://$(BR2_SOURCEFORGE_MIRROR).dl.sourceforge.net/project/partimage/stable/$(PARTIMAGE_VERSION)
PARTIMAGE_INSTALL_STAGING=YES
PARTIMAGE_LIBTOOL_PATCH=NO
PARTIMAGE_CONF_OPT= --with-ssl-headers=$(STAGING_DIR)/usr/include/openssl \
		    --without-libintl-prefix \
		    --program-transform-name= \
		    --with-log-dir=/dev/null  \
		    --disable-cheuid  \
		    --disable-login  \
		    --disable-ssl  \
		    --disable-nls 

PARTIMAGE_DEPENDENCIES=zlib bzip2 newt

$(eval $(call AUTOTARGETS,package/customize,partimage))
