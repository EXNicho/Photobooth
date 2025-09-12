-- Fix MySQL/MariaDB root authentication to mysql_native_password
-- Use in Laragon MySQL console or any MySQL client as a privileged user.

-- For MySQL 8+
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';
FLUSH PRIVILEGES;

-- For MariaDB (if the above fails), try:
-- UPDATE mysql.user SET plugin='mysql_native_password' WHERE User='root' AND Host='localhost';
-- SET PASSWORD FOR 'root'@'localhost' = PASSWORD('root');
-- FLUSH PRIVILEGES;

