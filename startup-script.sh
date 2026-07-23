#!/bin/bash

# Define database credentials
DB_NAME='silver_fieldkonnect'
DB_USER='root'
DB_PASS="\$!LvEr212!O2!\$594!QJ!hd20!\$23len7!2ta61!2"  # Use double quotes to handle special characters
DB_HOST="silver-fieldkonnect-live.cpeyyg6qsws4.us-east-1.rds.amazonaws.com"

# Define backup storage location
BACKUP_DIR="/home/ubuntu/dataBase-backup"
BACKUP_NAME="${DB_NAME}_backup_$(date +%Y%m%d_%H%M%S).sql"

# Log file for the script
LOG_FILE="/home/ubuntu/dataBase-backup/backup_log.txt"

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"
if [ $? -ne 0 ]; then
    echo "$(date '+%Y-%m-%d %H:%M:%S') Error: Failed to create backup directory $BACKUP_DIR" >> $LOG_FILE
    exit 1
fi

# Dump MySQL database and create backup file, redirect stdout and stderr to log
mysqldump -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" "$DB_NAME" > "$BACKUP_DIR/$BACKUP_NAME" 2>> "$LOG_FILE"
if [ $? -ne 0 ]; then
    echo "$(date '+%Y-%m-%d %H:%M:%S') Error: mysqldump failed for database $DB_NAME" >> $LOG_FILE
    exit 1
fi

# Compress the backup file, log any errors
gzip "$BACKUP_DIR/$BACKUP_NAME" 2>> "$LOG_FILE"
if [ $? -ne 0 ]; then
    echo "$(date '+%Y-%m-%d %H:%M:%S') Error: gzip compression failed for $BACKUP_NAME" >> $LOG_FILE
    exit 1
fi

# Delete older backups, keeping only the 4 most recent ones, log actions
cd "$BACKUP_DIR" || exit
ls -t "${DB_NAME}_backup_"*.gz | tail -n +3 | xargs rm -f 2>> "$LOG_FILE"

# Log completion message
echo "$(date '+%Y-%m-%d %H:%M:%S') Backup complete: $BACKUP_DIR/$BACKUP_NAME.gz" >> "$LOG_FILE"
