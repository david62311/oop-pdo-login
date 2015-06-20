# Login system using PHP
Specifications:

* PDO MySQL Database connection
* OOP user and session creation
* Salted password hashes
 
# Database creation SQL

## Creating a new database in your MySQL

You can either do this through the phpMyadmin GUI or run this SQL:

	CREATE DATABASE

## Creating the table

This SQL:

- Creates a primary key column named user_id that auto increments
- Creates a username column that can't be empty and must be unique
- Creates a password hash column of ength 60. This is because the BCRYPY algorhythm returns length 60 values

    CREATE TABLE users (
    user_id int(11) NOT NULL auto_increment PRIMARY KEY,
    username varchar(20) NOT NULL UNIQUE KEY,
    password char(60) NOT NULL
    );

# License
Use this as you like. For commercial use contact me first.