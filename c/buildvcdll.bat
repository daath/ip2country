@echo off
echo.
echo Building DLL with Microsoft Visual C++ Optimizing Compiler Kit
echo.
cl /DWIN32 /DWINDLL /nologo /G6 /GL /O2 /LD ip2country.c
echo.
echo Done.
echo.
