# Database Updater
Database version manager written in PHP

## Phases
The update algorithm supports 3 phases for each schema revision, pre-alter,
alter and post-alter.

Pre-alter and post-alter scripts are expected to be PHP. The alter phase is
expected to be an SQL script.

## Storage
Database schema revisions should all be stored in the same directory. Each
revision will consist of a set of 1 to 3 files, one for phase where all phases
are optional. The files need to named according to the following convention:

 -  Pre-alter: pre-alter-000001.php
 -  Alter: alter-000001.sql
 -  Post-alter: post-alter-000001.php

## SQL Enhancements
The .sql scripts of the alter phase are plain old SQL scripts with a few
enhancements to make it easier to write portable scripts and to make use of
insert ids so that data insertion doesn't need to happen in a post-alter script.

### Normalization of SERIAL fields
Most databases support a field type that is an auto incrementing integer most
commonly used for primary keys, however they all seem to have a slightly
different syntax. When writing your alter scripts you can write these statements
for whichever database you are comfortable with and dbUp will handle it
correctly for whichever database your script is being run against.

### INSERT ID variables
When performing INSERTs into tables that include a serial field it is not
possible to determine this ID for subsequent INSERTs without using sub queries.
dbUp supports special syntax to store these IDs in variables and to use them in
subsequent INSERT statements.
