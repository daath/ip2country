<?php

/*****************************************************************************
//
// IP2Country benchmark for shared memory version
//
// Copyright (C) 2004  L. Petersen, Weird Silence, www.weirdsilence.net
// 
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
//
*****************************************************************************/

    set_time_limit(0);

    require_once('ip2c_shm.php');

    function getmicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    $ip2c = new ip2countryShm();
    $ip2c->init( "ip2cntry.dat" );

    $list = file('Servers.lst');

    $n = count($list);
    $start = getmicrotime();
    for ($i = 0;$i < $n;$i++)
    {
        $code = $ip2c->lookup($list[$i]);
    }
    $time = getmicrotime() - $start;
    $ip2c->destroy();
    $lps = sprintf('%.2f',($n / $time));
    $time = sprintf('%.2f',$time);

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
        <h1>IP 2 Country Benchmark (Shared Memory)</h1>
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
