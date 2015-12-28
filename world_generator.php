<?php

// Just set parameters, run, copy, and replace last comma with semicolon

// 
// Parameters
// 

// World Key
$world_key = 1;

// Size of land box squares
// Recommend between 1 and 4
$box_size = 3;

// Area covered defined with these limits
// x of 180 and y of 84 covers the globe
// x and y limits must be evenly divisible by $box_size
// Box size 2 with x limit of 180 and y limit of 84 creates 60480 land squares
// Box size 3 with x limit of 180 and y limit of 84 creates 6697 land squares
$x_limit = 180;
$y_limit = 84;

// 
// Logic
// 

$insert_statement = "INSERT INTO `land` 
(`id`, `coord_key`, `lat`, `lng`, `world_key`, `claimed`, `user_key`, `land_name`, `price`, `content`, `primary_color`, `secondary_color`, `created`, `modified`) 
VALUES";

echo $insert_statement;
echo '<br>';

function round_down($n, $box_size) {
    if ($n > 0) {
        return floor($n/$box_size) * $box_size;
    }
    else if ($n < 0) {
        return ceil($n/$box_size) * $box_size;
    }
    else {
        return 0;
    }
}

// 
// Land loop
// 

// Loop lng
$i = 1;
for ($x = -$x_limit; $x < $x_limit; $x = $x + $box_size) {
    // Loop lat for each lng
    for ($y = -$y_limit; $y < $y_limit; $y = $y + $box_size) {
        // Get coord_key
        $coord_key = round_down($y, $box_size) . ',' . round_down($x, $box_size);
        echo "(NULL, '" . $coord_key . "', '" . $y . "', '" . $x . "', " . $world_key . ", '0', '0', '', 0, '', 'FF00FF', '222222', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
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