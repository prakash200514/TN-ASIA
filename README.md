
# 🚌 TNSTC Smart Bus Management System (Tirunelveli District)

An enterprise-grade, role-based web application designed for the **Tamil Nadu State Transport Corporation (TNSTC) – Tirunelveli District**. It provides a comprehensive digital platform connecting passengers, drivers/conductors, depot managers, the administration, and state governance (district minister).


## 🚀 Project Overview

The **TNSTC Smart Bus System** streamlines transport logistics, passenger ticketing, pass verification, emergency management, and real-time operations for the **7 key depots** in Tirunelveli District:
1. **Thamirabarani Depot** (Vannarpettai)
2. **Bye-Pass Depot** (Vannarpettai)
3. **Kattabomman Nagar Depot** (KTC Nagar)
4. **Cheranmahadevi Depot** (Cheranmahadevi)
5. **Valliyoor Depot** (Valliyoor)
6. **Thisayanvilai Depot** (Thisayanvilai)
7. **Papanasam Depot** (Papanasam)


## 🛠️ Technology Stack

- **Backend**: Native PHP 8.x (using PDO for secure, prepared SQL statements)
- **Frontend**: HTML5, CSS3 (Custom `tnstc.css` Design System), JavaScript (ES6+), Bootstrap 5, Font Awesome 6
- **Database**: MySQL (InnoDB engine with strict foreign key constraints)
- **Integrations**: Google Maps API (Live Tracking), Google Charts / Charts.js (Analytics), QR Code API (Ticket & Pass validation)


## 👥 User Roles & Features

### 1. 🧑‍💼 Passenger Portal
- **Secure Authentication**: Registration and Login with OTP email verification (`auth/otp_verify.php`) and password recovery.
- **Search Bus**: Search bus schedules and route stops with real-time seat availability (`passenger/search_bus.php`).
- **Ticket Booking**: Interactive seat selector, online payment simulation, and instant PDF/QR code ticket generation (`passenger/book_ticket.php`).
- **Live Tracking**: Locate buses on interactive Google Maps (`passenger/live_tracking.php`).
- **Smart Bus Pass**: Apply for Monthly or Student passes with proof document uploads (`passenger/bus_pass.php`).
- **Complaints & Grievances**: Lodge complaints (categorized by delay, staff behavior, cleanliness, etc.) and track resolution status (`passenger/complaints.php`).
- **Lost & Found**: Report lost belongings or claim items recovered in buses (`passenger/lost_found.php`).
- **AI Chatbot**: Intelligent virtual assistant to answer queries on routes, timings, and fares (`passenger/chatbot.php`).

### 2. 🚌 Driver & Conductor Panel
- **Assigned Trips**: View duty rosters and scheduled trips (`driver/assigned_trips.php`).
- **QR Ticket Validator**: Scan passenger tickets using mobile camera and match via API (`driver/scan_ticket.php`).
- **Real-time Delay Updates**: Post immediate delay details and reason codes directly to the passenger app (`driver/delay_update.php`).
- **SOS Emergency System**: Trigger critical safety alarms detailing location coordinates to depot control (`driver/emergency.php`).
- **Passenger Manifest**: Access the manifest lists of ticket holders for the current run (`driver/passenger_list.php`).

### 3. 🏢 Depot Manager Dashboard
- **Fleet Management**: Audit, register, and update status of depot buses (`depot_manager/buses.php`).
- **Roster & Scheduling**: Manage schedule assignments for routes, drivers, and conductors (`depot_manager/schedules.php`).
- **Bus Pass Verification**: Review submitted student/monthly documents and approve/reject passes (`depot_manager/pass_verification.php`).
- **Staff Control**: Track leave and status for drivers and conductors (`depot_manager/staff.php`).
- **Grievance Redressal**: Read complaints and submit official resolution replies (`depot_manager/complaints.php`).
- **Analytics & Reports**: Visual charts depicting passenger volume, revenue, and delays (`depot_manager/reports.php`).

### 4. 🏛️ TNSTC Minister Panel
- **Executive Analytics**: Global dashboard summarizing district-wide operations (`minister/dashboard.php`).
- **Depot Performance**: Comparative analysis of efficiency, bookings, and revenue metrics (`minister/depot_performance.php`).
- **Emergency Reports**: Review active and resolved safety alerts (`minister/emergency_report.php`).
- **Delay Reports**: Track overall punctuality rates and depot delay factors (`minister/delay_report.php`).
- **Complaint Analytics**: Track categories of grievances to identify areas for systemic improvement (`minister/complaint_analytics.php`).

### 5. ⚙️ System Administrator Dashboard
- **User Control**: Complete access control over user permissions, role elevation, and activation status (`admin/manage_users.php`).
- **Depot & Route Registries**: Manage primary depot coordinates, routes, and individual route stops (`admin/manage_depots.php`, `admin/manage_routes.php`).
- **Audit Logs & Master Management**: Manage master copies of tickets, schedules, passes, complaints, and recovered items system-wide.



## 🗄️ Database Schema Outline

The application runs on `tnstc_tirunelveli` database schema. Key tables include:
- `users`: Stores passenger, driver, conductor, manager, minister, and admin credentials.
- `depots`: Office names, locations, manager foreign keys, and map coordinates.
- `buses`: Registered vehicles, categories (Ordinary, Express, AC, etc.), seat capacity, and status.
- `routes` & `route_stops`: Defines routes, intermediate stops, distance, and sequence.
- `schedules`: Operational runs mapped to a bus, route, driver, conductor, time, date, and delay offsets.
- `tickets`: Paid tickets storing bookings, passenger details, seat numbers, fare, and verification QR links.
- `bus_pass`: Applications and active bus passes with validity range and document paths.
- `complaints` & `lost_found`: Grievance records and items ledger.
- `live_tracking` & `emergency_alerts`: Live coordinates of running buses and SOS distress signals.

## ⚙️ Installation & Setup

### Requirements
- **Server Environment**: Apache (XAMPP / WAMP / MAMP or standalone PHP/Apache setup)
- **PHP Version**: 8.0 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.4+
- **SMTP Credentials**: Required for mail notifications and OTP verification.

### Step-by-Step Installation

1. **Clone the Repository**  
   Place the project files into your web root directory (e.g., `C:\xampp\htdocs\TNSTC`).

2. **Database Setup**  
   - Start MySQL on your local server.
   - Open phpMyAdmin or your MySQL client.
   - Create a database named `tnstc_tirunelveli`:
     ```sql
     CREATE DATABASE tnstc_tirunelveli CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```
   - Import the `database.sql` file:
     ```bash
     mysql -u root -p tnstc_tirunelveli < database.sql
     

3. **Application Configuration**  
   Open `config/db.php` and configure:
   - **Database connection**: `DB_HOST`, `DB_USER`, `DB_PASS`.
   - **App URL**: Set `APP_URL` to match your local deployment (e.g., `http://localhost/TNSTC`).
   - **SMTP Mail Credentials**: Enter valid host, username, and password for mail features.
   - **Google Maps API**: Enter your Maps API Key for tracking and live mapping widgets.

4. **Verify Folders**  
   Ensure that the target directory has write permissions for ticket generation and pass document uploads (e.g. proof documents storage directories).



## 🔑 Default Credentials (for testing)

All default test accounts have preset passwords. Use them to access their respective panels:

| Role | Email | Password |
| :--- | :--- | :--- |
| **System Admin** | `admin@tnstc.tn.gov.in` | `Admin@123` |
| **District Minister** | `minister@tnstc.tn.gov.in` | `Minister@123` |
| **Depot Manager** | `manager1@tnstc.tn.gov.in` (Thamirabarani Depot) | `Manager@123` |
| **Driver / Conductor** | `driver1@tnstc.tn.gov.in` | `Driver@123` |
| **Conductor** | `conductor1@tnstc.tn.gov.in` | `Driver@123` |
| **Passenger** | `arun@gmail.com` | `Pass@123` |



*Developed for the Department of Transport, Government of Tamil Nadu.*
=======
🚌 TNSTC Smart Bus Management System (Tirunelveli District)
An enterprise-grade, role-based web application designed for the Tamil Nadu State Transport Corporation (TNSTC) – Tirunelveli District. It provides a comprehensive digital platform connecting passengers, drivers/conductors, depot managers, the administration, and state governance (district minister).

🚀 Project Overview
The TNSTC Smart Bus System streamlines transport logistics, passenger ticketing, pass verification, emergency management, and real-time operations for the 7 key depots in Tirunelveli District:

Thamirabarani Depot (Vannarpettai)
Bye-Pass Depot (Vannarpettai)
Kattabomman Nagar Depot (KTC Nagar)
Cheranmahadevi Depot (Cheranmahadevi)
Valliyoor Depot (Valliyoor)
Thisayanvilai Depot (Thisayanvilai)
Papanasam Depot (Papanasam)

🛠️ Technology Stack
Backend: Native PHP 8.x (using PDO for secure, prepared SQL statements)
Frontend: HTML5, CSS3 (Custom tnstc.css Design System), JavaScript (ES6+), Bootstrap 5, Font Awesome 6
Database: MySQL (InnoDB engine with strict foreign key constraints)
Integrations: Google Maps API (Live Tracking), Google Charts / Charts.js (Analytics), QR Code API (Ticket & Pass validation)

👥 User Roles & Features
1. 🧑‍💼 Passenger Portal
Secure Authentication: Registration and Login with OTP email verification (auth/otp_verify.php) and password recovery.
Search Bus: Search bus schedules and route stops with real-time seat availability (passenger/search_bus.php).
Ticket Booking: Interactive seat selector, online payment simulation, and instant PDF/QR code ticket generation (passenger/book_ticket.php).
Live Tracking: Locate buses on interactive Google Maps (passenger/live_tracking.php).
Smart Bus Pass: Apply for Monthly or Student passes with proof document uploads (passenger/bus_pass.php).
Complaints & Grievances: Lodge complaints (categorized by delay, staff behavior, cleanliness, etc.) and track resolution status (passenger/complaints.php).
Lost & Found: Report lost belongings or claim items recovered in buses (passenger/lost_found.php).
AI Chatbot: Intelligent virtual assistant to answer queries on routes, timings, and fares (passenger/chatbot.php).

2. 🚌 Driver & Conductor Panel
Assigned Trips: View duty rosters and scheduled trips (driver/assigned_trips.php).
QR Ticket Validator: Scan passenger tickets using mobile camera and match via API (driver/scan_ticket.php).
Real-time Delay Updates: Post immediate delay details and reason codes directly to the passenger app (driver/delay_update.php).
SOS Emergency System: Trigger critical safety alarms detailing location coordinates to depot control (driver/emergency.php).
Passenger Manifest: Access the manifest lists of ticket holders for the current run (driver/passenger_list.php).

3. 🏢 Depot Manager Dashboard
Fleet Management: Audit, register, and update status of depot buses (depot_manager/buses.php).
Roster & Scheduling: Manage schedule assignments for routes, drivers, and conductors (depot_manager/schedules.php).
Bus Pass Verification: Review submitted student/monthly documents and approve/reject passes (depot_manager/pass_verification.php).
Staff Control: Track leave and status for drivers and conductors (depot_manager/staff.php).
Grievance Redressal: Read complaints and submit official resolution replies (depot_manager/complaints.php).
Analytics & Reports: Visual charts depicting passenger volume, revenue, and delays (depot_manager/reports.php).

4. 🏛️ TNSTC Minister Panel
Executive Analytics: Global dashboard summarizing district-wide operations (minister/dashboard.php).
Depot Performance: Comparative analysis of efficiency, bookings, and revenue metrics (minister/depot_performance.php).
Emergency Reports: Review active and resolved safety alerts (minister/emergency_report.php).
Delay Reports: Track overall punctuality rates and depot delay factors (minister/delay_report.php).
Complaint Analytics: Track categories of grievances to identify areas for systemic improvement (minister/complaint_analytics.php).

5. ⚙️ System Administrator Dashboard
User Control: Complete access control over user permissions, role elevation, and activation status (admin/manage_users.php).
Depot & Route Registries: Manage primary depot coordinates, routes, and individual route stops (admin/manage_depots.php, admin/manage_routes.php).
Audit Logs & Master Management: Manage master copies of tickets, schedules, passes, complaints, and recovered items system-wide.

🗄️ Database Schema Outline
The application runs on tnstc_tirunelveli database schema. Key tables include:

users: Stores passenger, driver, conductor, manager, minister, and admin credentials.
depots: Office names, locations, manager foreign keys, and map coordinates.
buses: Registered vehicles, categories (Ordinary, Express, AC, etc.), seat capacity, and status.
routes & route_stops: Defines routes, intermediate stops, distance, and sequence.
schedules: Operational runs mapped to a bus, route, driver, conductor, time, date, and delay offsets.
tickets: Paid tickets storing bookings, passenger details, seat numbers, fare, and verification QR links.
bus_pass: Applications and active bus passes with validity range and document paths.
complaints & lost_found: Grievance records and items ledger.
live_tracking & emergency_alerts: Live coordinates of running buses and SOS distress signals.

⚙️ Installation & Setup
Requirements
Server Environment: Apache (XAMPP / WAMP / MAMP or standalone PHP/Apache setup)
PHP Version: 8.0 or higher
Database: MySQL 5.7+ or MariaDB 10.4+
SMTP Credentials: Required for mail notifications and OTP verification.

Step-by-Step Installation
Clone the Repository
Place the project files into your web root directory (e.g., C:\xampp\htdocs\TNSTC).

Database Setup

Start MySQL on your local server.
Open phpMyAdmin or your MySQL client.
Create a database named tnstc_tirunelveli:
sql

CREATE DATABASE tnstc_tirunelveli CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
Import the database.sql file:
bash

mysql -u root -p tnstc_tirunelveli < database.sql

Application Configuration
Open config/db.php and configure:

Database connection: DB_HOST, DB_USER, DB_PASS.
App URL: Set APP_URL to match your local deployment (e.g., http://localhost/TNSTC).
SMTP Mail Credentials: Enter valid host, username, and password for mail features.
Google Maps API: Enter your Maps API Key for tracking and live mapping widgets.

Verify Folders
Ensure that the target directory has write permissions for ticket generation and pass document uploads (e.g. proof documents storage directories).

🔑 Default Credentials (for testing)
All default test accounts have preset passwords. Use them to access their respective panels:

Role	Email	Password
System Admin	admin@tnstc.tn.gov.in	Admin@123
District Minister	minister@tnstc.tn.gov.in	Minister@123
Depot Manager	manager1@tnstc.tn.gov.in (Thamirabarani Depot)	Manager@123
Driver / Conductor	driver1@tnstc.tn.gov.in	Driver@123
Conductor	conductor1@tnstc.tn.gov.in	Driver@123
Passenger	arun@gmail.com	Pass@123
Developed for the Department of Transport, Government of Tamil Nadu.s
>>bda5fa890c9652c34d16ffd450bb629422e1f276
