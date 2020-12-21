 #so we dont forget how to do something
 #howto get an entire folder with wget
 #omitting the index.html files and not
 #recreating the directory structure.
 #the key here is --cut-dirs=2 which goes
 #back 2 folder levels removing the 
 #postinstall/dayzmod
 
 sudo wget -r -np -nH --cut-dirs=2  -R "index.html*" files.iaregamer.com/postinstall/dayzmod/
