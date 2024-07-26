#!/bin/bash

# Database credentials from config.php
DB_HOST="localhost"
DB_USER="nevil"
DB_PASS="7683Nev!//"
DB_NAME="HAGSZ"

# Directory to store the dump files
BACKUP_DIR="/var/www/html/backup"

# Ensure the backup directory exists
mkdir -p $BACKUP_DIR

# Find the latest dump file version
LATEST_VERSION=$(ls $BACKUP_DIR | grep -E 'Dumpfile_v[0-9]+' | sed -E 's/Dumpfile_v([0-9]+)\.sql/\1/' | sort -n | tail -1)

# Determine the new dump file version
if [[ -z $LATEST_VERSION ]]; then
  NEW_VERSION=1
else
  NEW_VERSION=$((LATEST_VERSION + 1))
fi

# Create the new dump file
DUMP_FILE="$BACKUP_DIR/Dumpfile_v${NEW_VERSION}.sql"
mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME > $DUMP_FILE

# Check if the dump was successful
if [[ $? -eq 0 ]]; then
  echo "Database dump created successfully: $DUMP_FILE"
else
  echo "Error creating database dump"
  exit 1
fi


