var ip2c;

ip2c = WScript.CreateObject("WSIp2Country.Ip2Country")   
                                  
ip2c.LoadDatabase("ip2cntry.dat");
WScript.Echo(ip2c.CountryCode(ip2c.Lookup("80.63.66.190")));
