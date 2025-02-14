Done: user login, user sign up, database connectivity, appointment section, Admin Section, Doctor's section.
# DentCare

# Steps to run the project:
1. Install the XAMP (it will have all the required stuff like php and sql to run the project).
2. keep your XAMPP in C:/ folder.
3. Add your this project it in htdocs [which will be under the xamp folder].
4. Open XAMPP run both sql and apache server.
5. Open localhost.

Note: If sql does not run then open services.msc [windows + R then type services.msc] 
then look for MYsql, check it's status, if it's running then stop it and try running MYSQL in XAMP, it will start.
# There might be other issue causing it , so it may not work as well.

#-----------------------------------------------------------------------------------------------------------------------------

# Steps to set up the database locally:

First, open XAMPP Control Panel and make sure both Apache and MySQL services are running
Open phpMyAdmin in your browser:

Go to http://localhost/phpmyadmin or
Click the "Admin" button next to MySQL in XAMPP Control Panel

Create a new database:

Click "New" in the left sidebar
Enter "dentcare" as the database name (it must match exactly what's in your connection.php)
Select "utf8mb4_general_ci" as the collation
Click "Create"


Import the database structure:

Select the "dentcare" database from the left sidebar
Click the "Import" tab at the top
Click "Choose File" and select your dentcare.sql file
Scroll down and click "Import"



The SQL dump you provided contains all the necessary tables (admins, appointments, doctors, users) and some sample data. After importing, you should have:

An admin account (admin@gmail.com/admin123)
Some doctor accounts
Some user accounts
A couple of sample appointments

Your connection.php file is already correctly configured for a default XAMPP installation with:

host: localhost
username: root
password: (blank)
database: dentcare

Once you've completed these steps, your database will be set up and your PHP project should be able to connect to it successfully.
