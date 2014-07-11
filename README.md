# Database Updater

Database version manager written in PHP


## Phases

The update algorithm supports 3 phases for each schema revision, pre-alter,
alter and post-alter. Pre-alter and post-alter scripts are PHP scripts. The
alter phase is an SQL script. Each phase can return data which will be passed to
subsequent phases including from PHP to SQL and vice versa (more on this later).

### __WIP__ PHP Script execution methods

DbUp supports a couple of different ways of structuring/encapsulating PHP
pre/post alter scripts. The default simply requires the PHP files containing the
alter logic. These files can be structured whichever way best suits the logic
being performed. The return values from the pre-alter script is passed into the
subsequent alter SQL script.


## File system structure

Database schema revisions should all be stored in the same directory. Each
revision will consist of a set of 1 to 3 files, one for phase where all phases
are optional. The files need to named according to the following convention:

 -  Pre-alter: pre-alter-000001.php
 -  Alter: alter-000001.sql
 -  Post-alter: post-alter-000001.php


## SQL Enhancements

The .sql scripts of the alter phase support a superset of SQL syntax. The
additional syntax makes it eaiser to write portable scripts, as well as to
perform some common tasks that would normally require a pre/post alter script.


### INSERT ID variables

When performing INSERTs into tables that contain a serial (auto increment) field
it is difficult to determine the generated ID for subsequent use. DbUp supports
special syntax to store these IDs in variables and to use them in subsequent
INSERT statements. To store the insert ID use the following syntax:

    my_insert_id := INSERT INTO my_table (key, value)
        VALUES ('aKey', 'aValue');

To then use the saved insert ID in a subsequent statement, reference the
variable using the following syntax:

    INSERT INTO my_table_ref (my_table_id, refd)
        VALUES (:my_insert_id, 'refVal');

In order for this to work with PostgreSQL the id field for the table needs to be
backed by a sequence named `<table-name>_id_seq`.

### **WIP** Normalization of SERIAL fields

Most databases support a field type that is an auto incrementing integer most
commonly used for primary keys, however they all seem to have a slightly
different syntax. When writing your alter scripts you can write these statements
for whichever database you are comfortable with and dbUp will handle it
correctly for whichever database your script is being run against.

This feature is currently a WIP and only supports a limitted number of use cases
(i.e the ones I've need personally). If you find a various that's not working
create an issue, it should be fairly easy to add support.

Current support includes:

 -  MySQL `integer [NOT NULL] AUTO_INCREMENT` field can be executed against a
    PostgreSQL connection. Field can be specified as not null.
 -  That's it.

### **WIP** Normalization of ALTER TABLE

Some support for normalizing the various `ALTER TABLE` commands

 -  MySQL `ALTER TABLE <table> MODIFY [COLUMN] <column> <new-type>` and PgSQL
    `ALTER TABLE <table> ALTER COLUMN <column> [SET DATA] TYPE <new-type>`
    statements are normalized.
