# autodesk_license_webapp
Autodesk License Management Internal Website

This code may be a bit outdated and the conditions I used to create this were not the best. If you want to adapt something like this, please make required corrections.

# Environment Used
- Apache 2.4.18
- Autodesk: lmutil v11.12.1.4 build 154914 x64_lsb
- PHP: 7.0.15-0ubuntu0.16.04.2 (cli) ( NTS )
- Ubuntu Server 16.04
- Chrome v73 (64-bit)

# Before Running
## File Configuration
- Webpage code is index.php
- CSS is main.css
- Folder hierarchy will have a stylesheet folder with main.css being inside.
 - If you installed the Autodesk **lmutil** software in another location than the one inside of `getLicenseList()`, you will need to change `exec()` to use the proper location! 

## Network License Configuration
- Inside of `getLicenseList()`, `exec()` will need to have the correct path to the **lmutil**. Replace **LMUTIL_PATH & LICENSE_FILE_PATH** field with the correct location. Ex: **/var/www/autodesk**

## Change Environment Specific Variables
- **DOMAIN_NAME_HERE**
 - Add in your domain name here in the format of “DC=company,DC=me”
- **YOUR_SERVER_HERE**
 - Use a server IP instead of a name to future proof changing of servers
- **LDAP_USER_HERE**
 - The account needs to have read (only) access to your AD structure. Further security can be implemented as needed
- **LDAP_USER_PASSWORD_HERE**
 - **Don't do this!** Plaintext password for the above account.
- **LDAP_TREE_PATH_HERE**
 - Example “OU=users, OU=Office1, DC=company, DC=me”. Limit this to the OU’s where the users actually reside to lessen the time it takes to pull username data. Also there is a hard limit by default of 1,000 users pulled in the versions of the software I have listed above.

## Change the Webpage Footer
- The bottom line includes a link to your IT Departments help desk page. Modify the line `<a href=”https://yourITteam.me”>IT Department</a>”` with your desired information

# Features
- Get an AD user list using LDAP
- Get license usage information from the Autodesk license manager
- Compare data and generate an internal webpage so that everyone who uses the software can see if a license is available

#Limitations
- Currently only coded to work with versions 2017 - 2019
 - You can add new versions by adding another line with a PHP function call and using the 5-digit code for the new product

# Improvements
- **User a better technique to store the password for LDAP!**
- Better lookup technique, either a sorted AD list or some other way to get faster results. (Binary search a sorted updated list, etc)
- Make the CSS and format nicer for the visual website
- Change -1 returns to named constants
- Many others but won't list them at this time

# Resources
- [LDAP Binding PHP](https://www.php.net/manual/en/function.ldap-bind.php)
- [Coding PHP W3 Schools](https://www.w3schools.com/php/)
- [Coding PHP PHP.com](https://www.php.net/manual/en/)
