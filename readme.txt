fsACARS Configuration Guide (turn on wordwrap)


INTRODUCTION / QUICKSTART
=========================

Configuring fsACARS for your virtual airline is simple:

1. Upload all the server files to one directory on your server.

2. Edit fsACARS's serverconfig.txt to update the URLs.

3. Distribute fsACARS (with your new serverconfig.txt) to your users.


SERVER CONFIGURATION
====================

1. Modify the following server files, as necessary. Note that all of the server files will work as they are, however you will likely want to customize things for your own server:

  pirep.php - generic pilot report receiver script
  posrep.php - generic position report receiver script
  userquery.php - generic user validation script
  airports.csv - a list of airports with associated coordinates (used by pirep.php)
  posrep.csv - pilot position reports (created and updated by posrep.php)
  serverlogbook.php - pilot reports are logged here (created and updated by pirep.php)

2. Upload all the server files to your server.
     
3. Edit the serverconfig.txt file located in the folder with fsACARS. Note that only the first five lines of this file are parsed by fsACARS (see below).  Each line is described below.  For an example, view the serverconfig.txt file that came with fsACARS.

  Line 1: URL of userquery script on your server.
  Line 2: URL of position reporting script on your server.
  Line 3: URL of the pilot report script on your server.
  Line 4: URL of the location where pilot reports are stored on your server.
  Line 5: your server authentication token (anything you desire)

4. Create a folder on your server to match the URLs in serverconfig.txt.


5. Distribute fsACARS (with the modified serverconfig.txt) to your users.  Note that they will not need any of the server files in (1).  



