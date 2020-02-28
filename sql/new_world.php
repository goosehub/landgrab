<?php

// This is for creating new worlds
// If you just want an instance of tilegrab, use existing worlds in worlds folder
// Just set parameters, run, copy, and replace last comma with semicolon

// 
// Parameters
// 

// Size of tile box squares
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
// Box size 2 with lng limit of 180 and lat limit of 84 creates 15000+ tile squares
// Box size 3 with lng limit of 180 and lat limit of 84 creates 6697 tile squares
$lat_limit = 84;
$lng_limit = 180;

$result = '';

// 
// World Logic
// 

$world_insert_statement = "INSERT INTO `world` (`id`, `slug`, `tile_size`) 
VALUES
(NULL, '" . $world_slug . "', '" . $box_size . "');";

$result .= $world_insert_statement;
$result .= '<br>';

// 
// tile Logic
// 

$tile_insert_statement = "INSERT INTO `tile` 
(`id`, `lat`, `lng`, `world_key`, `is_capitol`, `modified`)
VALUES";

$result .= $tile_insert_statement;
$result .= '<br>';

// 
// tile loop
// 

// Loop lng
$i = 1;
for ($lng = -$lng_limit; $lng <= $lng_limit; $lng = $lng + $box_size) {
    // Loop lat for each lng
    for ($lat = -$lat_limit; $lat < $lat_limit; $lat = $lat + $box_size) {
        // This prevents bug where -180 and 180 overlap, creating tile the UI can't access
        if ($lng != '-180') {
            // Get coord_slug
            $result .= "(NULL, '" . $lat . "', '" . $lng . "', " . $world_key . ", '0', CURRENT_TIMESTAMP)";
            if ($i % 1000 == 0)
            {
                $result .= ';';
                $result .= '<br>';
                $result .= $tile_insert_statement;
                $result .= '<br>';
            } else {
                $result .= ',';
            }
            $result .= '<br>';
            $i++;
        }
    }
}

// Replace last comma with semi colon
$search = ',';
$replace = ';';
$result = strrev(implode(strrev($replace), explode($search, strrev($result), 2)));

echo $result;