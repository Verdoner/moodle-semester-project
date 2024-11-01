#!/bin/bash

# chmod +x bash_moodle_unix.sh
# chmod +x server/bin/moodle-docker-compose

export MOODLE_DOCKER_WWWROOT=./server/moodle
export MOODLE_DOCKER_DB=mariadb

echo "Opening Moodle Docker Compose bash..."
server/bin/moodle-docker-compose exec webserver bash
