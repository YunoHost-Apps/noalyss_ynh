
#=================================================
# COMMON VARIABLES
#=================================================

YNH_PHP_VERSION="8.0"

pkg_dependencies="postgresql apt-transport-https libgd-dev php${YNH_PHP_VERSION}-pgsql php${YNH_PHP_VERSION}-zip php${YNH_PHP_VERSION}-mbstring php${YNH_PHP_VERSION}-bcmath php${YNH_PHP_VERSION}-xml php${YNH_PHP_VERSION}-gmp php${YNH_PHP_VERSION}-gd php-php-gettext"

#=================================================
# COMMON HELPERS
#=================================================

#=================================================
# EXPERIMENTAL HELPERS
#=================================================

# Send an email to inform the administrator
#
# usage: ynh_send_readme_to_admin app_message [recipients]
# | arg: app_message - The message to send to the administrator.
# | arg: recipients - The recipients of this email. Use spaces to separate multiples recipients. - default: root
#	example: "root admin@domain"
#	If you give the name of a YunoHost user, ynh_send_readme_to_admin will find its email adress for you
#	example: "root admin@domain user1 user2"
ynh_send_readme_to_admin() {
	local app_message="${1:-...No specific information...}"
	local recipients="${2:-root}"

	# Retrieve the email of users
	find_mails () {
		local list_mails="$1"
		local mail
		local recipients=" "
		# Read each mail in argument
		for mail in $list_mails
		do
			# Keep root or a real email address as it is
			if [ "$mail" = "root" ] || echo "$mail" | grep --quiet "@"
			then
				recipients="$recipients $mail"
			else
				# But replace an user name without a domain after by its email
				if mail=$(ynh_user_get_info "$mail" "mail" 2> /dev/null)
				then
					recipients="$recipients $mail"
				fi
			fi
		done
		echo "$recipients"
	}
	recipients=$(find_mails "$recipients")

	local mail_subject="☁️🆈🅽🅷☁️: \`$app\` has important message for you"

	local mail_message="This is an automated message from your beloved YunoHost server.
Specific information for the application $app.
$app_message
---
Automatic diagnosis data from YunoHost
$(yunohost tools diagnosis | grep -B 100 "services:" | sed '/services:/d')"
	
	# Define binary to use for mail command
	if [ -e /usr/bin/bsd-mailx ]
	then
		local mail_bin=/usr/bin/bsd-mailx
	else
		local mail_bin=/usr/bin/mail.mailutils
	fi

	# Send the email to the recipients
	echo "$mail_message" | $mail_bin -a "Content-Type: text/plain; charset=UTF-8" -s "$mail_subject" "$recipients"
}

#### List all existing PostgreSQL databases associated with a given user
#
# [internal]
#
# usage: ynh_psql_list_user_dbs db_user
# | arg: db_user  - the PostgreSQL role/user of which to list all owned databases
ynh_psql_list_user_dbs() {
	local db_user=$1
	
	if ynh_psql_user_exists --user=$db_user; then # Check that the db_user exists
		local sql="COPY (SELECT datname FROM pg_database JOIN pg_authid ON pg_database.datdba = pg_authid.oid WHERE rolname = '${db_user}') TO STDOUT"
		local dbs_list=ynh_psql_execute_as_root --sql="$sql" # Fetch database(s) associated to role $db_user
		return dbs_list
	else
		ynh_print_err --message="User \'$db_user\' does not exist"
		return ""
	fi
}

#### Remove all existing PostgreSQL databases associated with a given user
#
# usage: ynh_psql_remove_all_user_dbs --db_user=db_user
# | arg: -u, --db_user=    - the PostgreSQL role/user of which to remove all owned databases
#
# This can be useful to prepare the removal of a given user.
ynh_psql_remove_all_user_dbs() {
    # Declare an array to define the options of this helper.
    local legacy_args=u
    local -A args_array=([u]=db_user=)
    local db_user
    # Manage arguments with getopts
    ynh_handle_getopts_args "$@"
	
	local dbs_to_drop = ynh_psql_list_user_dbs $db_user
	if [ -n "$dbs_to_drop" ]; then	   # Check that the list of database(s) is not empty 
		local db_name
		for $db_name in $dbs_to_drop   # Iterate through the list of database(s) to remove
		do
			if ynh_psql_database_exists --database=$db_name; then # Check if the database exists
				ynh_psql_drop_db $db_name                         # Remove the database
				ynh_print_info --message="Removed database $db_name associated to role $db_user"
			else
				ynh_print_warn --message="Database $db_name not found"
			fi
		done
	else 
		ynh_print_warn --message="No associated database to role $db_user was found"
	fi
}

# Dump all existing PostgreSQL databases associated with a given user
#
# usage: ynh_psql_dump_all_user_dbs --db_user=db_user [--app=app]
# | arg: -u, --db_user=    - the PostgreSQL role/user of which to remove all owned databases
# | arg: -a, --app=     - the application id to tag the dump with
# Requires YunoHost version 3.5.0 or higher.
ynh_psql_dump_all_user_dbs() {
    # Declare an array to define the options of this helper.
    local legacy_args=ua
    local -A args_array=([u]=db_user= [a]=app=)
    local db_user
    local app
    # Manage arguments with getopts
    ynh_handle_getopts_args "$@"
    app="${app:-}"
	
	local dbs_to_dump = ynh_psql_list_user_dbs $db_user
	if [ -n "$dbs_to_dump" ]; then	   # Check that the list of database(s) is not empty 
		
		local db_name
		for $db_name in $dbs_to_dump   # Iterate through the list of database(s) to dump
		do
			if ynh_psql_database_exists --database=$db_name; then # Check if the database exists
				ynh_psql_dump_db $db_name > "$app-$db_name-dump.sql" # Dump the database to a filename format of app-db_name-dump.sql, or of db_name-dump.sql if app parameter was not supplied  
				ynh_print_info --message="Dumped database $db_name associated to role $db_user"
			else
				ynh_print_warn --message="Database $db_name not found"
			fi
		done
	else 
		ynh_print_warn --message="No associated database to role $db_user was found"
	fi
}

# Restore all dumped PostgreSQL databases for the given app
#
# usage: ynh_psql_restore_all_app_dbs_dumps --db_user=db_user [--db_user_pwd=db_user_pwd]
# | arg: -u, --db_user=    		- the PostgreSQL role/user which will own the restored databases. If not existing, it will be created.
# | arg: -p, --db_user_pwd= 	- the password associated to the PostgreSQL role/user which will own the databases. If not existing, it will be generated and saved to the app's config file.
#	
# SQL dump files to restore must be named according to this format "app_id-db_name-dump.sql" and be located in the app folder 
# The filename format requirement is made so to match the files dumped with ynh_psql_dump_all_user_dbs --user=user --app=app (with both parameters specified).
# 
# Requires YunoHost version 2.7.13 or higher.
ynh_psql_restore_all_app_dbs_dumps(){
    # Declare an array to define the options of this helper.
    local legacy_args=up
    local -A args_array=([u]=db_user= [p]=db_user_pwd=)
    local db_user
    local db_user_pwd
    # Manage arguments with getopts
    ynh_handle_getopts_args "$@"	
	
	if [ -z "$app" ]; then
		ynh_die --message="No global app_ID variable defined in the script"
	fi

	ynh_psql_test_if_first_run  # Make sure PSQL is installed
	
	local filename
	for filename in *-dump.sql	# Loop among all files ending with "-dump.sql" in the current folder  
	do	
		local db_name
		db_name="${filename#${app}-}"							# Remove "$app-" prefix from filename string to parse db_name. Will do nothing if there is no match.
		db_name="${db_name%-dump.sql}"							# Remove "-dump.sql" suffix from filename string to parse db_name. Will do nothing if there is no match.
		db_name=ynh_sanitize_dbid --db_name="$db_name"
		
		if [[ "${filename#${app}-}" = "$filename" || -z "$db_name" ]] ; then  	# Check whether app_ID is included in filename OR $db_name is empty
			ynh_print_warn --message="File ignored: $filename. Filename not matching expected format (appID-db_name-dump.sql)"
			continue												
		else														
		    db_user_pwd="${db_user_pwd:-}"
			if [ -z "$db_user_pwd" ]; then 
				db_user_pwd=$(ynh_app_setting_get --app=$app --key=psqlpwd) 				# Try to retrieve db_user_pwd from the app's settings. It may prove empty during the first loop, but will get populated before the second loop by ynh_psql_setup_db() below.   
			fi
			
			ynh_psql_setup_db --db_user=$db_user --db_name=$db_name --db_pwd=$db_user_pwd	# Check that the db_user exists or create it generating a random password and then create an empty database named $db_name.  
			ynh_psql_execute_file_as_root --file="./${filename}" --database=$db_name		# Restore the dabatase from the corresponding dump file
			
			ynh_print_info --message="Restored database $db_name, owned by PostgreSQL user $db_user"
		fi
	done	
}
