@echo off
echo.
echo Building library ip2country\bin\Debug\ip2country.dll
csc /nologo /t:library /out:ip2country\bin\Debug\ip2country.dll ip2country\ip2country.cs
echo Building benchmark ip2c_bench\bin\Debug\ip2c_bench.exe
csc /nologo /t:winexe /out:ip2c_bench\bin\Debug\ip2c_bench.exe /r:ip2country\bin\Debug\ip2country.dll ip2c_bench\ip2c_bench.cs
copy /y /b ip2country\bin\Debug\ip2country.dll ip2c_bench\bin\Debug\
echo Building command line tool ip2c\bin\Debug\ip2c.exe
csc /nologo /t:exe /out:ip2c\bin\Debug\ip2c.exe /r:ip2country\bin\Debug\ip2country.dll ip2c\ip2c.cs
copy /y /b ip2country\bin\Debug\ip2country.dll ip2c\bin\Debug\
echo.
