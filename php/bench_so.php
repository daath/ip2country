<?php

    set_time_limit(0);

    dl('ip2c.so');

    if ( ip2c_init( "ip2cntry.dat" ) != 0 )
    {
        echo "Couldn't init...<br>";
        exit;
    }

    function getmicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    $list = file('Servers.lst');

    $n = count($list);
    $start = getmicrotime();
    for ($i = 0;$i < $n;$i++)
    {
        $ip = trim( $list[ $i ] );
        $code = ip2c_countrycode( ip2c_lookup( $ip ) );
    }
    $time = getmicrotime() - $start;
    $lps = sprintf('%.2f',($n / $time));
    $time = sprintf('%.2f',$time);

    ip2c_destroy();

    echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n"

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
    <head>
        <title>IP 2 Country</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <style type="text/css">
            body {
                font-family: verdana, arial, helvetica, sans-serif;
                font-size: 12px;
                background-color: white;
                color: black;
            }
        </style>
        <script type="text/javascript">
            function init()
            {
                ip = document.getElementById('ip');
                ip.focus();
            }
        </script>
    </head>
    <body onload="init()">
        <h1>IP 2 Country Benchmark (PHP module)</h1>
        <div>
<?php

    echo "$n servers<br />$time seconds runtime<br />$lps lookups/sec";

?>
        </div>
        <div style="width: 10px; height: 50px"></div>
        <div><small>Based on <a href="http://ip-to-country.webhosting.info/">IP-To-Country</a></small></div>
        <div><small><a href="http://validator.w3.org/check/referer">XML</a> | <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a></small></div>
    </body>
</html>
