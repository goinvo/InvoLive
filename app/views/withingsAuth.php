<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel PHP Framework</title>
</head>
<body>

<?php
    $key = Config::get('live.withings-key');
    $secret = Config::get('live.withings-secret');

    $fitbit = new WithingsPHP($key, $secret);
   	$fitbit->initSession('http://live.dev/hello2');
   	$fitbit->getProfile();

?>

</body>
</html>
