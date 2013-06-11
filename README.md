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
