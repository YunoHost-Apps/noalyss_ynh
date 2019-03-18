#!/bin/bash

# Create a master password and set up global settings
# Please always call this script in install and restore scripts
# usage: noalyss_psql_test_if_first_run

noalyss_psql_test_if_first_run() {
	if [ -f /etc/yunohost/psql ];
	then
		echo "PostgreSQL is already installed, no need to create master password"
	else
		local pgsql="$(ynh_string_random)"
		echo "$pgsql" > /etc/yunohost/psql

		if [ -e /etc/postgresql/9.4/ ]
		then
			local pg_hba=/etc/postgresql/9.4/main/pg_hba.conf
		elif [ -e /etc/postgresql/9.6/ ]
		then
			local pg_hba=/etc/postgresql/9.6/main/pg_hba.conf
		else
			ynh_die "postgresql shoud be 9.4 or 9.6"
		fi

		systemctl start postgresql
		sudo --login --user=postgres psql -c"ALTER user postgres WITH PASSWORD '$pgsql'" postgres

		# force all user to connect to local database using passwords
		# https://www.postgresql.org/docs/current/static/auth-pg-hba-conf.html#EXAMPLE-PG-HBA.CONF
		# Note: we cant use peer since YunoHost create users with nologin
		# See: https://github.com/YunoHost/yunohost/blob/unstable/data/helpers.d/user
		sed -i '/local\s*all\s*all\s*peer/i \
			local all all password' "$pg_hba"
		systemctl enable postgresql
		systemctl reload postgresql
	fi
}

# Open a connection as a user
#
# example: noalyss_psql_connect_as 'user' 'pass' <<< "UPDATE ...;"
# example: noalyss_psql_connect_as 'user' 'pass' < /path/to/file.sql
#
# usage: noalyss_psql_connect_as user pwd [db]
# | arg: user - the user name to connect as
# | arg: pwd - the user password
# | arg: db - the database to connect to
noalyss_psql_connect_as() {
	local user="$1"
	local pwd="$2"
	local db="$3"
	sudo --login --user=postgres PGUSER="$user" PGPASSWORD="$pwd" psql "$db"
}

# # Execute a command as root user
#
# usage: noalyss_psql_execute_as_root sql [db]
# | arg: sql - the SQL command to execute
noalyss_psql_execute_as_root () {
	local sql="$1"
	sudo --login --user=postgres psql <<< "$sql"
}

# Execute a command from a file as root user
#
# usage: noalyss_psql_execute_file_as_root file [db]
# | arg: file - the file containing SQL commands
# | arg: db - the database to connect to
noalyss_psql_execute_file_as_root() {
	local file="$1"
	local db="$2"
	sudo --login --user=postgres psql "$db" < "$file"
}

# Create a database, an user and its password. Then store the password in the app s config
#
# After executing this helper, the password of the created database will be available in $db_pwd
# It will also be stored as "psqlpwd" into the app settings.
#
# usage: noalyss_psql_setup_db user name [pwd]
# | arg: user - Owner of the database
# | arg: name - Name of the database
# | arg: pwd - Password of the database. If not given, a password will be generated
noalyss_psql_setup_db () {
	local db_user="$1"
	local db_name="$2"
	local new_db_pwd=$(ynh_string_random)	# Generate a random password
	# If $3 is not given, use new_db_pwd instead for db_pwd.
	local db_pwd="${3:-$new_db_pwd}"
	noalyss_psql_create_db "$db_name" "$db_user" "$db_pwd"	# Create the database

  # sudo yunohost app setting "$app" "$key" --value="$value" --quiet
  # sudo yunohost app setting noalyss psqlpwd -v=RhWNxNe6KdrncQdhbBvWsur6 --quiet
	ynh_app_setting_set $app psqlpwd $db_pwd	# Store the password in the app s config
}

# Create a database and grant privilegies to a user
#
# usage: noalyss_psql_create_db db [user [pwd]]
# | arg: db - the database name to create
# | arg: user - the user to grant privilegies
# | arg: pwd  - the user password
noalyss_psql_create_db() {
	local db="$1"
	local user="$2"
	local pwd="$3"
	noalyss_psql_create_user "$user" "$pwd"
	sudo --login --user=postgres createdb --owner="$user" "$db"
}

# Drop a database
#
# usage: noalyss_psql_remove_db user
# | arg: user - the user to drop
noalyss_psql_remove_db() {
	local user="$1"
	# because the user may have multiple databases
	sudo su postgres --login -c "psql -l | grep \"\<$user\>\" | awk '{print \$1}' | xargs -l dropdb"
	noalyss_psql_drop_user "$user"
}

# Dump a database
#
# example: noalyss_psql_dump_db 'roundcube' > ./dump.sql
#
# usage: noalyss_psql_dump_db user
# | arg: db - the database name to dump
# | ret: the psqldump output
noalyss_psql_dump_db() {
	local user="$1"
	sudo su postgres --login -c "psql -l | grep \"\<$user\>\" | awk '{print \$1}' | xargs -l pg_dump -C"
}


# Create a user
#
# usage: noalyss_psql_create_user user pwd [host]
# | arg: user - the user name to create
# NOTICE : for Noalyss the user need to be able to createdb
noalyss_psql_create_user() {
	local user="$1"
	local pwd="$2"
	sudo --login --user=postgres psql -c"CREATE USER $user createdb PASSWORD '$pwd'" postgres
}

# Drop a user
#
# usage: noalyss_psql_drop_user user
# | arg: user - the user name to drop
noalyss_psql_drop_user() {
	local user="$1"
	sudo --login --user=postgres dropuser "$user"
}


