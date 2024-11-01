#!/bin/bash

# chmod +x start_moodle_unix.sh
# chmod +x server/bin/moodle-docker-compose

export MOODLE_DOCKER_WWWROOT=./server/moodle
export MOODLE_DOCKER_DB=mariadb

echo "Stopping Moodle Docker Compose services..."
server/bin/moodle-docker-compose stop
echo "Moodle services stopped."
