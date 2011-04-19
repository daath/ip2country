<?php

/*****************************************************************************
//
// IP2Country module - Memory cached version
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

    class ip2countryMem
    {
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
        var $_data = 0;
        var $has_init = 0;

        function init( $filename )
        {
            if ( $fp = fopen( $filename, 'rb' ) )
            {
                if ($this->has_init != 1)
                {
                    $tmp = fread( $fp, 4 );
                    if ( $tmp != 'ip2c' )
                    {
                        return IP2C_NOT_IP2C_DATABASE;
                    }
                    $this->idx = ip2countryMem::readlong( $fp );

                    fseek( $fp, $this->idx );
                    $this->records = sprintf( '%u' ,ip2countryMem::readlong( $fp ) );
                    $this->min =  sprintf( '%u', ip2countryMem::readlong( $fp ) );
                    $this->max =  sprintf( '%u', ip2countryMem::readlong( $fp ) );
                    $this->recsize = ip2countryMem::readbyte( $fp );
                    $this->countries = ip2countryMem::readshort( $fp );

                    $tmp = fread( $fp, ( $this->countries * 2 ) );
                    for ( $i = 0; $i < $this->countries; $i++ )
                    {
                        $this->countryname[] = substr( $tmp, ( $i * 2 ), 2 );
                    }

                    $this->minip = ip2countryMem::readbyte( $fp );
                    $this->maxip = ip2countryMem::readbyte( $fp );
                    for ( $i = 0; $i < 256; $i++ )
                    {
                        $this->topidx[ $i ] = -1;
                    }
                    for ( $i = $this->minip; $i <= $this->maxip; $i++ )
                    {
                        $this->topidx[ $i ] = ip2countryMem::readlong( $fp );
                    }

                    fseek( $fp, 8 );
                    $len = $this->records * $this->recsize;
                    $this->_data = fread( $fp,$len );
                    fclose( $fp );
                    $this->has_init = 1;
                }
            }
            else
            {
                return IP2C_DATABASE_OPEN_ERROR;
            }
        }
    
        function str2long( $tmp )
        {
            return ord( $tmp[ 3 ] ) << 24 | ord( $tmp[ 2 ] ) << 16 | ord( $tmp[ 1 ] ) << 8 | ord( $tmp[ 0 ] );
        }

        function readlong( $fp )
        {
            $tmp = fread( $fp, 4 );
            return ord( $tmp[ 3 ] ) << 24 | ord( $tmp[ 2 ] ) << 16 | ord( $tmp[ 1 ] ) << 8 | ord( $tmp[ 0 ] );
        }

        function str2short( $tmp )
        {
            return ord( $tmp[ 1 ] ) << 8 | ord( $tmp[ 0 ] );
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
            if ( $this->_data )
            {
                $list = split( '\.', $ip );
                $aclass = $list[ 0 ];
                $orgip = $ip;
                $ip = sprintf( '%u', ip2long( $ip ) );

                if ( $ip < $this->min or $ip > $this->max )
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

                $oldtop = -1;
                $oldbot = -1;
                $nextrecord = floor( ( $top + $bottom ) / 2 );

                if ( $ip == $this->min )
                {
                    $nextrecord = 1;
                    $tmp = substr( $this->_data,8,2 );
                    return $this->str2short( $tmp );
                }
                elseif ( $ip == $this->max )
                {
                    $nextrecord = $this->records;
                    $tmp = substr( $this->_data,( ( $nextrecord * $this->recsize ) - $this->recsize + 8 ),2 );
                    return $this->str2short( $tmp );
                }

                $cnt = 0;
                while ( !$found )
                {
                    $cnt++;
                    $pos = ( $nextrecord * $this->recsize );
                    $tmp = substr( $this->_data, $pos, 4 );
                    $pos += 4;
                    $start = sprintf( '%u', $this->str2long( $tmp ) );
                    if ( $ip < $start )
                    {
                        $top = $nextrecord;
                    }
                    else
                    {
                        $tmp = substr( $this->_data, $pos, 4 );
                        $pos += 4;
                        $end = sprintf( '%u', $this->str2long( $tmp ) );
                        if ( $ip > $end )
                        {
                            $bottom = $nextrecord;
                        }
                        else
                        {
                            $tmp = substr( $this->_data, $pos, 2 );
                            return $this->str2short( $tmp );
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
            unset( $this->_data );
            $this->has_init = 0;
        }

    }

?>
