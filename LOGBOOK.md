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

### Student Reflection.

This week I built a registration system for Mtaa‑Connect with both client‑side and server‑side validation. 
The JavaScript checks for empty fields, email format, phone format, password length, and password confirmation before submission. 

On the PHP side, I used `password_hash()` to securely store passwords, `mysqli_real_escape_string()` to prevent SQL injection, and checked for duplicate emails to avoid conflicts. 
The form follows the red and purple colour scheme and is responsive. 

I tested the system with valid and invalid inputs – errors are displayed clearly, and successful registrations save a new user to the `users` table. 
This exercise reinforced the importance of layered validation and secure data handling.




## Week 4: Login System and Session Management  

**Title:** User Authentication and Role‑Based Access for Mtaa‑Connect

### Required Evidence

**Fig 1: Login form (red/purple theme)**  
![Fig 1: Login form](Week4/screenshots/fig1-login-form.png)

**Fig 2: JavaScript validation – empty fields**  
![Fig 2: Validation error](Week4/screenshots/fig2-validation-error.png)

**Fig 3: Invalid login attempt error**  
![Fig 3: Invalid login](Week4/screenshots/fig3-invalid-login.png)

**Fig 4: Landlord dashboard**  
![Fig 4: Landlord dashboard](Week4/screenshots/fig4-landlord-dashboard.png)

**Fig 5: Tenant dashboard**  
![Fig 5: Tenant dashboard](Week4/screenshots/fig5-tenant-dashboard.png)

**Fig 6: Logout redirect to login page**  
![Fig 6: Logout](Week4/screenshots/fig6-logout.png)

### Student Reflection.

This week I built the login system for Mtaa‑Connect using PHP sessions and password hashing. 
The login page validates credentials against the `users` table using `password_verify()`. 
On successful login, session variables are set and the user is redirected to a role‑specific dashboard. 
Landlords see property management options, while tenants see search and inquiry options. 
I also implemented a logout that destroys the session. 
The dashboard pages are protected – any direct access redirects to login. 
This provides a secure foundation for future features like property CRUD and inquiries.




## Week 5: Property & Unit CRUD Operations
  
**Title:** Building the Core Portal – Property Management for Landlords and Browsing for Tenants

### Required Evidence

**Fig 1: Add Property form (landlord)**  
![Fig 1: Add Property](Week5/screenshots/fig1-add-property.png)

**Fig 2: Add Unit form under a property**  
![Fig 2: Add Unit](Week5/screenshots/fig2-add-unit.png)

**Fig 3: My Properties page with units listed**  
![Fig 3: My Properties](Week5/screenshots/fig3-my-properties.png)

**Fig 4: Browse page (tenant view)**  
![Fig 4: Browse Units](Week5/screenshots/fig4-browse-units.png)

**Fig 5: Search filter working**  
![Fig 5: Search Filter](Week5/screenshots/fig5-search-filter.png)

**Fig 6: Database tables (properties + units) in phpMyAdmin**  
![Fig 6: Database Tables](Week5/screenshots/fig6-database-tables.png)

### Student Reflection.

This week I built the core functionality of the Mtaa‑Connect portal. 
Landlords can now create properties (buildings/complexes) and add multiple units (apartments/rooms) under each property. 
Each unit stores floor number, house number, size, price, amenities, and status (vacant/occupied/booked). 
Tenants can browse all vacant units with search and filter by location and size. 
This transforms the project from a simple auth system into a fully functional real‑estate marketplace. 
I also learned about foreign keys and cascading deletes to maintain data integrity. 
The portal is now ready for the final phase: inquiries and notifications.


## Week 6: Inquiries and Notifications.

**Title:** Connecting Tenants and Landlords – Inquiry & Reply System

### Required Evidence

**Fig 1: Tenant inquiry form**
![Fig 1: Inquiry Form](Week6/screenshots/fig1-inquiry-form.png)

**Fig 2: Inquiry sent confirmation**
![Fig 2: Inquiry Sent](Week6/screenshots/fig2-inquiry-sent.png)

**Fig 3: Landlord dashboard with unread badge**
![Fig 3: Dashboard Badge](Week6/screenshots/fig3-dashboard-badge.png)

**Fig 4: Inquiries list for landlord**
![Fig 4: Inquiries List](Week6/screenshots/fig4-inquiries-list.png)

**Fig 5: Landlord reply form**
![Fig 5: Reply Form](Week6/screenshots/fig5-reply-form.png)

**Fig 6: Tenant inquiry history with reply**
![Fig 6: Tenant History](Week6/screenshots/fig6-tenant-history.png)

### Student Reflection (100 words)

This week I built the inquiry and notification system, completing the marketplace interaction loop. 
Tenants can now send inquiries about vacant units, and landlords receive them with unread badges. 
Landlords can reply directly from the inquiries page, and tenants can view their inquiry history with landlord responses. 
The system uses a dedicated `inquiries` table with `is_read` and `replied` flags to track communication status. 
I also integrated unread counts into the dashboard, providing real‑time notifications. 
This feature transforms the platform from a static listing site into an interactive marketplace where landlords and tenants can communicate seamlessly.



## Week 7: Architecture & Design (UML, Security)
**Title:** Documenting System Structure and Security Design for Mtaa‑Connect

### Required Evidence

**Fig 1: Multi‑Tier Architecture Diagram**  
![Fig 1: Architecture](Week7/screenshots/fig1-architecture.png)

**Fig 2: UML Use Case Diagram**  
![Fig 2: Use Case](Week7/screenshots/fig2-usecase.png)

**Fig 3: UML Class Diagram**  
![Fig 3: Class Diagram](Week7/screenshots/fig3-class.png)

**Fig 4: Database ERD**  
![Fig 4: ERD](Week7/screenshots/fig4-erd.png)

**Fig 5: Security Design Document**  
![Fig 5: Security](Week7/screenshots/fig5-security.png)

**Fig 6: Additional Design Diagram**  
![Fig 6: Additional](Week7/screenshots/fig6-additional.png)

### Student Reflection.

This week I documented the architecture and design of Mtaa‑Connect using UML diagrams. 
I created a three‑tier architecture diagram showing the presentation (HTML/CSS/JS), business logic (PHP), and data (MySQL) layers. 
The use case diagram identifies three actors: Landlord, Tenant, and Guest, with their respective functionalities. 

The class diagram maps the core entities: User, Property, Unit, UnitMedia, and Inquiry, with their relationships. 
The database ERD visualizes the actual table structures and foreign keys. 
I also wrote a security design document covering authentication, authorization, input validation, SQL injection prevention, and file upload security. 
This documentation helps demonstrate my understanding of system design principles and will be valuable for the final project report.



## Week 8: Responsive Web Design & Mobile‑First UI
**Title:** Making Mtaa‑Connect Fully Responsive with Mobile‑First Approach

### Required Evidence

**Fig 1: Mobile view with hamburger menu open**  
![Fig 1: Mobile Hamburger](Week8/screenshots/fig1-mobile-hamburger.png)

**Fig 2: Mobile view – browse page (filters stacked)**  
![Fig 2: Mobile Browse](Week8/screenshots/fig2-mobile-browse.png)

**Fig 3: Tablet view – dashboard with 2‑column layout**  
![Fig 3: Tablet Dashboard](Week8/screenshots/fig3-tablet-dashboard.png)

**Fig 4: Desktop view – browse with 3‑column grid**  
![Fig 4: Desktop Browse](Week8/screenshots/fig4-desktop-browse.png)

**Fig 5: Mobile‑first form (registration/inquiry)**  
![Fig 5: Mobile Form](Week8/screenshots/fig5-mobile-form.png)

**Fig 6: CSS file with media queries**  
![Fig 6: CSS Code](Week8/screenshots/fig6-css-code.png)

### Student Reflection 

This week I transformed Mtaa‑Connect into a fully responsive, mobile‑first web application. 
I consolidated all styles into a single `style.css` file and used CSS Grid and Flexbox for layouts. 
I implemented a hamburger menu for mobile navigation that toggles smoothly. Using media queries, I created breakpoints at 768px (tablet) and 1024px (desktop) to adapt the UI. 
The browse page now shows filters stacked on mobile and inline on desktop, while unit cards adjust from 1 column to 3+ columns. 
I tested the responsive design using Chrome DevTools on multiple device sizes. 
The result is a portal that works seamlessly on phones, tablets, and desktops, making it accessible to all users.
