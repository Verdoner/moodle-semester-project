# Moodle Docker Setup for Unix-based systems (Linux & MacOS)

## Prerequisites

Before setting up the Moodle Docker environment, ensure the following prerequisites are installed:

1. **Docker**: [Install Docker](https://docs.docker.com/get-docker/)  
   Docker is required to build and run the Moodle Docker container.

2. **Git**: [Install Git](https://git-scm.com/downloads)  
   Git is required to clone the repository and manage version control.
> [!IMPORTANT]
> **Ensure all environment variables for Docker and Git are properly configured in your system.**

## Clone the Repository

1. Open your terminal and navigate to the folder where you would like to clone this repository.

2. Choose one of the following commands based on your needs:

### Without the Default Moodle Folder:
This command clones only the repository without the default Moodle folder submodule:
```bash
git clone -b Docker-Setup-Unix https://github.com/AAU-P5-Moodle/moodle-2.git && cd ./moodle-2 && chmod -R 0777 ./server/moodledata && ( rm -rf .git || true )
```
### With the Default Moodle Folder:
This command clones the repository along with the default Moodle folder submodule:
```bash
git clone -b Docker-Setup-Unix --recursive https://github.com/AAU-P5-Moodle/moodle-2.git && cd ./moodle-2 && chmod -R 0777 ./server/moodledata && ( rm -rf .git || true )
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
> [!NOTE]
> **The initial startup may take some time, as it will install various Composer and Node modules.**

   ### Checking if Moodle is Ready

   Once you start Moodle with the script, you can monitor the container logs to know when it’s fully ready.
   - Open your Docker dashboard and locate the container labeled `server-webserver-1`.
   - When you see the message "**Running 'watch' task**" in the logs of this container, Moodle is ready for use.

## Accessing MariaDB, Behat, Moodle and phpMyAdmin
Once Moodle is ready, you can access the following services from your web browser:
- **MariaDB**: You can connect to the MariaDB database using your preferred database management software, such as **HeidiSQL**, **Sequel Pro**, or **MySQL Workbench**. Use the following credentials and connection details:
   - Host: `localhost` or `127.0.0.1` 
   - Port: `3306`
   - Username: `root`
   - Password: `root`
- **Moodle**: Navigate to http://localhost:8000 to view Moodle.
- **phpMyAdmin**: Navigate to http://localhost:8080 to access the phpMyAdmin interface for managing the database.
- **Behat**: Navigate to http://localhost:7900/?autoconnect=1&resize=scale&password=secret to view Behat tests running on Moodle.
   > **Note**: If you get logged out of the VNC for the Behat tests, you can use the password `secret` to log back in.
> [!TIP]  
> **If you’d like to run tests with a local Selenium server instead, please refer to the [Setting up Selenium server for Behat testing locally](#setting-up-selenium-server-for-behat-testing-locally) section. This provides an alternative approach for executing Behat tests locally, allowing you to select specific browsers and enabling direct interaction with the Selenium server setup.**

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

# Additional Notes
## Setting up Selenium server for Behat testing locally
To run Behat tests with a local Selenium server, you will need to download the Selenium server JAR file and make some configuration changes.
1. **Download Selenium server**  
   Ensure you have **Java 11 or higher** installed, as it is required to run the Selenium server.
   Then, download the Selenium server JAR file from the following link:
   - [Selenium Server 4.25.0](https://github.com/SeleniumHQ/selenium/releases/download/selenium-4.25.0/selenium-server-4.25.0.jar)
      
3. **Run the Selenium server**  
   Navigate to the folder where the Selenium server JAR file is located.  
   Depending on your operating system, use one of the following commands to start the Selenium server:
   - Open a new terminal (dedicated to running Selenium) and execute:
     ```bash
     java -jar selenium-server-4.25.0.jar standalone
     ```
4. **Modify Moodle configuration for Behat**  
   To configure Moodle for use with the Selenium server, make the following adjustments in the `config.php` file located in the `/server/moodle` directory.
   1. Open `/server/moodle/config.php`.
   2. Locate the following lines:
      ```php
      $CFG->behat_wwwroot = 'http://webserver';
      ```
      ```php
      $CFG->behat_profiles = array(
          'default' => array(
              'browser' => getenv('MOODLE_DOCKER_BROWSER'),
              'wd_host' => 'http://selenium:4444/wd/hub',
          ),
      );
      ```
   3. Update them to the following configuration:
      ```php
      $CFG->behat_wwwroot = 'http://127.0.0.1:8000';
      ```
      ```php
      $CFG->behat_profiles = array(
          'default' => array(
              'browser' => "chrome", // or "firefox", "safari", or any other locally installed browser supported by Selenium
              'wd_host' => 'http://host.docker.internal:4444/wd/hub', // Works on MacOS; for Linux, use a different IP as per https://stackoverflow.com/a/70725882
          ),
      );
      ```
      - **Browser setting**: Set `browser` to match the locally installed browser you want Selenium to use (e.g., `"chrome"`, `"firefox"` or `"safari"`).
      - **Selenium WebDriver host (`wd_host`)**:
        - For **MacOS**, the host should be set to `http://host.docker.internal:4444/wd/hub`.
        - For **Linux**, refer to [this workaround](https://stackoverflow.com/a/70725882) for the correct IP address if `host.docker.internal` is not supported.
       
5. **Reinitialize Behat**  
   After modifying the `config.php` file, you must reinitialize Behat to apply the new settings. Run the following command inside your Docker terminal:
   ```bash
   php admin/tool/behat/cli/init.php
   ```
   This step ensures that the updated configuration is recognized by Behat, allowing it to use the local Selenium server.
   Now you can [run Behat tests](#behat) locally.
