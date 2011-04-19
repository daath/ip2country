Quickstart: In linux type make, edit CFLAGS in Makefile if you want to change 
my optimizations.
The makefile is only for linux...

Remember to add defines when compiling -DWIN32 for windows -DUNIX for 
linux/unix.
If you are SURE that you will only ever put valid IP addresses into it,
you can compile ip2country.c with -DUSEFASTIP2LONG that will speed it
up roughly 20%, it removes any kind of error checking on the input IP.
Use ONLY if you are SURE that you will only be feeding it valid IP addresses.

If you want to build from windows, you can. If you need a windows compiler
you can get Borland's free one here:

    http://www.borland.com/bcppbuilder/freecompiler/

I built it with this using these commands:

bcc32 -IPATH_TO_BORLAND_BCC55\include -LPATH_TO_BORLAND_BCC55\lib -6 -DWIN32 ip2c_bench.c ip2country.c
bcc32 -IPATH_TO_BORLAND_BCC55\include -LPATH_TO_BORLAND_BCC55\lib -6 -DWIN32 ip2c.c ip2country.c

Change PATH_TO_BORLAND_BCC55 to whereever you placed BCC, also you may need 
to add PATH_TO_BORLAND_BCC55\bin to you windows PATH (unless you specify the
entire path).

It also compiles with Microsoft Visual C++ Toolkit 2003 available free from
Microsoft here:

    http://msdn.microsoft.com/visualc/vctoolkit2003/

If you want to make the most of this toolkit, you should also download the 
Platform SDK:

    http://www.microsoft.com/msdownload/platformsdk/sdkupdate/

I compiled the two test applications with MSVC2003 like so:

    cl /nologo /DWIN32 /G6 /O2 /GL ip2c.c ip2country.c
    cl /nologo /DWIN32 /G6 /O2 /GL ip2c_bench.c ip2country.c


Files:
------

ip2c.c - test app, use ./ip2c ip.number.here.man
ip2c_bench.c - benchmarks your machine, see how many lookups/sec you get
ip2country.c - the module that does the magic on ip2cntry.dat
ip2country.h - header file for the module
ip2cntry.dat - the binary database built from ip-2-country

This is licensed under GPL.

"This 'work' uses the IP-to-Country Database
 provided by WebHosting.Info (http://www.webhosting.info),
 available from http://ip-to-country.webhosting.info."

