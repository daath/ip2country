/*****************************************************************************
//
// IP2Country assembly
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

namespace WeirdSilence.Net.Ip2Country 
{
	using System;
	using System.Net;
	using System.IO;
	using System.Text;
	
	public class ip2country
	{
	    private MemoryStream ms;
	    private BinaryReader br;
	    private int records = 0;
	    private uint min = 0;
	    private uint max = 0;
	    private byte recsize = 0;
	    private uint countries = 0;
	    private String[] countryname;
	    private byte minip = 0;
	    private byte maxip = 0;
	    private int[] topidx;
	    private bool invalid = false;
	    private bool cache = true;
	    private byte offset = 0;
	
		/// <summary>
		/// Converts four bytes to an unsigned long integer
		/// </summary>
		/// <param name="ipAddress">
		/// Four bytes, little endian, representing the IPv4 address
		/// </param>
		/// <returns>
		/// 32 bit unsigned long integer representing the IPv4 address
		/// </returns>
	    internal static long GetLongAddress( byte[] ipAddress )
	    {
	        return ipAddress[0] * 16777216L + ipAddress[1] * 65536L + 
	                ipAddress[2] * 256L + ipAddress[3];
	    }
	
		/// <overloads>This method has two overloads</overloads>
		/// <summary>Overloaded contructor - Assumes caching</summary>
		/// <param name="filename">Filename of database file to use</param>
	    public ip2country( String filename )
	    {
	        _ip2country( filename, true );
	    }
	
		/// <summary>Overloaded contructor - Caching optional</summary>
		/// <param name="filename">Filename of database file to use</param>
		/// <param name="useCache">
		/// Use memory cache (true), or read directly from file (false)
		/// </param>
	    public ip2country( String filename, bool useCache )
	    {
	        _ip2country( filename, useCache );
	    }
	
	    private void _ip2country( String filename, bool useCache )
	    {
	        FileStream fs = File.Open( filename, FileMode.Open, 
	                                      FileAccess.Read );
	        int fileLen = Convert.ToInt32( fs.Length );
	        cache = useCache;
	        BinaryReader lbr = new BinaryReader( fs );
	        try
	        {
	            String magic = lbr.ReadChar().ToString() + 
	                            lbr.ReadChar().ToString() + 
	                            lbr.ReadChar().ToString() + 
	                            lbr.ReadChar().ToString();
	            if ( magic != "ip2c" )
	                invalid = true;
	            int idx = lbr.ReadInt32();
	            lbr.BaseStream.Position = idx;
	            records = lbr.ReadInt32();
	            min = lbr.ReadUInt32();
	            max = lbr.ReadUInt32();
	            recsize = lbr.ReadByte();
	            countries = lbr.ReadUInt16();
	            countryname = new String[countries];
	            for (int i = 0; i < countries; i++)
	                countryname[i] = lbr.ReadChar().ToString() + 
	                                 lbr.ReadChar().ToString();
	            minip = lbr.ReadByte();
	            maxip = lbr.ReadByte();
	            topidx = new int[256];
	            for (int i = 0; i <= 255; i++)
	                topidx[i] = -1;
	            for (int i = minip; i <= maxip; i++)
	                topidx[i] = lbr.ReadInt32();
	            
	            lbr.BaseStream.Position = 8;
	            if (cache)
	            {
	                Console.WriteLine( "Using cache..." );
	                fileLen = Convert.ToInt32( fileLen - lbr.BaseStream.Position );
	                byte[] sbuffer = lbr.ReadBytes( fileLen );
	                ms = new MemoryStream( fileLen );
	                BinaryWriter lbw = new BinaryWriter( ms );
	                lbw.Write( sbuffer );
	                br = new BinaryReader( ms );
	            }
	            else
	            {
	                Console.WriteLine( "Cache disabled..." );
	                offset = 8;
	                br = new BinaryReader( lbr.BaseStream );
	            }
	            br.BaseStream.Position = offset;
	        }
	        catch (Exception)
	        {
	            invalid = true;
	        }
	    }

		/// <summary>
		/// Returns the country code from an index
		/// </summary>
		/// <param name="index">
		/// The index of the country code to return
		/// </param>
		/// <returns>
		/// An ISO 2 letter country code, i.e. DK / SE / US
		/// </returns>
	    public String countryCode(int index)
	    {
	    	String res = "--";
	    	if ( index >= 0 && index < countries )
	    	{
	    		res = countryname[ index ];
	    	}
	        return res;
	    }
	    
	    /// <summary>
	    /// Try to map an IP address to a country
	    /// </summary>
	    /// <param name="ipnum">
	    /// A regular dotted quad IPv4 address
	    /// </param>
	    /// <returns>
	    /// An index to the country code for use with method countryCode()
	    /// </returns>
	    public int lookup( String ipnum )
	    {
	        if (invalid)
	        {
	            return -3;
	        }
	        int len = ipnum.Length;
	        int cnt;
	        int mult = 1;
	        ushort num = 0;
	        int shift = 24;
	        int aclass;
	        uint ip = 0;
	        int top, bottom, nextrecord;
	        int i;
	        int oldtop = -1;
	        int oldbottom = -1;
            if ( len == 0 || ipnum[ 0 ] == '.' )
                return -1;
        
	        cnt = 0;    
            while ( cnt < len )
            {
                if ( ipnum[ cnt ] >= '0' && ipnum[ cnt ] <= '9' )
                {
                    num = (ushort) ( ( num * ( mult ) ) + ( ipnum[ cnt ] & 15 ) );
                    mult = 10;
                }
                else if ( ipnum[ cnt ] == '.' )
                {
                    ip |= (uint) ( num << shift );
                    shift -= 8;
                    num = 0;
                    mult = 1;
                }
                else
                    return -1;
                if (shift < 0)
                    break;
                cnt++;
            }
            if (shift != 0)
                return -1;
	        ip |= num;
	        
	        aclass = (byte) ( ip >> 24 );
	        
	        if ( ip < min || ip > max || topidx[ aclass ] < 0 )
	            return -1;
	        if ( aclass == maxip )
	        {
	            top = records;
	            bottom = Math.Abs( topidx[ aclass - 1 ] ) - 1;
	        }
	        else
	        {
	            bottom = Math.Abs( topidx[ aclass ] ) - 1;
	            i = 1;
	            while ( topidx[ aclass + i ] < 0 )
	                i++;
	            top = topidx[ aclass + i ];
	        }
	        if ( aclass == minip )
	            bottom = 0;
	        
	        nextrecord = ( top + bottom ) / 2;
	
	        while (true)
	        {
	            br.BaseStream.Position = ( nextrecord * recsize ) + offset;
	            if ( ip < br.ReadUInt32() )
	                top = nextrecord;
	            else
	            {
	                if ( ip > br.ReadUInt32() )
	                    bottom = nextrecord;
	                else
	                    return br.ReadInt16();
	            }
	            nextrecord = ( top + bottom ) / 2;
	            if ( top == oldtop && bottom == oldbottom )
	                return -1;
	            oldtop = top;
	            oldbottom = bottom;
	        }
	    }
	    
	    ~ip2country()
	    {
	        br.Close();
	    }
	}
}
