# Login system using PHP
Specifications:

* PDO MySQL Database connection
* OOP user and session creation
* Salted password hashes
 
# Database creation SQL

    CREATE TABLE users (
    user_id int(11) NOT NULL auto_increment PRIMARY KEY,
    username varchar(20) NOT NULL UNIQUE KEY,
    password char(60) NOT NULL
    );

# License
Use this as you like. For commercial use contact me first.