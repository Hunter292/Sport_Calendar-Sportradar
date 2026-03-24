# Sport_Calendar-Sportradar
A web app for tracking sport events for  Sportradar Coding Academy 2026
The app has 3 main features: 
-	Viewing upcoming and past events with ability to filter and sort by date, venue, sport, city, country and team. Events are listed in a table. A more detailed view of a single event is available after clicking   a link.
-	Adding new events thru a form. 
-	View of past events with ability to add who won the event/match.
Adding new events and winners is protected with login requirement(prefilled for testing).

Assumptions taken: teams have unique names and there aren’t 2 venues with the same name in one city. Matches happen as part of competitions, name “sparing” is used for independent events.
Technology: PHP, JavaScript and MySQL database.
Setup: Requires PHP interpreter, MYSQL database and a web server. Connecting to the database is controlled by “connect.php” which is currently configured for a local server(XAMPP) with default user and password. 
