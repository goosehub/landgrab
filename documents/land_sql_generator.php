<?php

// Size of land box squares
$box_size = 2;
// Area covered defined with these limits
// Must be evenly divisible by $box_size
$x_limit = 180;
$y_limit = 84;
// Box size 2 with x limit of 180 and y limit of 84 creates 60480 land squares and covers the globe

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
for ($x = -$x_limit; $x < $x_limit; $x = $x + $box_size) {
    // Loop lat for each lng
    for ($y = -$y_limit; $y < $y_limit; $y = $y + $box_size) {
        // Get coord_key
        $coord_key = round_down($y, $box_size) . '|' . round_down($x, $box_size);
        echo "(NULL, '" . $coord_key . "', '" . $y . "', '" . $x . "', '0', '0', 'Unclaimed', '', 'FF00FF', '222222', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),";
        echo '<br>';

    }
}