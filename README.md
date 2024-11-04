# Moodle Docker Setup

## Prerequisites

Before setting up the Moodle Docker environment, ensure the following prerequisites are installed:

1. **Docker**: [Install Docker](https://docs.docker.com/get-docker/)  
   Docker is required to build and run the Moodle Docker container.

2. **Git**: [Install Git](https://git-scm.com/downloads)  
   Git is required to clone the repository and manage version control.

> **Note**: Ensure all environment variables for Docker and Git are properly configured in your system.

## Clone the Repository

1. Create an empty folder where you want to clone the repository.

2. Choose one of the following commands based on your needs:

### Without the Default Moodle Folder:
This command clones only the repository without the default Moodle folder submodule.

On Unix (Linux & MacOS), run:
```bash
( rm -rf ./* || true ) && git clone -b Docker-Setup https://github.com/AAU-P5-Moodle/moodle-2.git . && ( rm -rf .git || true )
```
On Windows, run:
```bash
del /f /q *.* && for /d %i in (*) do rmdir /s /q "%i" && git clone -b Docker-Setup https://github.com/AAU-P5-Moodle/moodle-2.git . && rmdir /s /q .git
```
### With the Default Moodle Folder:
This command clones the repository along with the default Moodle folder submodule.

On Unix (Linux & MacOS), run:
```bash
( rm -rf ./* || true ) && git clone -b Docker-Setup --recursive https://github.com/AAU-P5-Moodle/moodle-2.git . && ( rm -rf .git || true )
```
On Windows, run:
```bash
del /f /q *.* && for /d %i in (*) do rmdir /s /q "%i" &&  git clone -b Docker-Setup --recursive https://github.com/AAU-P5-Moodle/moodle-2.git . && rmdir /s /q .git
```

## Set Up Sparse Checkout
Now run:
```bash
git init && git config core.sparseCheckout true && git remote add -f origin https://github.com/AAU-P5-Moodle/moodle-2.git && echo server/moodle > .git/info/sparse-checkout
```
```bash
git checkout [branchname] # e.g., main
```

## Copy Docker Template Files
Copy the template files for Docker into the Moodle folder:

On Unix (Linux & MacOS), run:
```bash
cp server/composer.docker-template.json server/moodle/composer.json && cp server/config.docker-template.php server/moodle/config.php && cp server/package.docker-template.json server/moodle/package.json
```
On Windows, run:
```bash
copy server\composer.docker-template.json server\moodle\composer.json && copy server\config.docker-template.php server\moodle\config.php && copy server\package.docker-template.json server\moodle\package.json
```

## Start Moodle
On Unix (Linux & MacOS), run: `sh start_moodle_unix.sh`

On Windows, run: `start_moodle_windows.bat`

> Note: The first start may take a long time, as it needs to install many composer and node modules.

## Access Docker Terminal
You can open a terminal inside Docker with:

On Unix (Linux & MacOS), run: `sh bash_moodle_unix.sh`

On Windows, run: `bash_moodle_windows.bat`

## Initialize Testing and Tools
Once inside the Docker terminal, initialize the following tools:
### PHPUnit
```bash
php admin/tool/phpunit/cli/init.php
vendor/bin/phpunit --testsuite=mod_homework_testsuite
```
### Behat
```bash
php admin/tool/behat/cli/init.php
php admin/tool/behat/cli/run.php --tags=@mod_homework
```
### Codesniffer
```bash
vendor/bin/phpcs --standard=moodle-extra mod/homework
```
### Grunt
```bash
grunt amd --root="./mod/homework" --force
```

## Notes
- The setup may take some time due to dependency installation.
