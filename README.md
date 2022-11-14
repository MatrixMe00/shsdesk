# shsdesk
 This project is an online admission system for senior high schools in Ghana

# What to know
 * It is required that this folder be placed in an apache server. If you are using Xampp or WAMP, then this folder
   will have to be in the htdocs folder for it to run
 * The name of the databases to this project is 'shsdesk' and 'shsdesk2'. It would be created automatically when imported into the
   MySQL server, with or without creating the two databases beforehand

# The Database
 * This project comes with two database which can be used. The databases can be tempered with according to your inputs
 * The name of the database file for demonstration is demo_database.sql.
 * The name for the production database file is default_database.sql.
 * There is another database file called clean_database.sql, which can be used as a clean file for your own default inputs
 * All databases can be found in the databases directory in the root folder

# Admin - Test Phase using demo_database.sql
 * Two schools have been added to the system for testing purposes. You can access his account using the admin menu

 **Logins for accounts which have satisfied requirements**
 * Username: jonDoe         Password: 1234    ->    Admin / IT Personnel
 * Username: Kwao           Password: 1234    ->    School Head

 **Logins for accounts which have not satisfied requirements**
 * Username: Mario          Password: 1234    ->    Admin / IT Personnel
 * Username: Donald         Password: 1234    ->    School Head

 **Logins for superadmins**
 * Username: Developer      Password: admin
 * Username: Superadmin     Password: admin

**Admin - Production Phase using default_database.sql**
 * This database comes with a default user which is the developer
 * Any other addition would be done by the developer and via the registration portal
 * Below are the details for logging in to the admin panel
 * 
 * Username: New User       Password: Password@1      Fullname: SHSDesk Developer     Email: developer@shsdesk.com

# Registration
 * A new school subscribed to the services of our system would have to fill the registration form to be noticed on our system
 * Directing your url to /register would open up the registration form for you [http://localhost/shsdesk/register]

**Student upload module**
 * A test spreadsheet file labeled demo_students.xlsx can be used to upload its contents into the system
 * You can manually create your own list by downloading the default file from the CSSPS List tab in the admin panel

**Demo Schools**
 * The system uses two schools for demonstration purposes namely: New School One and New School Two.
 * New School One is a fully activated school, that is to say it has satisfied the requirements for display on the homepage
 * New School Two on the other hand has been prevented from showing because no house has been uploaded for it
 * For this exercise, you can only get admission forms for students in New School One, but not for students in New School Two
 * New School Two will show when at least one house has been uploaded, and you can do this in the admins portal

**Admission**
 * A student can only undergo the admission procedures when a desired school has uploaded at least one student into the system.
 * Payment and sms would require internet since its use is not offline.
 * You can make use of the following transaction references if you are in offline mode
 
 * ------------------------------------------ >
 * Transaction Reference    School Name       |
   --------------------- -> ----------------- |
 * T12345678901234          School Number Two |
 * T125208218225648         School Number Two |
 * T148004111419515         School Number One |
 * T167712297328658         School Number One |
 * ------------------------------------------ >
 * 
 * You can also readily use these students to test the application procedure
 * 
 * ------------------------------------------ >
 * Index Number             School Name       |
 * ------------             ----------------- |
 * 010362005821             School Number One |
 * 010511701021             School Number Two |
 * 010512002021             School Number Two |
 * 010404501821             School Number One |
 * 010511100421             School Number One |
 * 310202127021             School Number Two |
 * ------------------------------------------ >
