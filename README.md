1.      DESCRIPTION
        Content management system application for creating and manipulating blog posts.

2.      RUN ON LOCALHOST

        2.1 WINDOWS

        Download and install XAMPP
        Open XAMPP Control Panel
        Next to the Apache module click Start
        Next to the MySQL module click Start
        Navigate to XAMPP's installation directory (C:\xampp)
        Open the htdocs directory
        Create directory cms
        Download rest of the files

        CREATING THE DATABASE AND SETTING-UP TABLES
        Navigate to: http://localhost/phpmyadmin/
        Click the Databases tab at the top
        Under Create database, enter cms in the text box
        Click Create
        Click the Import tab at the top
        Choose File cms.sql from downloaded files

        2.2 UBUNTU

        Prerequisites:
        PHP
        Apache Web server
        MySQL

        Steps:
        Clone the project into /var/www/html directory
        Create database cms
        Run cms.sql script from terminal
        mysql -u root -p cms < cms.sql

3.      RUN PROJECT
        Navigate to http://localhost/cms/index.php in your browser

