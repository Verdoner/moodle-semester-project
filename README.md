# Moodle Docker Setup

## Prerequisites

Before setting up the Moodle Docker environment, ensure the following prerequisites are installed:

1. **Docker Desktop**: [Install Docker](https://docs.docker.com/get-docker/)  
   Docker is required to build and run the Moodle Docker container.

2. **WSL 2**: [Install WSL 2](https://learn.microsoft.com/en-us/windows/wsl/install)  
   If WSL 2 was not installed during the installation of Docker Desktop, then install WSL 2 by running the following command in PowerShell as Administrator:
   ```bash
   wsl --install
   ```
   To set WSL 2 as the default version, run:
   ```bash
   wsl --set-default-version 2
   ```

3. **Ubuntu for Windows**: [Install Ubuntu](https://www.microsoft.com/store/productId/9PDXGNCFSCZV)  
   After installing WSL 2, install Linux distribution Ubuntu from the Microsoft Store.

4. **Configure Docker with WSL 2:**
   - After installing Docker Desktop, open it and go to **Settings > General**. Enable "Use the WSL 2 based engine."
   - Go to **Settings > Resources > WSL Integration** and ensure the WSL 2 integration is enabled for your installed Linux distributions (e.g., Ubuntu).
   - This setup allows Docker Desktop to leverage WSL 2 for efficient container management on Windows.

> **Note**: Ensure all environment variables for Docker, WSL 2 & Ubuntu are properly configured in your system.

## Setting Up Ubuntu and Creating a User
Once Ubuntu is installed from the Microsoft Store, follow these steps to complete the initial setup:
1. **Open Ubuntu:**
   - Launch Ubuntu by searching for it in the Start menu or by running the command `wsl` in a PowerShell or Command Prompt window.
   - On the first run, Ubuntu will prompt you to create a new user account.
2. **Create a New User:**
   - Enter a username when prompted (this will be your default user in Ubuntu).
   - Create a password for this user and confirm it.
   - After setup, you will be logged in as the newly created user.
3. **Navigate to the User's Home Directory:**
   - By default, you will start in your home directory, which is located at:
      ```bash
      /home/USERNAME/
      ```
   - Replace USERNAME with the name you created in the previous step if needed. This is where your user-specific files and configurations are stored.
4. **Confirm Access to Home Directory:**
   - To verify your current directory, you can run:
     ```bash
     pwd
     ```
   - This should return a path similar to `/home/USERNAME`, confirming you’re in the correct location.

Now you’re ready to proceed with configuring your Moodle Docker environment in this directory.

## Clone the Repository
Choose one of the following commands based on your needs:

### Without the Default Moodle Folder:
This command clones only the repository without the default Moodle folder submodule.

Run:
```bash
git clone -b Docker-Setup https://github.com/AAU-P5-Moodle/moodle-2.git && cd ./moodle-2 && chmod -R 0777 ./server/moodledata && ( rm -rf .git || true )
```
### With the Default Moodle Folder:
This command clones the repository along with the default Moodle folder submodule.

Run:
```bash
git clone -b Docker-Setup --recursive https://github.com/AAU-P5-Moodle/moodle-2.git && cd ./moodle-2 && chmod -R 0777 ./server/moodledata && ( rm -rf .git || true )
```

## Set Up Sparse Checkout
Now run:
```bash
git init && git config core.sparseCheckout true && git remote add -f origin https://github.com/AAU-P5-Moodle/moodle-2.git && echo server/moodle > .git/info/sparse-checkout
```
Now you can checkout the branch you were working on:
```bash
git checkout [branchname] # e.g., main
```

## Copy Docker Template Files
Copy the template files for Docker into the Moodle folder:

Run:
```bash
cp server/composer.docker-template.json server/moodle/composer.json && cp server/config.docker-template.php server/moodle/config.php && cp server/Gruntfile.docker-template.js server/moodle/Gruntfile.js && cp server/package.docker-template.json server/moodle/package.json
```

## Start Moodle
Run: `sh start_moodle_unix.sh`

> Note: The first start may take a long time, as it needs to install many composer and node modules.

## Access Docker Terminal
You can open a terminal inside Docker with:

Run: `sh bash_moodle_unix.sh`

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

## Exit Docker Terminal
You can exit the Docker terminal with: `exit`

## Notes
- The setup may take some time due to dependency installation.
