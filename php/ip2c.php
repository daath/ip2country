<?php

/*****************************************************************************
//
// IP2Country module - Disk access version
//
// Copyright ( C ) 2004  L. Petersen, Weird Silence, www.weirdsilence.net
// 
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or ( at your option ) any later version.
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

    define( "IP2C_IP_NOT_FOUND", -1 );
    define( "IP2C_NOT_IP2C_DATABASE", -2 );
    define( "IP2C_DATABASE_OPEN_ERROR", -3 );
    define( "IP2C_CALL_INIT_FIRST", -4 );

    class ip2country
    {
        var $fp = Null;
        var $records = 0;
        var $min = Null;
        var $max = Null;
        var $recsize = 0;
        var $countries = 0;
        var $countryname = array( );
        var $idx = 0;
        var $minip = 0;
        var $maxip = 0;
        var $topidx = array( );
        var $has_init = 0;

        function init( $filename )
        {
            if ( $this->fp = fopen( $filename, 'rb' ) )
            {
                if ( $this->has_init != 1 )
                {
                    $tmp = fread( $this->fp, 4 );
                    if ( $tmp != 'ip2c' )
                    {
                        return IP2C_NOT_IP2C_DATABASE;
                    }
                    $this->idx = ip2country::readlong( $this->fp );

                    fseek( $this->fp, $this->idx );
                    $this->records = sprintf( '%u' ,ip2country::readlong( $this->fp ) );
                    $this->min =  sprintf( '%u', ip2country::readlong( $this->fp ) );
                    $this->max =  sprintf( '%u', ip2country::readlong( $this->fp ) );
                    $this->recsize = ip2country::readbyte( $this->fp );
                    $this->countries = ip2country::readshort( $this->fp );

                    $tmp = fread( $this->fp, ( $this->countries * 2 ) );
                    for ( $i = 0; $i < $this->countries; $i++ )
                    {
                        $this->countryname[] = substr( $tmp, ( $i * 2 ), 2 );
                    }

                    $this->minip = ip2country::readbyte( $this->fp );
                    $this->maxip = ip2country::readbyte( $this->fp );
                    for ( $i = 0; $i < 256; $i++ )
                    {
                        $this->topidx[ $i ] = -1;
                    }
                    for ( $i = $this->minip; $i <= $this->maxip; $i++ )
                    {
                        $this->topidx[ $i ] = ip2country::readlong( $this->fp );
                    }
                    $this->has_init = 1;
                }
            }
            else
            {
                return IP2C_DATABASE_OPEN_ERROR;
            }
        }
    
        function readlong( $fp )
        {
            $tmp = fread( $fp, 4 );
            return ord( $tmp[ 3 ] ) << 24 | ord( $tmp[ 2 ] ) << 16 | ord( $tmp[ 1 ] ) << 8 | ord( $tmp[ 0 ] );
        }

        function readshort( $fp )
        {
            $tmp = fread( $fp, 2 );
            return ord( $tmp[ 1 ] ) << 8 | ord( $tmp[ 0 ] );
        }
        
        function readbyte( $fp )
        {
            $tmp = fread( $fp, 1 );
            return ord( $tmp[ 0 ] );
        }

        function lookup( $ip )
        {
            if ( $this->has_init != 1 )
            {
                return IP2C_CALL_INIT_FIRST;
            }
            if ( $this->fp )
            {
                $list = split( '\.', $ip );
                $aclass = $list[ 0 ];
                $orgip = $ip;
                $ip = sprintf( '%u', ip2long( $ip ) );

                if ( $ip < $this->min || $ip > $this->max || $this->topidx[ $aclass ] < 0 )
                {
                    return IP2C_IP_NOT_FOUND;
                }

                if ( $aclass == $this->maxip )
                {
                    $top = $this->records;
                    $bottom = abs( $this->topidx[ $aclass ] ) - 1;
                }
                else
                {
                    $bottom = abs( $this->topidx[ $aclass ] ) - 1;
                    $i = 1;
                    while ( $this->topidx[ $aclass + $i ] < 0 )
                    {
                        $i++;
                    }
                    $top = $this->topidx[ $aclass + $i ];
                }
                if ( $aclass == $this->minip )
                {
                    $bottom = 0;
                }

                $found = false;
                $oldtop = -1;
                $oldbot = -1;
                $nextrecord = floor( ( $top + $bottom ) / 2 );

                if ( $ip == $this->min )
                {
                    fseek( $fp, 16 );
                    return ip2country::readshort( $this->fp );
                }
                elseif ( $ip == $this->max )
                {
                    fseek( $this->fp ,( ( $this->records * $this->recsize ) - $this->recsize + 16 ) );
                    return ip2country::readshort( $this->fp );
                }

                $cnt = 0;
                while ( !$found )
                {
                    $cnt++;
                    fseek( $this->fp, ( ( $nextrecord * $this->recsize ) + 8 ) );
                    $start = sprintf( '%u', ip2country::readlong( $this->fp ) );
                    if ( $ip < $start )
                    {
                        $top = $nextrecord;
                    }
                    else
                    {
                        $end = sprintf( '%u', ip2country::readlong( $this->fp ) );
                        if ( $ip > $end )
                        {
                            $bottom = $nextrecord;
                        }
                        else
                        {
                            return ip2country::readshort( $this->fp );
                        }

                    }
                    $nextrecord = floor( ( $top + $bottom ) / 2 );
                    if ( $top == $oldtop && $bottom == $oldbot )
                    {
                        return IP2C_IP_NOT_FOUND;
                    }
                    $oldtop = $top;
                    $oldbot = $bottom;
                }
            }
        }

        function idx2country( $index )
        {
            $res = "--";
            if ( $index >= 0 || $index < $this->countries )
            {
                $res = $this->countryname[ $index ];
            }
            return $res;
        }

        function destroy()
        {
            if ( $this->fp )
            {
                fclose( $this->fp );
                $this->has_init = 0;
            }
        }
    }

?>
