@echo off

SET MOODLE_DOCKER_WWWROOT=.\server\moodle
SET MOODLE_DOCKER_DB=mariadb

echo Restarting Moodle Docker Compose services...
"server\bin\moodle-docker-compose.cmd" restart