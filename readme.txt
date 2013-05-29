#######################
# Installed packages: #
#######################
vim
git
apache2 *
php5 *
libapache2-mod-php5 *
mysql-server *
libapache2-mod-auth-mysql *
php5-mysql *
phpmyadmin *
ecasound *

######################
# Login information: #
######################
MySQL / phpMyAdmin Account: root @ localhost (127.0.0.1)
MySQL / phpMyAdmin Password: csciband

##################
# How to deploy? #
##################
1. Install all the required packages (indicated with *).
2. Execute the script "init.sh".
3. Import "initDB.sql" into the MySQL server using phpMyAdmin.
4. Done! :-)

#######################
# Website Information #
#######################
Directory: /var/www/band OR /home/prjband/www/
URL: http://dhcpv5.cse.cuhk.edu.hk/band/

##############################################
# Open-source libraries used in this project #
##############################################
RTCMultiConnection: https://github.com/muaz-khan/WebRTC-Experiment/tree/master/RTCMultiConnection
RecordRTC: https://webrtc-experiment.appspot.com/RecordRTC/
Ecasound: http://www.eca.cx/ecasound/

#################################################
# Some interesting apps for playing instruments #
#################################################
/* Android */
https://play.google.com/store/apps/details?id=batalsoft.drumsolohd
https://play.google.com/store/apps/details?id=com.codingcaveman.SoloTrial
https://play.google.com/store/apps/details?id=com.rubycell.pianisthd
https://play.google.com/store/apps/details?id=com.gamestar.pianoperfect
https://play.google.com/store/apps/details?id=br.com.rodrigokolb.realdrum
https://play.google.com/store/apps/details?id=br.com.rodrigokolb.thedrumfree
https://play.google.com/store/apps/details?id=br.com.rodrigokolb.realbass
/* iOS */
Drum Kit
Virtuoso

###############
# References: #
###############
http://www.howtoforge.com/ubuntu_lamp_for_newbies
