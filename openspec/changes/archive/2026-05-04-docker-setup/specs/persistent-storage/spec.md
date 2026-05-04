## ADDED Requirements

### Requirement: MySQL data persistence with named volume
The docker-compose.yml SHALL define a named volume `mysql_data` that persists MySQL database files across container restarts and removals.

#### Scenario: MySQL data survives container restart
- **WHEN** user runs `docker-compose down` and `docker-compose up`
- **THEN** all database data is preserved and available

#### Scenario: MySQL data survives container removal
- **WHEN** user runs `docker-compose down -v` without removing named volumes
- **THEN** database data persists in the `mysql_data` volume

#### Scenario: MySQL volume is mounted to correct path
- **WHEN** MySQL container starts
- **THEN** the `mysql_data` volume is mounted to `/var/lib/mysql`

### Requirement: Uploads directory persistence with named volume
The docker-compose.yml SHALL define a named volume `uploads_data` that persists user-uploaded files in the `public/uploads` directory.

#### Scenario: Uploaded files survive container restart
- **WHEN** user runs `docker-compose down` and `docker-compose up`
- **THEN** all uploaded files in `public/uploads` are preserved

#### Scenario: Uploads volume handles large files
- **WHEN** the uploads directory contains 1.4GB of files
- **THEN** the volume persists all files without data loss

#### Scenario: Uploads volume is mounted to correct path
- **WHEN** PHP container starts
- **THEN** the `uploads_data` volume is mounted to `/var/www/html/public/uploads`

### Requirement: Volume ownership and permissions
The uploads volume SHALL have correct ownership (www-data:www-data) and write permissions for the PHP application.

#### Scenario: PHP can write to uploads directory
- **WHEN** application attempts to save an uploaded file
- **THEN** the file is successfully written to `/var/www/html/public/uploads`

#### Scenario: Uploads directory has correct ownership
- **WHEN** container starts
- **THEN** the uploads directory is owned by www-data user (UID 1000)

### Requirement: Storage directory persistence
The docker-compose.yml SHALL mount the Laravel storage directory as a volume to persist logs, cache, and session files.

#### Scenario: Laravel logs persist across restarts
- **WHEN** user runs `docker-compose down` and `docker-compose up`
- **THEN** Laravel log files in `storage/logs` are preserved

#### Scenario: Storage directory has write permissions
- **WHEN** Laravel writes to storage directory
- **THEN** files are successfully created in `storage/framework` and `storage/logs`

### Requirement: Volume backup capability
The named volumes SHALL be accessible for backup operations using standard Docker volume commands.

#### Scenario: MySQL volume can be backed up
- **WHEN** administrator runs `docker run --rm -v mysql_data:/data -v $(pwd):/backup alpine tar czf /backup/mysql_backup.tar.gz /data`
- **THEN** a complete backup of MySQL data is created

#### Scenario: Uploads volume can be backed up
- **WHEN** administrator runs `docker run --rm -v uploads_data:/data -v $(pwd):/backup alpine tar czf /backup/uploads_backup.tar.gz /data`
- **THEN** a complete backup of uploaded files is created
