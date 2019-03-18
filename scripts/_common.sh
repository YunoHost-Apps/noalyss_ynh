
#=================================================
# COMMON VARIABLES
#=================================================
# TODO : remove p7zip-full if not needed
# for jessie
pkg_dependencies="postgresql php5 php5-pgsql php5-gd php-gettext p7zip-full libgd2-xpm-dev"

if [ "$(lsb_release --codename --short)" != "jessie" ]; then
	pkg_dependencies="postgresql php-pgsql php-gd php-gettext p7zip-full php-zip php-bcmath"
fi

#=================================================
# COMMON HELPERS
#=================================================

# None for the moment.