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

using System;
using WeirdSilence.Net.Ip2Country;

public class ip2countryTest
{
    public static void Main(string[] argv)
    {
        if (argv.Length == 1)
        {
            ip2country ic = new ip2country( "ip2cntry.dat" );
            TimeSpan span;
            DateTime end;
            DateTime start = DateTime.Now;
            int countryidx = ic.lookup( argv[0] );
            
            switch ( countryidx )
            {
                case -1:
                    Console.WriteLine( "{0} is not in the database", argv[0] );
                    break;
                case -2:
                    Console.WriteLine( "{0} doesn't seem to be a valid IP number.", argv[0] );
                    break;
                case -3:
                    Console.WriteLine( "ip2cntry.dat is not a valid database" );
                    break;
                default:
                    String country = ic.countryCode( countryidx );
                    end = DateTime.Now;
                    span = new TimeSpan( end.Ticks - start.Ticks );
                    Console.WriteLine( "{0} is in {1} - took {2} ms", argv[0], country, Math.Round(span.TotalMilliseconds) );
                    break;
            }
        }
        else
        {
            Console.WriteLine( "Usage: ip2c <IP address>" );
        }
    }
}
