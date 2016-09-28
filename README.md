### Table of contents

[TOC]

### Status API
===================


This is a ApiRest create in SymFony Framework version 3.1.4, you can publish a status, find status, and delete status. When you create an status a code is generate in case of you want delete it.

----------

### Instalation
-------------

First you must have composer already install, if you haven't this is the official page of composer for install.
https://getcomposer.org/download/

Now you have to clone the repository in your project folder.

cd projects/

git clone git@github.com:leonardorotundo/intrawayApi.git

cd intrawayApi/

composer install

Now we going to settings of framework symfony.

In the file <i class="icon-file"></i> app/config/parameters.yml you have to set the database_user, database_password and your account gmail for send email.
Example:
mailer_transport: gmail
mailer_host: smtp.gmail.com
mailer_user: your_gmail (THIS IS JUST FOR TEST)
mailer_password: your_password

Now you have to create database. Open an terminal and put this in your intrawayApi folder project.

php bin/console doctrine:database:create

Now we going to see the requirements of symfony:

In the terminal put php bin/symfony_requirements and follow the instructions.

Now in the for see documentation of the Api you can see this http://localhost/intrawayApi/web/app_dev.php/api/doc



Now you can use the StatusApi, enjoy it!

One important Symfony requirement is that the var directory must be writable both by the web server and the command line user.

On Linux and macOS systems, if your web server user is different from your command line user, you need to configure permissions properly to avoid issues. There are several ways to achieve that:

1. Use the same User for the CLI and the Web Server¶

Edit your web server configuration (commonly httpd.conf or apache2.conf for Apache) and set its user to be the same as your CLI user (e.g. for Apache, update the User and Group directives).

If this solution is used in a production server, be sure this user only has limited privileges (no access to private data or servers, execution of unsafe binaries, etc.) as a compromised server would give to the hacker those privileges.

2. Using ACL on a System that Supports chmod +a (macOS)¶

On macOS systems, the chmod command supports the +a flag to define an ACL. Use the following script to determine your web server user and grant the needed permissions:

$ rm -rf var/cache/*
$ rm -rf var/logs/*

$ HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
$ sudo chmod -R +a "$HTTPDUSER allow delete,write,append,file_inherit,directory_inherit" var
$ sudo chmod -R +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" var
3. Using ACL on a System that Supports setfacl (Linux/BSD)¶

Most Linux and BSD distributions don't support chmod +a, but do support another utility called setfacl. You may need to install setfacl and enable ACL support on your disk partition before using it. Then, use the following script to determine your web server user and grant the needed permissions:

$ HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
# if this doesn't work, try adding `-n` option
$ sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var
$ sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var
setfacl isn't available on NFS mount points. However, storing cache and logs over NFS is strongly discouraged for performance reasons.

4. Without Using ACL¶

If none of the previous methods work for you, change the umask so that the cache and log directories are group-writable or world-writable (depending if the web server user and the command line user are in the same group or not). To achieve this, put the following line at the beginning of the bin/console, web/app.php and web/app_dev.php files:


umask(0002); // This will let the permissions be 0775

// or

umask(0000); // This will let the permissions be 0777
Changing the umask is not thread-safe, so the ACL methods are recommended when they are available.


