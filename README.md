# Rybel LLC Data Sync Utility
An internal utility for syncing data between a production database and a development database

## The Problem
Often, development databases can be less than helpful when testing because they don't share the same data as the production database. Either the data is out of date or there isn't much data period.

## The Solution
This utility will present the user a list of tables from the production and development databases. Assuming that the structures are identical between the tables, the utility will sync the data from the production database into the development database.

## Limitations
For this utility to work, the table must be present and have the same structure in both databases.
