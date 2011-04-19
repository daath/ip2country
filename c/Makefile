CFLAGS += -DUNIX -march=i586 -O2 -fomit-frame-pointer -Wall
LDFLAGS += -s

all: ip2c ip2c_bench

.PHONY: all clean

ip2c: ip2country.o
	$(CC) $(CFLAGS) $(LDFLAGS) ip2country.o ip2c.c -o ip2c

ip2country.o: ip2country.h ip2country.c
	$(CC) $(CFLAGS) -c ip2country.c

ip2c_bench: ip2country.o
	$(CC) $(CFLAGS) $(LDFLAGS) ip2country.o ip2c_bench.c -o ip2c_bench

clean:
	rm -f ip2c ip2c_bench ip2country.o

