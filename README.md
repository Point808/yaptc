# YAPTC #
### About: ###
Josh North - josh.north@point808.com  
Basic (i.e. stupidly simple) timekeeping application.  I'm working on more features but who knows if that will get released.  

### License ###
Free for use and modification.  Credit is appreciated if you do anything with it but nothing is required.  

### Credits ###
* Bootstrap - http://getbootstrap.com
* phpass (password hashing, license included) - http://www.openwall.com/phpass/  
* HUGE thank you to PHP Developers Network users "califdon", "Celauran", and "social_experiment" for their guide on login and registration systems at http://forums.devnetwork.net/viewtopic.php?f=28&t=135287&sid=f7140b48a14f50fd7f0340581442447b#p675168  

### Theory: ###
We track time in this application by punching start and end times on each record.  Times are calculated using MySQL math functions in the queries.  This allows us to be more flexible with granularity and time zones, etc etc.  Everything else is mostly in line with standard PHP web apps.  

### Requirements: ###
* PHP 7 working fine 
* MySQL (I used MySQL 5.5.41)  
* Web server (tested on NGINX)  

### Setup Assumptions: ###
* You have the above requirements met.  
* You are somewhat familiar with editing PHP config files (I hope).  
* I assume you have git installed - if not, rather than using the git-pull, download the zip from my git page.  
* MAKE SURE YOUR SERVER CLOCK IS CORRECT!!! This system relies on the server time, not the client machine time.

### Setup Instructions: ###
1. Go to your webserver root as a user with write privileges (i.e. /var/www/html)  
2. Decide what sub-directory you want the app in.  I use "timecard" here.  
3. Download the zip release and unzip in the correct directory.  
4. Change to the new directory and copy the config.inc.php.example file to config.inc.php  
5. Edit the config.inc.php top section variables to suit your environment.  
6. Use your MySQL management interface of choice to import the mysql.sql file to your database server.  It will create a database named "yaptc". You also need to probably make a user with permissions on that database and that database alone to use in the config file so that this app does not have root mysql permissions!!!  Note also - I had trouble importing with some tools.  In the end, I used the mysql command line tool and just copied/pasted the text in.  
7. Open the time card app.  Login with the default user/pass of "admin" and "admin".  
8. Go for it. Create new users, etc etc etc.  

### Screenshots: ###
![Login Page] (https://git.point808.com/attachments/7381ee2d-3bb5-4430-9c37-29527f8bfa4f)  
![Home Punch] (https://git.point808.com/attachments/5b762048-54cf-41bf-91b0-468f333f4bae)  
![User Profile] (https://git.point808.com/attachments/4b13b0f1-d6ff-4445-93ad-6e699fdba075)  
![Manage Users] (https://git.point808.com/attachments/24ab13eb-d32e-4b25-89e1-88edc2e9cbf2)  
![Punch Editor] (https://git.point808.com/attachments/ec4fa0e7-caa4-46d5-ab9c-d08ea55b1eb5)  
![Reports] (https://git.point808.com/attachments/dbe6fee9-f1ad-49bb-8a30-f8e568f54e64)  