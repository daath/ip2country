<?php

/*****************************************************************************
//
// IP2Country demo web app for IP2Country module
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

    session_start();

    require_once('ip2c.php');
    require_once('isocountryarray.php');

    function getmicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    $ip2c = new ip2country();
    $start = getmicrotime();
    $ip2c->init( "ip2cntry.dat" );
    $end = getmicrotime();
    $inittime = sprintf('%d',(($end - $start) * 1000));

    if (!isset($_SESSION['user']))
    {
        $cidx = $ip2c->lookup($_SERVER['REMOTE_ADDR']);
        if ($user == -1)
        {
            $user = 'nowhere?';
        }
        else
        {
            $code = $ip2c->idx2country( $cidx );
            $cn = $isoCountry[ $code ];
            $user = $cn . " <img src='/world.small/" . strtolower($code) . ".png' width='18px' height='12px' alt='Flag for " . $cn . "' title='Flag for " . $cn . "' />";
        }
        $_SESSION['user'] = $user;
    }
    else
    {
        $user = $_SESSION['user'];
    }

    $show = 0;

    if (isset($_GET['ip']) && ip2long(trim($_GET['ip'])) != -1)
    {
        $start = getmicrotime();
        $countryIndex = $ip2c->lookup(trim($_GET['ip']));
        $end = getmicrotime();
        $time = sprintf('%d',(($end - $start) * 1000));
        if ($countryIndex == -1)
        {
            $result = "IP is not in database!<br />Query took $time ms";
        }
        else
        {
            $country = $ip2c->idx2country( $countryIndex);
            $cn = $isoCountry[ $country ];
            $result = "$_GET[ip] is found in " . $cn . " <img src='/world.small/" . strtolower($country) . ".png' width='18px' height='12px' alt='Flag for " . $cn . "' title='Flag for " . $cn . "' /><br />Query took $time ms";
        }
        $result .= "<br />Init time was $inittime ms";
        $show = 1;
    }

    $ip2c->destroy();

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
        <h1>IP 2 Country</h1>
        <div>Welcome! You seem to be from <?= $user ?>!<br /><br /></div>
        <form action="/" id="ip2c" method="get">
            <div>
                Look up IP:
                <input type="text" id="ip" name="ip" size="30" maxlength="16" />
                <input type="submit" value="Look up" />
            </div>
        </form>
<?php

    if ($show)
    {
        echo "<div><b>$result</b></div>";
    }

?>
        <div style="width: 10px; height: 50px"></div>
        <div><small>Based on <a href="http://ip-to-country.webhosting.info/">IP-To-Country</a></small></div>
        <div><small><a href="http://validator.w3.org/check/referer">XML</a> | <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a></small></div>
    </body>
</html>
