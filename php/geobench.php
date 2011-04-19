<?php

/*****************************************************************************
//
// GeoIP Free Benchmarker - You need GeoIP.inc from GeoIP
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

    require_once('geoip.inc');

    function getmicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    $gi = geoip_open("GeoIP.dat",GEOIP_STANDARD

    $list = file('Servers.lst');

    $n = count($list);
    $start = getmicrotime();
    for ($i = 0;$i < $n;$i++)
    {
        $code = geoip_country_code_by_addr($gi, $list[$i]);
    }
    $time = getmicrotime() - $start;
    geoip_close($gi);
    $lps = sprintf('%.2f',($n / $time));
    $time = sprintf('%.2f',$time);

    echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n"

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
    <head>
        <title>GeoIP 2 Country Benchmark</title>
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
        <h1>GeoIP 2 Country Benchmark</h1>
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
