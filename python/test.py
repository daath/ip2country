"""
/*****************************************************************************
//
// IP2Country helper app for debugging IP2Country module
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

from struct import unpack
from ip2country import ip2country

ip2c = ip2country()

print "Records:", ip2c.records, "range:", ip2c.min, "-", ip2c.max, "A class ", ip2c.minip, "-", ip2c.maxip, "recsize:", ip2c.recsize

for i in range( 0, ip2c.records ):
    pos = ( i * ip2c.recsize )
    start,end,cidx = unpack( '<LLH', ip2c._data[ pos : ( pos + ip2c.recsize ) ] )
    if start > 1126930494L:
        cntry = ip2c.countryCode( cidx )
        print "record",i,"pos",pos,start,"-",end,cntry
        if end > 1156930494L:
            break
    
    
