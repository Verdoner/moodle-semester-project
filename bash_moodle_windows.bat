@echo off

SET MOODLE_DOCKER_WWWROOT=.\server\moodle
SET MOODLE_DOCKER_DB=mariadb

echo Opening Moodle Docker Compose bash...
"server\bin\moodle-docker-compose.cmd" exec webserver bash