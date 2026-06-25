# Mtaa-Connect – Logbook of Practical Progress

## Week 1: Local Environment Setup  
**Title:** Installation and Testing of Local Development Environment for Mtaa-Connect

### Required Evidence

**Fig 1: Apache default page running on localhost**  
![Fig 1: Apache default page](screenshots/week1/fig1-apache.png)

**Fig 2: HTML Hello World test page (Mtaa-Connect branding)**  
![Fig 2: HTML Hello World](screenshots/week1/fig2-hello-html.png)

**Fig 3: PHP test page with phpinfo() output**  
![Fig 3: PHP Info](screenshots/week1/fig3-php-info.png)

**Fig 4: Database connection success message**  
![Fig 4: Database connection](screenshots/week1/fig4-db-connect.png)

**Fig 5: phpMyAdmin login page**  
![Fig 5: phpMyAdmin](screenshots/week1/fig5-phpmyadmin.png)

**Fig 6: Project folder structure in the terminal**  
![Fig 6: Folder structure](screenshots/week1/fig6-folder-structure.png)

### Student Reflection (100 words)

I installed Apache, MariaDB, and PHP on Arch Linux to set up a local development environment for Mtaa‑Connect. 
The main challenge was configuring Apache to parse PHP – I had to uncomment the `LoadModule` directive in `httpd.conf` 
and restart the service. 
I then created a simple HTML page with the project's purple and red theme, a PHP info page to verify the installation, and a database connection script using
 `mysqli_connect()`. The connection returned a success message, confirming that MariaDB is running properly. I also set up phpMyAdmin for visual database management. 
I organized the project into `src/`, `database/`, and `documentation/` folders to maintain a professional structure. 
All services are now running correctly on `localhost`, and I have captured screenshots as evidence of each working component.
