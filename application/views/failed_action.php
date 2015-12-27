<!DOCTYPE html>
<html>
  <head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
	<meta charset="utf-8">
	<title>Failed to complete your action</title>
  </head>
  <body>
	  <h1>Failed to complete your action</h1>
      <p><?php echo $validation_errors; ?></p>
      <a href="<?=base_url();?>">Return to Game</a> 
  </body>
</html>