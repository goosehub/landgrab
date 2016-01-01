<?php

// Just set parameters, run, copy, and replace last comma with semicolon

// 
// Parameters
// 

// World Key
$world_key = 5;

// World Slug
$world_slug = 'foo';

$world_tax_rate = '0.01';

// Size of land box squares
// Recommend 2, 3, 4, 6, or 12
$box_size = 2;

// Area covered defined with these limits
// x and y limits must be evenly divisible by $box_size
// x of 180 and y of 84 covers the globe and is divisible by 1, 2, 3, 4, 6, and 12
// Box size 2 with x limit of 180 and y limit of 84 creates 60480 land squares
// Box size 3 with x limit of 180 and y limit of 84 creates 6697 land squares
$x_limit = 180;
$y_limit = 84;

$result = '';

// 
// World Logic
// 

$world_insert_statement = "INSERT INTO `world` 
(`id`, `slug`, `land_size`, `land_tax_rate`, `latest_rebate`, `created`, `modified`) 
VALUES 
(NULL, '" . $world_slug . "', '" . $box_size . "', '" . $world_tax_rate . "', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);";

$result .= $world_insert_statement;

// 
// Land Logic
// 

$land_insert_statement = "INSERT INTO `land` 
(`id`, `coord_slug`, `lat`, `lng`, `world_key`, `claimed`, `account_key`, `land_name`, `price`, `content`, `primary_color`, `secondary_color`, `created`, `modified`) 
VALUES";

$result .= $land_insert_statement;
$result .= '<br>';

// 
// Land loop
// 

// Loop lng
$i = 1;
for ($x = -$x_limit; $x < $x_limit; $x = $x + $box_size) {
    // Loop lat for each lng
    for ($y = -$y_limit; $y < $y_limit; $y = $y + $box_size) {
        // Get coord_slug
        $coord_slug = $y . ',' . $x;
        $result .= "(NULL, '" . $coord_slug . "', '" . $y . "', '" . $x . "', " . $world_key . ", '0', '0', '', 0, '', '#000000', '#000000', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
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