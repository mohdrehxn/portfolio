# mysql.Dockerfile — MySQL 8 for Render
FROM mysql:8.0

# Copy init SQL to auto-create tables on first run
COPY setup.sql /docker-entrypoint-initdb.d/setup.sql

EXPOSE 3306
