<?php
// Set coord input
$grid_coord = $_GET['grid_coord'];

// Get data
$json = json_decode(file_get_contents("data.json"), true);

// Get land data from data
$grid_square = isset($json[$grid_coord]) ? $json[$grid_coord] : false;

// echo data to client
if ( isset($grid_square['content']) )
{
    echo $grid_square['content'];
    echo '|';
    echo $grid_square['stroke'];
    echo '|';
    echo $grid_square['fill'];
}
else
{
    echo 'This land is unclaimed';
    echo '|';
    echo '#222222';
    echo '|';
    echo '#FF00FF';
}