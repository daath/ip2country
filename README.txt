This is the general file structure of the binary database file.

Header (8 bytes)
================

length                  what
----------------------- -----------------------------
4 bytes                 4cc
4 bytes                 offset of index

Country records (10 bytes each)
===============================

length                  what
----------------------- -----------------------------
4 bytes                 start rage
4 bytes                 end range
2 bytes                 country index

Index header (15 bytes)
=======================

length                  what
----------------------- -----------------------------
4 bytes                 Number of records
4 bytes                 Smallest IP
4 bytes                 Largest IP
1 bytes                 Record size
2 bytes                 Amount of countries

Country list (2 bytes each)
===========================

length                  what
----------------------- -----------------------------
2 bytes                 tldCC - Two char country code

A-class info (6 bytes each)
===========================

length                  what
----------------------- -----------------------------
1 byte                  Smallest A-class
1 byte                  Largest A-class
4 bytes                 Position of A-class in index


File structure
==============
1. Header
2. Country records (Several)
3. Index header
4. Country List (Several)
5. A-class info (Several)
