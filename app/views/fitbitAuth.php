<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel PHP Framework</title>
</head>
<body>

<?php
    $key = Config::get('live.fitbit-key');
    $secret = Config::get('live.fitbit-secret');

    $fitbit = new FitBitPHP($key, $secret);

   // $fitbit->initSession('http://live.dev/hello');
    
    $fitbit->setOAuthDetails('aca45b45b6a056d52f70e30fc7d59794', '635aa6d3fd4e9aa54e6e2bee68ab17c0');
    // echo 'T:'.$fitbit->getOAuthToken();
    // echo 'S:'.$fitbit->getOAuthSecret();
    $fitbit->setUser('25WCYP');

    // 274R55 frisket
    // 274HFM ivan
    $xml = $fitbit->getTimeSeries('steps', 'today', 'max');
    print_r($xml);
?>

</body>
</html>
