cd /var/www/files/installers

cd mcserver
./mcserver ai
mv serverfiles /var/www/files/rsync/mcserver
cd ..

cd mcbserver
./mcbserver ai
mv serverfiles /var/www/files/rsync/mcbserver
cd ..

cd samp
./sampserver ai
mv serverfiles/samp03 /var/www/files/rsync/samp
cd ..

