/*****************************************************************************
//
// IP2Country demo app for IP2Country module
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

#include <stdio.h>
#include <stdlib.h>
#include "ip2country.h"

int main( int argc, char *argv[] )
{
    int result;
    countryCode theCode;

    printf( "IP 2 Country lookup (c) 2004 L. Petersen, Weird Silence\n" );
    if ( argc != 2 )
    {
        fprintf( stderr, "Usage: %s <ipnumber>\n", argv[0] );
        exit( 1 );
    }

    ip2c_init( "ip2cntry.dat" );
    result = ip2c_lookup(argv[1]);

    switch ( result )
    {
        case -1:
            fprintf( stderr, "The IP is not in the database.\n" );
            break;
        case -2:
            fprintf( stderr, "The datafile is not valid.\n" );
            break;
        case -3:
            fprintf( stderr, "Could not open datafile.\n" );
            break;
        case -4:
            fprintf( stderr, "Init wasn't called first.\n" );
            break;
        default:
            ip2c_idx2country( result, &theCode );
            printf( " %s is %s\n", argv[1], theCode.code );
    }

    ip2c_destroy();

    return 0;
}

