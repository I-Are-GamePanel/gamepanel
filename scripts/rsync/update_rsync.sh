#Finalize updates
cd /var/www/files
chown -R www-data:www-data *
cd /var/www/files/rsync
rm sizes.list
rm rsync.list
rm rsync.log
du -sLlk */*| awk '{print $2";"$1}'>sizes.list
ls -d1 */|tr -d />rsync.list

cp /var/www/files/rsync/sizes.list  /var/www/html/panel/modules/gamemanager/sizes.list
cp /var/www/files/rsync/rsync.list  /var/www/html/panel/modules/gamemanager/rsync.list
