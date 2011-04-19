@echo off

Set PATH=C:\Code\BCC55\bin;%PATH%
Set INCLUDE=C:\Code\BCC55\include;%INCLUDE%
Set LIB=C:\Code\BCC55\lib;%LIB%

rem echo Ændr denne fils stier og fjern kommenteringen og denne linie!
C:\code\bcc55\bin\bcc32 -IC:\Code\BCC55\include -LC:\Code\BCC55\lib -6 -DWIN32 ip2c_bench.c ip2country.c
C:\code\bcc55\bin\bcc32 -IC:\Code\BCC55\include -LC:\Code\BCC55\lib -6 -DWIN32 ip2c.c ip2country.c

pause
