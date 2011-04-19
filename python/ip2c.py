"""
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
"""
from sys import argv
from time import clock
from ip2country import ip2country

if len( argv ) == 2:
    ip2c = ip2country()
    start = clock()
    idx = ip2c.lookup( argv[ 1 ] )
    if idx == -1:
        print "Couldn't find it!"
    elif idx == -2:
        print "Give me an IP damnit!"
    elif idx == -3:
        print "Not an IP 2 Country database!"
    else:
        end = clock()
        cname = ip2c.countryCode( idx )
        print "%s is %s" % ( argv[ 1 ], cname )
        print "Lookup took %d microseconds" % ( ( end - start ) * 1000000 )
else:
    print "Give me an IP damnit!"
