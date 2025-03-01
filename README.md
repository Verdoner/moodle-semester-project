# Homework Plugin

## Overview
This plugin adds a new activity resource as an option in Moodle courses. It enables the creation, management, and display of homework assignments on the dashboard and calendar.

## Features
- **Activity Module:** Allows adding homework as an activity in a course.
- **Homework Details:** Users can add descriptions, start times, due dates, and materials.
- **Material Management:** Create and reuse materials such as literature, links, and embedded videos.
- **Dashboard & Calendar Integration:** Homework appears on the dashboard and calendar for easy tracking.
- **Filtering & Sorting:** Users can filter by course, time period, and sort by due date.
- **Material Download:** Students can download all homework materials as a ZIP file.

## File Locations
- **Activity Module:** `server/moodle/mod/homework`
- **Block Module:** `server/moodle/blocks/homework`

# Moodle Docker Setup (Windows with WSL 2 & Ubuntu)

## Prerequisites
Ensure you have the following installed:
- **Docker Desktop** ([Install Docker](https://docs.docker.com/get-docker/))
- **WSL 2** (If not installed with Docker, run in PowerShell as Admin:)
  ```sh
  wsl --install
  wsl --set-default-version 2
  ```
- **Ubuntu for Windows** ([Install Ubuntu](ms-windows-store://pdp/?ProductId=9PDXGNCFSCZV))
- **Enable WSL in Docker:**
  - Go to Docker **Settings > General** → Enable "Use the WSL 2 based engine."
  - In **Settings > Resources > WSL Integration**, enable for Ubuntu.

## Setting Up Ubuntu
1. **Launch Ubuntu** (via Start menu or `wsl -d Ubuntu`).
2. **Create a User** (follow prompts to set a username and password).
3. **Navigate to Home Directory:**
   ```sh
   cd ~
   pwd  # Should return /home/USERNAME
   ```

## Cloning the Repository
Choose one of the following:
- **Without Default Moodle Folder:**
  ```sh
  git config --global credential.helper store && \
  git clone -b Docker-Setup-Windows https://github.com/AAU-P5-Moodle/moodle-2.git && \
  cd ./moodle-2 && chmod -R 0777 ./server/moodledata && \
  ( rm -rf .git .gitignore || true )
  ```
- **With Default Moodle Folder:**
  ```sh
  git config --global credential.helper store && \
  git clone -b Docker-Setup-Windows --recursive https://github.com/AAU-P5-Moodle/moodle-2.git && \
  cd ./moodle-2 && chmod -R 0777 ./server/moodledata && \
  ( rm -rf .git .gitignore || true )
  ```
- **GitHub Authentication:**
  - Use **GitHub username** when prompted.
  - Use **Personal Access Token** instead of a password ([Generate Token](https://github.com/settings/tokens)).

## Sparse Checkout (Optional)
For minimal checkout:
```sh
  git init && git config core.sparseCheckout true && \
  git remote add -f origin https://github.com/AAU-P5-Moodle/moodle-2.git && \
  echo -e "server/moodle\n.gitignore" > .git/info/sparse-checkout
  git checkout [branchname]  # Replace with actual branch name
```

## Setting Up Moodle in Docker
1. **Copy Docker Template Files:**
   ```sh
   cp server/composer.docker-template.json server/moodle/composer.json && \
   cp server/config.docker-template.php server/moodle/config.php && \
   cp server/Gruntfile.docker-template.js server/moodle/Gruntfile.js && \
   cp server/package.docker-template.json server/moodle/package.json
   ```
2. **Start Moodle:**
   ```sh
   sh start_moodle_unix.sh
   ```
   If permission error occurs:
   ```sh
   sudo sh start_moodle_unix.sh
   ```

## Accessing Services
- **Moodle:** [http://localhost:8000](http://localhost:8000)
- **phpMyAdmin:** [http://localhost:8080](http://localhost:8080)
- **MariaDB Credentials:**
  ```
  Host: localhost | Port: 3306
  User: root | Password: root
  ```
- **Behat:** [http://localhost:7900/?autoconnect=1&resize=scale&password=secret](http://localhost:7900/?autoconnect=1&resize=scale&password=secret)

## Docker Terminal Access
To enter the Moodle Docker container:
```sh
sh bash_moodle_unix.sh
```
If permission denied:
```sh
sudo sh bash_moodle_unix.sh
```

## Running Tests & Tools
Inside Docker terminal:
- **PHPUnit:**
  ```sh
  php admin/tool/phpunit/cli/init.php
  vendor/bin/phpunit --testsuite=mod_homework_testsuite
  ```
- **Behat:**
  ```sh
  php admin/tool/behat/cli/init.php
  php admin/tool/behat/cli/run.php --tags=@mod_homework
  ```
- **CodeSniffer:**
  ```sh
  vendor/bin/phpcs --standard=moodle-extra mod/homework
  ```
- **Grunt:**
  ```sh
  grunt amd --force --root=/mod/homework
  ```
- **Exit Docker Terminal:**
  ```sh
  exit
  ```

# Setting Up Selenium for Behat
1. **Download Selenium Server** ([Selenium 4.25.0](https://www.selenium.dev/downloads/)).
2. **Run Selenium Server:**
   ```sh
   java -jar selenium-server-4.25.0.jar standalone
   ```
3. **Modify Moodle Config (`/server/moodle/config.php`):**
   ```php
   $CFG->behat_wwwroot = 'http://127.0.0.1:8000';
   $CFG->behat_profiles = array(
       'default' => array(
           'browser' => 'chrome',
           'wd_host' => 'http://host.docker.internal:4444/wd/hub',
       ),
   );
   ```
4. **Reinitialize Behat:**
   ```sh
   php admin/tool/behat/cli/init.php
   ```

Now, Behat tests can run locally with a Selenium server!
