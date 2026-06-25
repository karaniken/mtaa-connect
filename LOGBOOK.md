# Mtaa-Connect – Logbook of Practical Progress

**Student Name:** Kennedy Karani  
**Admission Number:** BBIT/2024/56963  
**Course/Class:** Bachelor of Business Information Technology  
**Unit Code:** BIT3208  
**Unit Name:** Advanced Web Design and Development  
**Lecturer Name:** Mr. Nyoro  
**Semester/Academic Year:** 3.2  

---

## Project Details

**Project Title:** Mtaa‑Connect – Community Marketplace  
**Selected Technologies:** PHP, MySQL, Apache (LAMP stack), HTML/CSS/JS  
**Online Portfolio / GitHub Link:** https://github.com/karanken/mtaa-connect  

---

## Week 1: Local Environment Setup  

**Title:** Installation and Testing of Local Development Environment for Mtaa‑Connect  

### Required Evidence

**Fig 1: Apache default page running on localhost**  
![Fig 1: Apache default page](Week1/screenshots/fig1-apache.png)

**Fig 2: HTML Hello World test page (Mtaa-Connect branding)**  
![Fig 2: HTML Hello World](Week1/screenshots/fig2-hello-html.png)

**Fig 3: PHP test page with phpinfo() output**  
![Fig 3: PHP Info](Week1/screenshots/fig3-php-info.png)

**Fig 4: Database connection success message**  
![Fig 4: Database connection](Week1/screenshots/fig4-db-connect.png)

**Fig 5: phpMyAdmin login page**  
![Fig 5: phpMyAdmin](Week1/screenshots/fig5-phpmyadmin.png)

**Fig 6: Project folder structure in the terminal**  
![Fig 6: Folder structure](Week1/screenshots/fig6-folder-structure.png)

### Student Reflection (100 words)

I installed Apache, MariaDB, and PHP on Arch Linux to set up a local development environment for Mtaa‑Connect. The main challenge was configuring Apache to parse PHP – I had to uncomment the `LoadModule` directive in `httpd.conf` and restart the service. I then created a simple HTML page with the project's purple and red theme, a PHP info page to verify the installation, and a database connection script using `mysqli_connect()`. The connection returned a success message, confirming that MariaDB is running properly. I also set up phpMyAdmin for visual database management. I organised the project into `src/`, `database/`, and `documentation/` folders to maintain a professional structure. All services are now running correctly on `localhost`, and I have captured screenshots as evidence of each working component.

---




## Week 2: Wireframes and Database Schema  

**Title:** User Interface Planning and System Design for Mtaa‑Connect

### Required Evidence

**Fig 1: Wireframe – Homepage (property grid)**  
![Fig 1: Homepage Wireframe](Week2/wireframes/wireframe-homepage.png)

**Fig 2: Wireframe – Registration page (role selection)**  
![Fig 2: Registration Wireframe](Week2/wireframes/wireframe-register.png)

**Fig 3: Wireframe – Landlord Dashboard**  
![Fig 3: Landlord Dashboard Wireframe](Week2/wireframes/wireframe-landlord-dashboard.png)

**Fig 4: Wireframe – Property Detail page**  
![Fig 4: Property Detail Wireframe](Week2/wireframes/wireframe-property-detail.png)

**Fig 5: Database schema diagram (tables and relationships)**  
![Fig 5: Database Schema](Week2/screenshots/fig5-database-schema.png)

**Fig 6: SQL script executed successfully in phpMyAdmin**  
![Fig 6: SQL Execution](Week2/screenshots/fig6-sql-executed.png)

### Student Reflection (100 words)

This week I designed the wireframes for Mtaa‑Connect, focusing on the landlord and tenant user flows. The homepage prioritises search and discovery, while the registration page includes role selection to separate landlord and tenant experiences. I also designed the database schema with three tables: `users`, `properties`, and `inquiries`. The relationships ensure that properties are linked to landlords and inquiries are linked to both tenants and properties. I created the SQL script and tested it in phpMyAdmin – all tables were created successfully. This foundation will guide the coding in the coming weeks, ensuring a secure and structured backend.






## Week 3: JavaScript Validation and PHP Registration  
**Title:** Frontend Interaction and Backend Foundations for Mtaa‑Connect

### Required Evidence

**Fig 1: Registration form (red/purple theme)**  
![Fig 1: Registration form](Week3/screenshots/fig1-register-form.png)

**Fig 2: JavaScript validation errors on empty fields**  
![Fig 2: Validation errors](Week3/screenshots/fig2-validation-errors.png)

**Fig 3: Password and confirm password validation**  
![Fig 3: Password validation](Week3/screenshots/fig3-password-validation.png)

**Fig 4: Successful registration message**  
![Fig 4: Registration success](Week3/screenshots/fig4-registration-success.png)

**Fig 5: New user record in the database (phpMyAdmin)**  
![Fig 5: Database record](Week3/screenshots/fig5-database-record.png)

**Fig 6: PHP backend processing code snippet**  
![Fig 6: PHP code](Week3/screenshots/fig6-php-code.png)

### Student Reflection (100 words)

This week I built a registration system for Mtaa‑Connect with both client‑side and server‑side validation. 
The JavaScript checks for empty fields, email format, phone format, password length, and password confirmation before submission. 
On the PHP side, I used `password_hash()` to securely store passwords, `mysqli_real_escape_string()` to prevent SQL injection, and checked for duplicate emails to avoid conflicts. 
The form follows the red and purple colour scheme and is responsive. 
I tested the system with valid and invalid inputs – errors are displayed clearly, and successful registrations save a new user to the `users` table. 
This exercise reinforced the importance of layered validation and secure data handling.


