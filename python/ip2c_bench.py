"""
/*****************************************************************************
//
// IP2Country benchmark app for IP2Country module
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

ip2c = ip2country()

f = open( "Servers.lst", "r" )
lines = f.readlines()
n = len(lines)
f.close()

start = clock()
#codes = []
for ip in lines:
    code = ip2c.lookup( ip )
    #if code == -1:
    #    codes.append( ( ip, ip2c.lookup( ip ) ) )
end = clock()
secs = ( end - start )
perSec = n / secs

#data = ''
#codes.sort()
#i = 0
#for ip, code in codes:
#    i += 1
#    data += str(i) + ". " + ip.strip() + "\n"
#print data
print "%d IPs in %.2f seconds - %.2f lookups per second" % ( n, secs, perSec )
