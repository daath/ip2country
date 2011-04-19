Dim ip2c

Set ip2c = WScript.CreateObject("WSIp2Country.Ip2Country")   
                                  
ip2c.LoadDatabase("ip2cntry.dat")
Wscript.Echo Ip2c.CountryCode(Ip2c.Lookup("80.63.66.190"))
