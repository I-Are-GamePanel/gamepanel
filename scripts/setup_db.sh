dbPass=$(</dev/urandom tr -dc _A-Z-a-z-0-9 | head -c8)
srvID=${PWD##*/}
dbID=dayz_${srvID}
password=


sed -i "s/dayzhivemind/${dbID}/g" dayz_19.sql

sed -i "s/Host = .*/Host = mysql.iaregamer.com/g" cfg/hiveext.ini
sed -i "s/Database = .*/Database = ${dbID}/g" cfg/hiveext.ini
sed -i "s/Username = .*/Username = ${dbID}/g" cfg/hiveext.ini
sed -i "s/Password = .*/Password = ${dbPass}/g" cfg/hiveext.ini

mysql -uremoteuser -p${password} -hmysql.iaregamer.com -e "CREATE DATABASE IF NOT EXISTS ${dbID}"
mysql -uremoteuser -p${password} -hmysql.iaregamer.com -e "GRANT ALL ON ${dbID}.* TO '${dbID}'@'localhost' IDENTIFIED BY '${dbPass}'"
mysql -uremoteuser -p${password} -hmysql.iaregamer.com -e "GRANT ALL ON ${dbID}.* TO '${dbID}'@'%' IDENTIFIED BY '${dbPass}'"


mysql -uremoteuser -p${password} -hmysql.iaregamer.com -e "FLUSH PRIVILEGES;"
mysql -uremoteuser -p${password} -hmysql.iaregamer.com -D ${dbID} < dayz.sql

mysql -uremoteuser -p${password} -hmysql.iaregamer.com -e "INSERT INTO panel.ogp_mysql_databases(mysql_server_id, home_id, db_user, db_passwd, db_name, enabled) VALUES (1,${srvID},'${dbID}','${dbPass}','${dbID}',1)"
