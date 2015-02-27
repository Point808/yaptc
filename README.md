# YAPTC #
### About: ###
Josh North - josh.north@point808.com - 2015-02-09  
This is a basic timekeeping application that is steadily getting more complex.  It may or may not work for you but I'm pretty proud of it.  

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
4. t
