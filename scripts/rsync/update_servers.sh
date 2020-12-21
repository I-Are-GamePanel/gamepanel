#Minecraft -----------------------------------------------

DEFAULTREVISION=release
# Pull out latest release version

REVISION=${1-$DEFAULTREVISION}
REGEX="(?<=$REVISION\": \").*?[^\\\\](?=\")"
VERSION=`curl -silent "https://launchermeta.mojang.com/mc/game/version_manifest.json" | grep -Po "$$
if [ "$VERSION" = "" ]; then
        echo "Invalid Version"; exit
fi
URL="https://s3.amazonaws.com/Minecraft.Download/versions/$VERSION/minecraft_server.$VERSION.jar"
echo "Version: " $VERSION
echo "URL: " $URL
# Download the latest version
curl -O $URL
# copy to the server jar
if [ -f "server.jar" ]; then
        rm server.jar
fi
cp "minecraft_server.$VERSION.jar" server.jar
cp "minecraft_server.$VERSION.jar" /var/www/files/addons/minecraft/


echo "MINECRAFT"
echo "........................."
cd /var/www/files/rsync/minecraft_linux64/linux
DEFAULTREVISION=snapshot
# Pull out latest snapshot version

REVISION=${1-$DEFAULTREVISION}
REGEX="(?<=$REVISION\": \").*?[^\\\\](?=\")"
VERSION=`curl -silent "https://launchermeta.mojang.com/mc/game/version_manifest.json" | grep -Po "$REGEX"`
if [ "$VERSION" = "" ]; then
        echo "Invalid Version"; exit
fi
URL="https://s3.amazonaws.com/Minecraft.Download/versions/$VERSION/minecraft_server.$VERSION.jar"
echo "Version: " $VERSION
echo "URL: " $URL
# Download the latest version
curl -O $URL
# copy to the server jar
if [ -f "server.jar" ]; then
        rm server.jar
fi
cp "minecraft_server.$VERSION.jar" server.jar
cp "minecraft_server.$VERSION.jar" /var/www/files/addons/minecraft/


#Urban Terror --------------------------------
echo "Urban Terror"
echo "........................."

cd /var/www/files/rsync/urt_linux/linux
./UrTUpdater_Ded.sh -q

#Counterstrike 1.6 -----------------------------
#has to run multiple times
cd /var/www/files/rsync/cstrike_linux32
./cstrike_install.sh

#Terraria ------------------------------------
#how to get latest version?
cd /var/www/files/rsync/terraria_linux
rm *.zip
wget http://terraria.org/server/terraria-server-1353.zip
unzip terraria-server-1353.zip
rm *.zip
mv 1353/Linux linux
rm -r 1353
cp serverconfig.txt linux/

#FiveM GTA ----------------------------------------------
cd /var/www/files/rsync/fivem_linux32/linux
BASE="https://runtime.fivem.net/artifacts/fivem/build_proot_linux/master/"
wget $BASE -Otmp
# Assuming the version schema isn't changed or the url above isn't moved, this will always work.
LATEST_VERSION=$(grep -Po '(?<=a href="./)[^"]*' tmp | head -1)
wget "${BASE}${LATEST_VERSION}"
#database file
"GET THE DB SCRIPT
wget https://github.com/citizenfx/cfx-server-data/archive/master.zip -Ocfx-server-data.zip
tar -xvf fx.tar.xz -C $PWD
unzip cfx-server-data.zip -d $PWD/server-data
mv $PWD/server-data/cfx-server-data-master/resources $PWD/resources
rm -r alpine resources run.sh


#Finalize updates
cd /var/www/files
chown -R www-data:www-data *
cd /var/www/files/rsync
rm sizes.list
rm rsync.list
rm rsync.log
du -sLlk */*| awk '{print $2";"$1}'>sizes.list
ls -d1 */|tr -d />rsync.list


