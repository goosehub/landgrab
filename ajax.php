<?php
// Set coord input
$request = $_GET['request'];

// Get data
$json = json_decode(file_get_contents("data.json"), true);

// Request for some data on all grids
if ($request === 'all')
{
    return;
}
// Request for all data on one grid
else
{
    // Find grid
    $grid_square = isset($json[$request]) ? $json[$request] : false;

    // echo data to client
    if ( isset($grid_square['content']) )
    {
        echo $grid_square['content'];
        echo '|';
        echo $grid_square['owner'];
    }
    // If unfound, default to this
    else
    {
        echo 'This land is unclaimed';
        echo '|';
        echo 'Unowned';
    }
}