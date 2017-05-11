<h1>LandGrab</h1>

A Google Maps game where you fight for control of the earth. Join the game at <a href="http://landgrab.xyz/">Landgrab.xyz</a> or join the community at <a href="https://www.reddit.com/r/LandGrab/">/r/LandGrab</a>.

<br>

To set up a local instance

<ul>
    <li>Place files in www/landgrab folder</li>
    <li>Run sql/landgrab.sql, sql/modify_effects.sql, and the files in sql/worlds as sql commands in database</li>
    <li>Configure database connection in config/database.php</li>
    <li>If using a specific domain, configure is_dev() in config/autoload.php and $config['base_url'] in config/config.php</li>
    <li>You may want to disable HTTPS redirect in config/autoload.php</li>
    <li>Set a cron for * * * * * php -f /path/to/application/cron.php</li>
    <li>Alternatively, hit up localhost/landgrab/cron/1234 to trigger a cron (Change the token under config/constants.php and cron.php for production)</li>
</ul>