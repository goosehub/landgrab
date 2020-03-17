<h1>LandGrab</h1>

A Game of Economies, Diplomacy, Warfare on planet earth using Google Maps. Join the game at <a href="http://landgrab.xyz/">Landgrab.xyz</a> or join the community at <a href="https://www.reddit.com/r/LandGrab/">/r/LandGrab</a>.

<br>

To set up a local instance

<ul>
    <li>Place files in webroot with proper permissions</li>
    <li>Configure database connection and token in auth.php</li>
    <li>Run sql/landgrab.sql, and sql/world.sql as sql commands in database</li>
    <li>You may need to add your domain under base_url in config/config.php</li>
    <li>You may need to disable HTTPS redirect in config/autoload.php</li>
    <li>Set a cron for <code>* * * * * php -f /ABSOLUTE_PATH_HERE/crons/CRON_NAME_HERE.php</code> for every cron in the crons folder</li>
    <li>Alternatively, use your browser to send a request to localhost/landgrab/cron/CRON_NAME_HERE/TOKEN_HERE to trigger a cron</li>
</ul>

<p>
	Note: api_response and api_error_response both echo and exit so no return needed.
</p>