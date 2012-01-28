


# Clicky Exporter

* Author:   John Eskilsson (<john.eskilsson@gmail.com>)


**This realease is very BETA and interfaces are likely to change**

## What is Clicky Exporter
It is a PHP command line program that will fetch statistical data from a site on an account in www.getclicky.com.
It supports this data:

* visitor

## Why was Clicky Exporter created?
I created it since there is no full export feature in Clicky. The API only
allows for fetching 5000 records in one go. This script addresses this problem
and paginates the results and downloads the data in XML format.
If you are not happy with having all your statistical data stored in the cloud only and you want to have full 
access to your data even if www.getclicky.com is taken off the internet for any reason.

This program is also useful if you want to have access to the raw website statistical data for analysis in a 3rd party product that support import of XML files.

The script is intended to run on PHP 5.3.x on Windows and PHP needs to have the curl module activated.

##Installation instructions
The script is only tested on Windows

###Install PHP if you don't already have it running

1. Download PHP 5.3 from here <http://windows.php.net/download/> take one of the zip files (check out "Which version do I choose?" on that page to chose the right one and install the right C++ runtime library)
1. Unpack the zip file to the directory where you want to php.exe to be (I usually go for c:\php\)
1. Open a command prompt and run PHP like this c:\php\php.exe -i. This should print PHP info to the command line. If this works PHP is installed properly
1. Rename the file c:\php\php.ini-production to c:\php\php.ini. This makes PHP automatically load this file when the command line script is run.
1. Now to make sure the CURL module is activated open c:\php\php.ini in an editor. Search for php_curl.dll. Overwrite that row with "extension=ext/php_curl.dll".
###Install and run Clicky Exporter
1. Download  the zip file or use GIT to get the files somewhere locally on your computer
1. Open a command prompt and navigate to the folder where you unzipped the files.
1. Log in to <http://www.getclicky.com> go to preferences for the site you want to download data for. Copy the siteId and the siteKey. They need to be added in the next step.
1. To run a test download of data run this from the command line: __**c:\php\php.exe ClickyExporter\clicky_exporter.php -t visitor -s "2012-01-13" -e "2012-01-13" -i [add your siteId here] -k [add you sitekey here]**__. This should pull some data down.
1. Look at the output of the script to find where the files are put.
1. Now you can set the parameters for the script by changing the config file called clicky_exporter_config.php

**Note:** If you set command line parameters they will override the parameters in the config file.

##How do I configure Clicky Exporter?


###These are the valid parameters to use:
* --runtimeFolder -r			This is where the output files go. Example: Absolute path "C:/data" or Relative path "data"
* --clickySiteId -i				This is the site id you can find in the Clicky settings
* --clickySiteKey -k			This is the site key you can find in the Clicky settings
* --type -t								This is the type of exporter to use. Example visitor (this is the only exporter that is available for now.)
* --startDateFormat -s		The date for when to start gathering data. Example: 2012-01-01
* --endDateFormat -e			The date for when to stop gathering data. Example: 2012-01-01
* --batchSize -b					The number of records to get in one batch fetch.
* --folders	-f						Set this to true if you want a folder with a unique name to be created in the runtime folder every time you run the program. If set to false the downloaded files goes striaght in the runtime folder.
* --timeout -o						This is the Curl time-out in seconds. If you are getting problems with Clicky Exporter trying to download the same file over and over again failing every time you should set this value higher and probably set the batchSize lower.

-------------------------------------------

##Known Issues:

* Only the visitor downloader is implemented so far
* Only XML output is supported so far
* The output files will not be merged in to one file so far


##How do I extend Clicky exporter
The script supports pluggable exporters:

To create you own exporter do this:

1. Copy ClickyExporter/Exporter/ClickyVisitorExporter.php and rename it like this ClickyExporter/Exporter/Clicky[RecordType]Exporter.php.
1. Now make sure to give the new exporter class the exact same name as the file you just created but without the .php at the end. 
Doing so will make the file be automatically loaded if you add this command line parameter "-t RecordType".
1. Now you have to implement the IClickyExporter interface. Same again easiest way is to copy the implementation of ClickyExporter/Exporter/ClickyVisitorExporter and adapt it for the new exporter.
