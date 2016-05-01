<?php

// Just set parameters, run, copy, and replace last comma with semicolon

// 
// Parameters
// 

// Size of land box squares
// Recommend 2, 3, 4, 6, or 12
$box_size = 2;

// World Key
$world_key = 1; // Ensure this is the next available key
$world_slug = 'huge';

// 
// Static
// 

// Area covered defined with these limits
// lng and lat limits must be evenly divisible by $box_size
// lng of 180 and lat of 84 covers the globe and is divisible by 1, 2, 3, 4, 6, and 12
// Box size 2 with lng limit of 180 and lat limit of 84 creates 15000+ land squares
// Box size 3 with lng limit of 180 and lat limit of 84 creates 6697 land squares
$lat_limit = 84;
$lng_limit = 180;

$result = '';

// 
// World Logic
// 

$world_insert_statement = "INSERT INTO `world` (`id`, `slug`, `land_size`, `created`, `modified`) 
VALUES
(NULL, '" . $world_slug . "', '" . $box_size . "', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);";

$result .= $world_insert_statement;
$result .= '<br>';

// 
// Land Logic
// 

$land_insert_statement = "INSERT INTO `land` 
(`id`, `coord_slug`, `lat`, `lng`, `world_key`, `claimed`, `account_key`, `land_name`, `content`, `type`, `color`, `created`, `modified`)
VALUES";

$result .= $land_insert_statement;
$result .= '<br>';

// 
// Land loop
// 

// Loop lng
$i = 1;
for ($lng = -$lng_limit; $lng <= $lng_limit; $lng = $lng + $box_size) {
    // Loop lat for each lng
    for ($lat = -$lat_limit; $lat < $lat_limit; $lat = $lat + $box_size) {
        // Get coord_slug
        $coord_slug = $lat . ',' . $lng;
        $result .= "(NULL, '" . $coord_slug . "', '" . $lat . "', '" . $lng . "', " . $world_key . ", '0', '0', '', '', '', '#000000', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        if ($i % 1000 == 0)
        {
            $result .= ';';
            $result .= '<br>';
            $result .= $land_insert_statement;
            $result .= '<br>';
        } else {
            $result .= ',';
        }
        $result .= '<br>';
        $i++;
    }
}

// Replace last comma with semi colon
$search = ',';
$replace = ';';
$result = strrev(implode(strrev($replace), explode($search, strrev($result), 2)));

echo $result;