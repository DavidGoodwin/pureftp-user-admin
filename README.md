# PureUserAdmin v 0.4.0


# TODO

 * Basic tests
 * docker-services.yml for example with pureftp + end-to-end test.

# Changes since 0.3 ...

 * Refactor codebase
 * Try and remove XSS, and SQL Injection issues 
 * Try and support multiple databases (via PDO)
 


# Changes since 0.2.1, by DG

 * Move configuration into a seperate config file
 * Attempt to remove some XSS issues; the code base is still vulnerable to SQL injection and requires magic_quotes to be enabled.


# Old stuff.


Copyright (c) 2004, Michiel van Baak <mvanbaak (AT) users (DOT) sourceforge (DOT) net>
Licensed under the General Public License (GPL), see COPYING file 
provided with this program.


Contents of this document
-------------------------------------------------------------------------------

   1. What is PureUserAdmin?
   2. (New) Features.
   3. Requirements.
   4. Installation.
   5. Project goals.
   6. For administrators.
   7. Security.
   8. Contributing.
   9. Copyright stuff.
  10. Contact information.
  11. Thanks


1. What is PureUserAdmin?
-------------------------------------------------------------------------------

PureUserAdmin is a webbased tool written in PHP4 to manage a 
MySQL/PostgreSQL/PureDB database that holds ftp only accounts.

2. (New) Features
-------------------------------------------------------------------------------

List of current features (new features marked with *) :
 
Listing ftp accounts
Adding ftp accounts
Modifying ftp accounts
Deleting ftp accounts
Check access rights on homedir
Notify users with their ftp info
PHP5 support
Search and pages in userlist*

3. Requirements.
-------------------------------------------------------------------------------

PureUserAdmin requires :
  
    * A http server
    * PHP5, with mysql.so/psql.so module (or build in support).
    * MySQL/PostgreSQL daemon.
		* PureFTPd with mysql/postgresql auth config
		* A webbrowser
  
Supported browsers are:
  
    * Mozilla 
    * Netscape 6.x+
    * IE 5.x (and higher)
    * Opera 6.0+
    * Lynx
  
4. Installation.
-------------------------------------------------------------------------------

Just unpack it somewhere in your www root.
Edit index.php to reflect your system.

5. Project goals.
-------------------------------------------------------------------------------

Main goal is:
	* to make a webbased application that every ftp-server 
		admin can use to manage the users in the MySQL database.
	* to make a webbased application that every ftp-user can
		use to change their ftp password

When the main goal is reached it would be nice to make a directory
creation/checking function (checking realized in 0.0.2) so a new 
user will have it's homedir setup or the program will display an 
error when the homedir is already there and the new user 
doesn't have permissions there.
  
6. For administrators.
-------------------------------------------------------------------------------

  This section is intended for system administrators who would like to 
  offer PureUserAdmin to the users on their systems. 

7. Security.
-------------------------------------------------------------------------------

* Run it over SSL ONLY!
* Do not run your webserver as user nobody!
* Don't forget to restrict access with .htaccess/.htpasswd
  
Also read the beautiful NO WARRANTY disclaimer in the GPL. ;)

  
8. Contributing.
-------------------------------------------------------------------------------

If you wish to contribute to PureUserAdmin, 
you can do so with the following things:
  
    * patches.
    * suggestions.
    * feature requests.
  
  When contributing code, please keep the following in mind:
  
    * Write your code according to the rest of programs code style.
    * Send in patches created with the 'diff' command.
    * Patch purpose must follow the project goals. (or fix bugs, etc)
    * Patches sent to me are public domain or under a GPL (compatible) 
      license, or they can't be added to the code. 
  

9. Copyright stuff.
-------------------------------------------------------------------------------

  PureUserAdmin is Copyright by Michiel van Baak,
  licensed under the General Public License (GPL)
  
  Copyright (C), 2004 by Michiel van Baak <mvanbaak (AT) users (DOT) sourceforge (DOT) net>
  
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.
      
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.
      
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
              
  For more information, see the COPYING file supplied with this
  program.


10. Contact information.
-------------------------------------------------------------------------------

Author:
  
    Michiel van Baak
    Email: mvanbaak (AT) users (DOT) sourceforge (DOT) net
    HomePage: http://lunteren.vanbaak.info

  Please report any bugs, requests and general comments by using the project
  page at sourcefoge
  
  You may also mail them to me. Before reporting bugs or sending contributions
  please read the Contributing section in this README.

	Project HomePage: http://sourceforge.net/projects/PureUserAdmin

  

11. Thanks
-------------------------------------------------------------------------------

  Thanks go out to:
  
  Testing:
  --------
	zeepee` (http://leonieke.net)
  
  Various suggestions and patches:
  --------------------------------
	Nancy (my wonderful wife) - she came up with the name
	The drupal xtemplate crew - interface ideas
	Ferry Boender - for pointing me to doxygen. since this is not really php5 friendly I used phpdoc
  
  And of course all the others who helped me.

Sat Oct 16 12:32:25 CEST 2004