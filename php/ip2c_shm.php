<?php

/*****************************************************************************
//
// IP2Country module - Shared memory version
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

    require_once( 'wsshm.php' );

    define( 'IP2C_IDX_KEY', 1764909929 );
    define( 'IP2C_DATA_KEY',1764909924 );
    define( 'IP2C_DATE_KEY',1764909933 );
    
    define( "IP2C_IP_NOT_FOUND", -1 );
    define( "IP2C_NOT_IP2C_DATABASE", -2 );
    define( "IP2C_DATABASE_OPEN_ERROR", -3 );
    define( "IP2C_CALL_INIT_FIRST", -4 );    

    class ip2countryShm
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
        var $shm = NULL;
        var $has_init = 0;

        function init( $filename )
        {
            if ( $this->has_init != 1 )
            {
                $shm = new wsShm;
                $refresh = false;
                if ( $shm->open( IP2C_DATE_KEY,'rb' ) )
                {
                    // Check date
                    $date = $shm->read( 0 );
                    $filedate = filemtime( $filename );
                    $shm->close( );
                    if ( $date != "$filedate" )
                    {
                        // Refresh shared memory
                        $refresh = true;
                    }
                    else
                    {
                        // Populate local idx
                        $this->loadIdx( );
                    }
                }
                else
                {
                    $refresh = true;
                }
                if ( $refresh )
                {
                    // Refresh shared memory
                    $res = $this->reloadShared( $filename );
                }
                $this->shm = new wsShm;
                if ( !$this->shm->open( IP2C_DATA_KEY,'rb' ) )
                {
                    die( "Couldn't open shared memory for data..." );
                }
                $this->has_init = 1;
            }
        }

        function readlong( $fp )
        {
            $tmp = fread( $fp, 4 );
            return ord( $tmp[ 3 ] ) << 24 | ord( $tmp[ 2 ] ) << 16 | ord( $tmp[ 1 ] ) << 8 | ord( $tmp[ 0 ] );
        }

        function reloadShared( $filename )
        {
            $filetime = filemtime( $filename );
            if ( $fp = fopen( $filename, 'rb' ) )
            {
                $tmp = fread( $fp, 4 );
                if ( $tmp != 'ip2c' )
                {
                    return IP2C_NOT_IP2C_DATABASE;
                }
                $idx = $this->readlong( $fp );
                fseek( $fp, $idx );
                $data = '';
                while ( !feof( $fp ) )
                {
                    $data .= fread( $fp, 2048 );
                }
                $idxs = new wsShm;
                if ( $idxs->open( IP2C_IDX_KEY,'wb',strlen( $data ) ) )
                {
                    $idxs->write( $data );
                    $idxs->close( );
                }
                else
                {
                    die( "Couldn't create shared memory for index!" );
                }
                
                $this->loadIdx( );

                fseek( $fp,8 );
                $datalen = $this->records * $this->recsize;
                $data = fread( $fp,$datalen );
                fclose( $fp );
                
                $dataShm = new wsShm;
                if ( $dataShm->open( IP2C_DATA_KEY,'wb',$datalen ) )
                {
                    $dataShm->write( $data );
                    $dataShm->close( );
                }
                else
                {
                    die( "Couldn't write data!" );
                }
                
                $dateShm = new wsShm;
                $dateShm->open( IP2C_DATE_KEY,'wb',strlen( "$filetime" ) );
                $dateShm->write( $filetime );
                $dateShm->close( );
            }
            else
            {
                return IP2C_DATABASE_OPEN_ERROR;
            }
        }

        function loadIdx( )
        {
            $idxs = new wsShm;
            if ( $idxs->open( IP2C_IDX_KEY,'rb' ) )
            {
                // Read it
                $this->records = $idxs->readLong( );
                $this->min = sprintf( '%u',$idxs->readLong( ) );
                $this->max = sprintf( '%u',$idxs->readLong( ) );
                $this->recsize = $idxs->readByte( );
                $this->countries = $idxs->readShort( );
            }
            $tmp = $idxs->read( ( $this->countries * 2 ) );
            for ( $i = 0; $i < $this->countries; $i++ )
            {
                $this->countryname[] = substr( $tmp, ( $i * 2 ), 2 );
            }

            $this->minip = $idxs->readByte( );
            $this->maxip = $idxs->readByte( );
            for ( $i = 0; $i < 256; $i++ )
            {
                $this->topidx[ $i ] = -1;
            }
            for ( $i = $this->minip; $i <= $this->maxip; $i++ )
            {
                $this->topidx[ $i ] = $idxs->readLong( );
            }
            $idxs->close( );
        }

        function lookup( $ip )
        {
            if ( $this->has_init != 1 )
            {
                return IP2C_CALL_INIT_FIRST;
            }
            if ( $this->shm )
            {
                $ip = trim( $ip );
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

                $oldtop = -1;
                $oldbot = -1;
                $nextrecord = floor( ( $top + $bottom ) / 2 );

                if ( $ip == $this->min )
                {
                    $nextrecord = 1;
                    $this->shm->seek( 8 );
                    return $this->shm->readShort( );
                }
                elseif ( $ip == $this->max )
                {
                    $nextrecord = $this->records;
                    $this->shm->seek( ( ( $nextrecord * $this->recsize ) - $this->recsize + 8 ) );
                    return $this->shm->readShort( );
                }

                $cnt = 0;
                while ( !$found )
                {
                    $cnt++;
                    $this->shm->seek( $nextrecord * $this->recsize );
                    $start = sprintf( '%u', $this->shm->readLong( ) );
                    if ( $ip < $start )
                    {
                        $top = $nextrecord;
                    }
                    else
                    {
                        $end = sprintf( '%u', $this->shm->readLong( ) );
                        if ( $ip > $end )
                        {
                            $bottom = $nextrecord;
                        }
                        else
                        {
                            return $this->shm->readShort( );
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
            else
            {
                die( 'No shared memory handle' );
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
            if ( $this->shm )
            {
                $this->shm->close( );
                $this->has_init = 0;
            }
        }
    }

?>
