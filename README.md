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
