<?php die(); /* DO NOT REMOVE THIS LINE! SERIOUS SECURITY RISK IF REMOVED! */ ?>

::: MySQL Database Information :::
[db_hostname] = "localhost"
[db_username] = "root"
[db_password] = "root"
[db_database] = "innov24_dev"
[db_prefix] = "pommo_"
::: Language Information :::
[lang] = en
Set this to your desired locale. Current languages available are;
	 bg - Bulgarian			it - Italian				
	 da - Danish			nl - Dutch
	 de - German			pl - Polish
	 en - English			pt - Portuguese
	 en-uk - British		pt-br - Brazilian Portuguese
	 es - Spanish			ro - Romanian
	 fr - French			ru - Russian			
::: Optional Configuration :::
====================================================================
Below Options are intended for debugging or overriding 
automatic configuration.
====================================================================
[debug] = off
	Enable (on) or disable(off). Debug mode is useful for providing
 	information to developers
[verbosity] = 1
	Set the logging verbosity level.
	1: Debbuging mode - *EVERYTHING* is outputted. 
	2: Informational mode - *MOST EVERYTHING* is outputted
	3: Quiet mode - *IMPORTANT THINGS* are outputted [default]
[date_format] = 1
	Set the preferred date format for "date" type subscriber fields.
	Available formats are;
	 1: YYYY/MM/DD (e.g. 1969/12/15) [default]
	 2: MM/DD/YYYY
	 3: DD/MM/YYYY
::: Overrides :::
Uncomment (remove the leading "**") to define the following settings.
  NOTE: These settings are auto-detected by default, and best left unchanged.
** [baseURL] = "/mysite/newsletter/"
	Set the Base URL (poMMo's path relative to the webserver) e.g.;
	  (poMMo location)							(baseURL value)
	    	  http://newsletter.mysite.com/				/
 http://www.mysite.com/me/pommo			/me/pommo/
  	NOTE: Include trailing slash
** [workDir] = "/path/to/pommoCache"
	Set the "working" directory. poMMo writes files to this directory. 
  	By default, it is set to the "cache" directory in the poMMo root.
  	For increased security move this directory to a location not reachable
  	via the web (e.g. /home/brice/work vs. /home/brice/public_html/work)
  	Make sure the webserver can write to this directory!
** [hostname] = www.mysite.com
	Set the webhost's server name
** [hostport] = 8080
	Set the webhost's listening port [Usually 80, 443, or 8080]