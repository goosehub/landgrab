<h1>LandGrab</h1>

A game in development
<br>
To set up a local instance

<ul>
    <li>Place files in www/landgrab folder</li>
    <li>Run sql/landgrab.sql and sql/land.sql in database</li>
    <li>Configure database connection in config/database.php</li>
    <li>Set a cron for * * * * * php -f /path/to/application/cron.php</li>
    <li>Alternatively, hit up localhost/landgrab/cron/1234 to trigger a cron (Change the token under config/constants.php for production)</li>
</ul>