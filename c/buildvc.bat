@echo off
echo.
echo Building with Microsoft Visual C++ Optimizing Compiler Kit
echo.
cl /DWIN32 /nologo /G6 /GL /O2 ip2c.c ip2country.c
cl /DWIN32 /nologo /G6 /GL /O2 ip2c_bench.c ip2country.c
echo.
echo Done.
echo.
