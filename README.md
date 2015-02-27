# YAPTC #
### About: ###
Josh North - josh.north@point808.com - 2015-02-09  
This is a basic timekeeping application that is steadily getting more complex.  It may or may not work for you but I'm pretty proud of it.  

### Demo: ###
The demo username/password is admin/admin.  It is fully functional with the exception of changing the admin password.  
http://www.point808.com/timecard  

### License ###
Free for use and modification.  Credit is appreciated if you do anything with it but nothing is required.  

### Credits ###
* Pure (CSS styling, license included) - http://purecss.io/  
* phpass (password hashing, license included) - http://www.openwall.com/phpass/  
* HUGE thank you to PHP Developers Network users "califdon", "Celauran", and "social_experiment" for their guide on login and registration systems at http://forums.devnetwork.net/viewtopic.php?f=28&t=135287&sid=f7140b48a14f50fd7f0340581442447b#p675168  

### Status: ###
This script is (as of today, 2015-02-26) in *fully-working beta status*.  

### Theory: ###
We track time in this application by punching start and end times on each record.  Times are calculated using MySQL math functions in the queries.  This allows us to be more flexible with granularity and time zones, etc etc.  Everything else is mostly in line with standard PHP web apps.  

### Requirements: ###
* PHP (I used PHP 5.5.9)  
* MySQL (I used MySQL 5.5.41)  
* Web server (I used NGINX 1.4.6)  

### Setup Assumptions: ###
* You have the above requirements met.  
* You are somewhat familiar with editing PHP config files (I hope).  
* I assume you have git installed - if not, rather than using the git-pull, download the zip from my git page.  

### Setup Instructions: ###
1. Go to your webserver root as a user with write privileges (i.e. /usr/share/nginx/html)  
2. Decide what sub-directory you want the app in.  I use "timecard" here.  
3. Run the following `git clone https://github.com/joshnorth/yaptc.git timecard`  
4. Change to the new directory and copy the config.inc.php.example file to config.inc.php  
5. Edit the config.inc.php top section variables to suit your environment.  
6. Use your MySQL management interface of choice to import the mysql.sql file to your database server.  It will create a database named "yaptc". You also need to probably make a user with permissions on that database and that database alone to use in the config file so that this app does not have root mysql permissions!!!  Note also - I had trouble importing with some tools.  In the end, I used the mysql command line tool and just copied/pasted the text in.  
7. Open the time card app.  Login with the default user/pass of "admin" and "admin".  
8. Go for it. Create new users, etc etc etc.  
