@echo off

SET MOODLE_DOCKER_WWWROOT=.\server\moodle
SET MOODLE_DOCKER_DB=mariadb

echo Starting Moodle Docker Compose services...
"server\bin\moodle-docker-compose.cmd" build && "server\bin\moodle-docker-compose.cmd" up -d
