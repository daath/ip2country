/*****************************************************************************
//
// IP2Country module
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
#include <fcntl.h>
#include <ctype.h>
#include <memory.h>
#include "ip2country.h"

char has_init = 0;
startIndex* theIndex;
aClassLimits* aLimits;
char countryName[ (256 * 2) + 1 ];
long topidx[ 256 ];

int topIdx[ 256 ];
rangeRec* rdata;

int ip2c_init( const char* filename )
{
    // Load index and fill
    FILE* fp;
    fileHeader *fhead;
    //int count;

    if ( has_init == 0 )
    {
        has_init = 1;
        
        fhead = (fileHeader *) malloc( sizeof( fileHeader ) );
        fp = fopen( filename, "rb" );
        if ( fp == NULL )
        {
            return IP2C_DATABASE_OPEN_ERROR;
        }
        fread( fhead, sizeof( fileHeader ), 1, fp );
        if ( memcmp( fhead->magicBytes, "ip2c", 4) != 0 )
        {
            fclose( fp );
            free ( fhead );
            return IP2C_NOT_IP2C_DATABASE;
        }
        else
        {
            if ( fseek( fp, fhead->indexIdx, SEEK_SET ) == 0 )
            {
                theIndex = (startIndex *) malloc( sizeof( startIndex ) );
                fread( theIndex, sizeof( startIndex ), 1, fp );
                fread( countryName, 2, theIndex->countries, fp );
                countryName[ ( theIndex->countries * 2 ) + 1 ] = '\0';
                aLimits = (aClassLimits *) malloc( sizeof( aClassLimits ) );
                fread( aLimits, sizeof( aClassLimits ), 1, fp );
                fread( topidx, (aLimits->max - aLimits->min + 1) * 4, 1, fp );
                fseek( fp, 8, SEEK_SET );
                rdata = (rangeRec *) malloc( theIndex->records * theIndex->recSize );
                fread( rdata, theIndex->recSize, theIndex->records, fp );
            }
            fclose( fp );
            free( fhead );
        }
    }
    return 0;
}

void ip2c_destroy( void )
{
    // Free memory etc
    if ( has_init == 1 )
    {
        free( theIndex );
        free( aLimits );
        free( rdata );
        has_init = 0;
    }
}

short ip2c_lookup( const char* ipnumber )
{
    unsigned long ip = 0;
    unsigned char aClass = 0;
    int top;
    int bottom;
    int oldtop = -1;
    int oldbottom = -1;
    int nextrecord;
    int i;
#ifdef USEFASTIP2LONG

    int z;
	unsigned long i2;
	
	i2 = ( *ipnumber++ ) & 15;
	if ( ( z = *ipnumber++ ) != '.' ) 
    {
		i2 = 10 * i2 + ( z & 15 );
		if ( ( z = *ipnumber++ ) != '.' ) 
        {
			i2 = 10 * i2 + ( z & 15 );
            ipnumber++;
		}
	}
    aClass = i2;
	ip = i2 << 24;
	i2 = ( *ipnumber++ ) & 15;
	if ( ( z = *ipnumber++ ) != '.' ) 
    {
		i2 = 10 * i2 + ( z & 15 );
		if ( ( z = *ipnumber++ ) != '.' ) 
        {
			i2 = 10 * i2 + ( z & 15 );
            ipnumber++;
		}
	}
	ip |= i2 << 16;
	i2 = ( *ipnumber++ ) & 15;
	if ( ( z = *ipnumber++ ) != '.' ) 
    {
		i2 = 10 * i2 + ( z & 15 );
		if ( ( z = *ipnumber++ ) != '.' ) 
        {
			i2 = 10 * i2 + ( z & 15 );
            ipnumber++;
		}
	}
	ip |= i2 << 8;
	i2 = ( *ipnumber++ ) & 15;
	if (*ipnumber) 
    {
		i2 = 10 * i2 + ( ( *ipnumber++ ) & 15 );
		if ( *ipnumber ) 
        {
			i2 = 10 * i2 + ( ( *ipnumber++ ) & 15 );
		}
	}
	ip |= i2;
    
#else
    
    int num = 0, mult = 1;
    int cnt = 0;
    int shift = 24;

    if ( has_init == 0 )
        return IP2C_CALL_INIT_FIRST;
    
    if ( ipnumber[ 0 ] == '\0' || ipnumber[ 0 ] == '.' )
        return IP2C_IP_NOT_FOUND;

    while ( ipnumber[ cnt ] != '\0' )
    {
        if ( ipnumber[ cnt ] >= '0' && ipnumber[ cnt ] <= '9' )
        {
            num = ( num * ( mult ) ) + ( ipnumber[ cnt ] & 15 );
            mult = 10;
        }
        else if ( ipnumber[ cnt ] == '.' )
        {
            ip |= num << shift;
            shift -= 8;
            num = 0;
            mult = 1;
        }
        else
            return IP2C_IP_NOT_FOUND;
        if (shift < 0)
            break;
        cnt++;
    }
    if (shift != 0)
        return IP2C_IP_NOT_FOUND;
    ip |= num;

    aClass = ( ip >> 24 );

#endif

    if ( aClass < aLimits->min || aClass > aLimits->max || topidx[ aClass - aLimits->min ] < 0 )
    {
        // IP is definitely not in the base
        return IP2C_IP_NOT_FOUND;
    }

    // Determine best limits for searching
    bottom = abs( topidx[ aClass - aLimits->min ] ) - 1;
    if ( aClass == aLimits->max )
        top = theIndex->records;
    else
    {
        i = 1;
        while ( topidx[ aClass - aLimits->min + i ] < 0 )
            i++;
        top = topidx[ aClass - aLimits->min + i ];
    }
    if ( aClass == aLimits->min )
        bottom = 0;

    while ( 1 )
    {
        nextrecord = ( top + bottom ) / 2;
        if ( ip < rdata[ nextrecord ].start )
        {
            /* No need for whatever is on top */
            top = nextrecord;
        }
        else if ( ip > rdata[ nextrecord ].end )
        {
            /* No need for whatever is at the bottom */
            bottom = nextrecord;
        }
        else
        {
            /* Woohooo! */
            return rdata[ nextrecord ].countryIndex;
        }
        if ( oldbottom == bottom && oldtop == top )
            return IP2C_IP_NOT_FOUND;

        oldtop = top;
        oldbottom = bottom;
    }
}

char *ip2c_idx2country( const int idx )
{
    static char res[3] = { "--\0" };
    if ( has_init == 1 && theIndex->countries >= idx && idx >= 0)
    {
        res[0] = countryName[ idx * 2 ];
        res[1] = countryName[ ( idx * 2 ) + 1 ];
        res[2] = '\0';
    }
    return res;
}

