#!/usr/bin/env bash

# Dependencies
# - Git, Mysql, PHP

# Get parameters when script is invoke
while [[ "$#" -gt 0 ]]; do
    case $1 in
	-h|--help) option=1 ;;
	-c|--create) option=2 ;;
	-p|--perish) option=3 ;;
	-a|--add-users) option=4 ;;
	-s|--drop-users) option=5 ;;
        *) echo "\n-> Unknown parameter passed: $1"; exit 1 ;;
    esac
    shift
done

# Functions
function _help {
	echo -e '\n-> Help';
	echo '- Create: -c or --create';
	echo '- Perish: -p or --perish';
	echo '- Add users: -a or --add-users';
	echo '- Drop users: -s or --drop-users';
}

function _create_db {
	sudo mysql -u root -Bse 'CREATE DATABASE company; USE company; CREATE TABLE users (id int NOT NULL AUTO_INCREMENT PRIMARY KEY,user varchar(16),theme varchar(8),type varchar(8),password varchar(64));CREATE TABLE reports (id int NOT NULL AUTO_INCREMENT PRIMARY KEY,mac varchar(12),name varchar(64),telephone varchar(32),tag varchar(32),locations json,relation varchar(32),model varchar(32),manufacturer varchar(32),downloaded int,uploaded int);'
	echo -e '\n-> Database created'
}

function _drop_db {
	sudo mysql -u root -Bse 'DROP DATABASE company;'
	echo -e '\n-> Database perished'
}

function _add_users {
	sudo mysql -u root -Bse "CREATE USER 'read'@'%' IDENTIFIED BY '123';GRANT SELECT ON company.* TO 'read'@'%';FLUSH PRIVILEGES;CREATEUSER 'login'@'%' IDENTIFIED BY '123';GRANT SELECT ON company.* TO 'login'@'%';FLUSH PRIVILEGES;CREATE USER 'connector'@'%' IDENTIFIED BY '123';GRANT SELECT ON company.* TO 'connector'@'%';FLUSH PRIVILEGES;"
	echo -e '\n-> Users added'
	echo -e '- Remember that this script is a shortcut, users have pratically total acess to your database'
}

function _drop_users {
	sudo mysql -u root -Bse "DROP USER 'read'; DROP USER 'connector'; DROP USER 'login';"
}


# Check if user has git
if ! command -v git &> /dev/null
then
	echo -e '\n-> Git is not installed'
else
	# Now check which function to execute
	case $option in
		# Use dir specified
		1) _help;;
		2) _create_db;;
		3) _drop_db;;
		4) _add_users;;
		5) _drop_users;;
	esac
fi
