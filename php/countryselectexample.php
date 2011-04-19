<?php

    require_once("isocountryarray.php");
    require_once("ip2c.php");

    $ip2c = new ip2country();
    $userIdx = $ip2c->lookup( $_SERVER['REMOTE_ADDR'] );
    $userCode = $ip2c->idx2country( $userIdx );
    $ip2c->destroy();

    $options = '';
    foreach ( $isoCountry as $code => $country )
    {
        $options .= "<option ";
        if ( $userCode == $code )
        {
            $options .= "selected='selected' ";
        }
        $options .= "value='$code'> $country </option>";
    }

?>
<html>
    <head>
        <title>Country Select Box Example</title>
    </head>
    <body>
        <form>
            Please select your country: 
            <select name="country">
                <?= $options ?>
            </select>
        </form>
    </body>
</html>
