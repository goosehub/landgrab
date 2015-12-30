<?php

// Just set parameters, run, copy, and replace last comma with semicolon

// 
// Parameters
// 

// World Key
$world_key = 2;

// Size of land box squares
// Recommend between 1 and 4
$box_size = 3;

// Area covered defined with these limits
// x and y limits must be evenly divisible by $box_size
// x of 180 and y of 84 covers the globe and is divisible by 1, 2, 3, 4, 6, and 12
// Box size 2 with x limit of 180 and y limit of 84 creates 60480 land squares
// Box size 3 with x limit of 180 and y limit of 84 creates 6697 land squares
$x_limit = 180;
$y_limit = 84;

// 
// Logic
// 

$insert_statement = "INSERT INTO `land` 
(`id`, `coord_key`, `lat`, `lng`, `world_key`, `claimed`, `account_key`, `land_name`, `price`, `content`, `primary_color`, `secondary_color`, `created`, `modified`) 
VALUES";

echo $insert_statement;
echo '<br>';

// 
// Land loop
// 

// Loop lng
$i = 1;
for ($x = -$x_limit; $x < $x_limit; $x = $x + $box_size) {
    // Loop lat for each lng
    for ($y = -$y_limit; $y < $y_limit; $y = $y + $box_size) {
        // Get coord_key
        $coord_key = $y . ',' . $x;
        echo "(NULL, '" . $coord_key . "', '" . $y . "', '" . $x . "', " . $world_key . ", '0', '0', '', 0, '', '#000000', '#000000', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        if ($i % 1000 == 0)
        {
            echo ';';
            echo '<br>';
            echo $insert_statement;
            echo '<br>';
        } else {
            echo ',';
        }
        echo '<br>';
        $i++;
    }
}