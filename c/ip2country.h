/*****************************************************************************
//
// IP2Country module header
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

#ifndef _IP2C_MODULE_
#define _IP2C_MODULE_

#pragma pack(1)

#define IP2C_IP_NOT_FOUND -1
#define IP2C_NOT_IP2C_DATABASE -2
#define IP2C_DATABASE_OPEN_ERROR -3
#define IP2C_CALL_INIT_FIRST -4
#ifdef WINDLL
#define EXPORT __declspec(dllexport)
#else
#define EXPORT
#endif

typedef struct fileMagic
{
    char magicBytes[4];
    long indexIdx;
} fileHeader;

typedef struct sIndex
{
    unsigned long records;
    unsigned long min;
    unsigned long max;
    unsigned char recSize;
    unsigned short countries;
} startIndex;

typedef struct minMaxA
{
    unsigned char min;
    unsigned char max;
} aClassLimits;

typedef struct iprec
{
    unsigned long start;
    unsigned long end;
    short countryIndex;
} rangeRec;

typedef struct ccode
{
    char code[3];
} countryCode;

EXPORT int ip2c_init( const char* filename );
EXPORT short ip2c_lookup( const char ipnumber[] );
EXPORT int ip2c_idx2country( const int idx, countryCode* theCode );
EXPORT void ip2c_destroy( void );

#endif

