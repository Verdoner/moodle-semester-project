# Moodle Docker Setup for Unix-based systems (Linux & MacOS)

## Prerequisites

Before setting up the Moodle Docker environment, ensure the following prerequisites are installed:

1. **Docker**: [Install Docker](https://docs.docker.com/get-docker/)  
   Docker is required to build and run the Moodle Docker container.

2. **Git**: [Install Git](https://git-scm.com/downloads)  
   Git is required to clone the repository and manage version control.
> **Note**: Ensure all environment variables for Docker and Git are properly configured in your system.

## Clone the Repository

1. Open your terminal and navigate to the folder where you would like to clone this repository.

2. Choose one of the following commands based on your needs:

### Without the Default Moodle Folder:
This command clones only the repository without the default Moodle folder submodule:
```bash
( rm -rf ./* || true ) && ( rm -rf .* || true ) && git clone -b Docker-Setup-Unix https://github.com/AAU-P5-Moodle/moodle-2.git . && ( rm -rf .git || true )
```
### With the Default Moodle Folder:
This command clones the repository along with the default Moodle folder submodule:
```bash
( rm -rf ./* || true ) && ( rm -rf .* || true ) && git clone -b Docker-Setup-Unix --recursive https://github.com/AAU-P5-Moodle/moodle-2.git . && ( rm -rf .git || true )
```

## Set Up Sparse Checkout
1. Initialize the sparse checkout configuration by running:
   ```bash
   git init && git config core.sparseCheckout true && git remote add -f origin https://github.com/AAU-P5-Moodle/moodle-2.git && echo server/moodle > .git/info/sparse-checkout
   ```
2. Now, check out the branch you were working on:
   ```bash
   git checkout [branchname]  # Replace [branchname] with your branch name, e.g., main
   ```

## Copy Docker Template Files
Copy the template files for Docker into the Moodle folder:
```bash
cp server/composer.docker-template.json server/moodle/composer.json && cp server/config.docker-template.php server/moodle/config.php && cp server/Gruntfile.docker-template.js server/moodle/Gruntfile.js && cp server/package.docker-template.json server/moodle/package.json
```

## Start Moodle
1. Ensure that Docker Engine is running.
2. Ensure you're in the cloned directory.
3. Start Moodle by running:
   ```bash
   sh start_moodle_unix.sh
   ```
   If you encounter a **Permission Denied** error, you may need to run the command with `sudo`:
   ```bash
   sudo sh start_moodle_unix.sh
   ```
   This will grant the necessary permissions to execute the script.
   > Note: The initial startup may take some time, as it will install various Composer and Node modules.

   ### Checking if Moodle is Ready

   Once you start Moodle with the script, you can monitor the container logs to know when it’s fully ready.
   - Open your Docker dashboard and locate the container labeled `server-webserver-1`.
   - When you see the message "**Running 'watch' task**" in the logs of this container, Moodle is ready for use.

## Accessing Moodle and phpMyAdmin
Once Moodle is ready, you can access the following services from your web browser:
- **Moodle**: Navigate to http://localhost:8000 to view Moodle.
- **phpMyAdmin**: Navigate to http://localhost:8080 to access the phpMyAdmin interface for managing the database.

## Access Docker Terminal
1. To open a terminal inside Docker, confirm you're in the cloned directory.
2. Run the following command:
   ```bash
   sh bash_moodle_unix.sh
   ```
   If you encounter a **Permission Denied** error, you may need to run the command with `sudo`:
   ```bash
   sudo sh bash_moodle_unix.sh
   ```
   This will grant the necessary permissions to execute the script.

## Initialize Testing and Tools
Once inside the Docker terminal, initialize the following tools:
### PHPUnit
Initialize PHPUnit:
```bash
php admin/tool/phpunit/cli/init.php
```
Run PHPUnit with the specified test suite:
```bash
vendor/bin/phpunit --testsuite=mod_homework_testsuite
```
### Behat
Set up Behat:
```bash
php admin/tool/behat/cli/init.php
```
Execute Behat tests with the desired tags:
```bash
php admin/tool/behat/cli/run.php --tags=@mod_homework
```
### Codesniffer
Run PHP CodeSniffer in a desired directory using the Moodle-extra standard:
```bash
vendor/bin/phpcs --standard=moodle-extra mod/homework
```
### Grunt
Compile JavaScript with Grunt in a desired directory:
```bash
grunt amd --force --root=/mod/homework
```

## Exit Docker Terminal
You can exit the Docker terminal with: `exit`

## Notes
- The setup may take some time due to dependency installation.
