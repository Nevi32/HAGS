#!/bin/bash

# Variables
DB_NAME="HAGS3"
DB_USER="nevill"
DB_PASS="7683Nev!//"
BACKUP_DIR="/var/www/html/HAGS/backup"
DUMP_LOCAL_DIR="/home/n32/DUMP_local"
DATE=$(date +%F)
BACKUP_FILE="$BACKUP_DIR/$DB_NAME-$DATE.sql"
LOCAL_BACKUP_FILE="$DUMP_LOCAL_DIR/$DB_NAME-$DATE.sql"

# Create backup
echo "Creating backup for database: $DB_NAME"
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_FILE

if [ $? -eq 0 ]; then
    echo "Backup created successfully at $BACKUP_FILE"
else
    echo "Error creating backup"
    exit 1
fi

# Move backup to DUMP_local directory
echo "Moving backup to $DUMP_LOCAL_DIR"
mv  $BACKUP_FILE $LOCAL_BACKUP_FILE

if [ $? -eq 0 ]; then
    echo "Backup moved successfully to $LOCAL_BACKUP_FILE"
else
    echo "Error moving backup to $DUMP_LOCAL_DIR"
    exit 1
fi


