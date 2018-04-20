# PureUserAdmin v 0.4.0


# TODO

 * Basic tests
 * docker-services.yml for example with pureftp + end-to-end test.

# Changes since 0.3 ...

 * Refactor codebase
 * Try and remove XSS, and SQL Injection issues 
 * Try and support multiple databases (via PDO)
 

# Installation 

(Assuming Debian or derivative)


 * apt-get install pure-ftpd-mysql
 * git clone git@github.com:DavidGoodwin/pureftp-user-admin.git /var/www/somewhere/
 * In /var/www/somewhere ...
   * wget https://getcomposer.org/composer.phar && php composer.phar install
   * echo "CREATE DATABASE pureftp" | mysql --defaults-extra-file=/etc/mysql/debian.cnf 
   * echo "CREATE USER pureftp IDENTIFIED BY PASSWORD 'somepass'" | mysql --defaults-extra-file=/etc/mysql/debian.cnf 
   * mysql --defaults-extra-file=/etc/mysql/debian.cnf pureftp < schema.sql
   * Edit /var/www/somewhere/config.php with your database details 
 * Expose /var/www/somewhere/public via Apache (e.g. Alias /pureftp-admin /var/www/somewhere/public )
 * Configure pure-ftp
   * See docs/pureftp-mysql.conf.example for what you could put in /etc/pure-ftpd/db/mysql.conf
   * cp docs/pureftp-mysql.conf.example /etc/pure-ftpd/db/mysql.conf
   * echo "yes" > /etc/pure-ftpd/conf/DisplayDotFiles
   * echo "no" > /etc/pure-ftpd/conf/PAMAuthentication
   * And if you're having problems: 
     * echo "yes" > /etc/pure-ftpd/conf/VerboseLog
   * /etc/init.d/pure-ftpd-mysql restart
 * test?
   
# Copyright

Historical note etc -

Copyright (c) 2004, Michiel van Baak <mvanbaak (AT) users (DOT) sourceforge (DOT) net>
Licensed under the General Public License (GPL), see COPYING file 
provided with this program.


