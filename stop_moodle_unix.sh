#!/bin/bash

# chmod +x stop_moodle_unix.sh

export MOODLE_DOCKER_WWWROOT=./server/moodle
export MOODLE_DOCKER_DB=mariadb

echo "Stopping Moodle Docker Compose services..."
server/bin/moodle-docker-compose down
echo "Moodle services stopped."
