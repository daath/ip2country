(*****************************************************************************
//
// IP2Country header
//
// Copyright (C) 2004  N.Petersen & L. Petersen
// Weird Silence, www.weirdsilence.net
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
*****************************************************************************)
unit Ip2Country;

interface
Uses Classes, SysUtils;

Type
  TFileHeader = packed record
    MagicBytes: array[0..3] of char;
    IndexIdx  : integer;
  end;

  TStartIndex = packed record
    Records:   integer;
    Min:       cardinal;
    Max:       cardinal;
    RecSize:   byte;
    Countries: word;
  end;

  TClassLimits = packed record
    Min: byte;
    Max: byte;
  end;


Type
  TCountryCode = Array[0..1] of char;

  TIp2Country = class
  private
    FCountryCodes: Array[0..255] of TCountryCode;
    TheIndex: TStartIndex;
    MinIp: Byte;
    MaxIp: Byte;
    TopIdx: Array[0..255] of Integer;
    Data: PChar;
    Limits: TClassLimits;
    procedure LoadFromStream(Stream: TStream);
    Procedure LoadData(const Filename: String);
    function get_CountryCode(Index: Smallint): String;
  public
    constructor Create(const Filename: String); overload;
    constructor Create(Stream: TStream); overload;
    destructor Destroy; override;
    Function Lookup(Const IpNum: String): SmallInt;
    Property CountryCode[Index: Smallint]: String read get_CountryCode;
  end;


implementation
Uses Winsock;

{ TIp2Country }

constructor TIp2Country.Create(const Filename: String);
begin
  LoadData(Filename);
end;

constructor TIp2Country.Create(Stream: TStream);
begin
  LoadFromStream(Stream);
end;

destructor TIp2Country.Destroy;
begin
  FreeMem(Data);
  inherited;
end;

function TIp2Country.get_CountryCode(Index: Smallint): String;
begin
  if (Index >= 0) and (Index <= TheIndex.Countries) then
    Result := FCountryCodes[Index]
  else
    Result := '--';
end;


procedure TIp2Country.LoadData(const Filename: String);
var
  Fs: TFileStream;
begin
  Fs := TFileStream.Create(Filename, fmOpenRead, fmShareDenyWrite);
  try
    LoadFromStream(Fs);
    Fs.Free;
  except
    fs.Free;
    Raise;
  end;
end;

procedure TIp2Country.LoadFromStream(Stream: TStream);
Var
  DataSize: Integer;
  Header: TFileHeader;
begin
  try
    FreeMem(Data);
    Data := nil;
    FillChar(FCountryCodes, SizeOf(FCountryCodes), 0);
    FillChar(TopIdx, SizeOf(TopIdx), 0);
    FillChar(TheIndex, SizeOf(TheIndex), 0);
    FillChar(Limits, SizeOf(Limits), 0);
    MinIp := 0;
    MaxIp := 0;
    Stream.Read(Header, SizeOf(Header));
    if Header.MagicBytes = 'ip2c' then
    with Stream do
    begin
      Seek(Header.IndexIdx, soFromBeginning);
      Read(TheINdex, SizeOf(TheIndex));
      Read(FCountryCodes[0], TheIndex.Countries * 2);
      Read(Limits, SizeOf(Limits));
      Read(Topidx[0], (Limits.Max - Limits.Min +1) * 4);
      Seek(8, soFromBeginning);
      DataSize := TheIndex.Records * TheIndex.RecSize;
      GetMem(Data, DataSize);
      Read(Data^, DataSize);
    end
    else
      Raise Exception.Create('Bad header found');
  except
    if Assigned(Data) then
      FreeMem(Data);
    Raise;
  end;
end;



function TIp2Country.Lookup(const IpNum: String): SmallInt;
Type
  PCardinal = ^Cardinal;
var
  AClass: byte;
  bottom,
  top,
  oldtop,
  oldbottom,
  i,
  nextrecord,
  pos: integer;
  Start,
  pEnd: Cardinal;
  ip: integer;
begin
  Result := -1;

  ip := inet_addr(pchar(IpNum));
  if ip <> INADDR_NONE then
  begin
    ip := ntohl(ip);
    AClass := ip shr 24;

    if ((AClass < Limits.Min) or (AClass > Limits.Max) or (TopIdx[AClass - Limits.Min] < 0)) then
      // IP is definitely not in the base
      Exit;

    // Determine best limits for searching
    bottom := abs(TopIdx[AClass - Limits.Min]) - 1;
    if AClass = Limits.Max then
      top := TheIndex.Records
    else
    begin
      i := 1;
      while TopIdx[AClass - Limits.Min + i] < 0 do
        Inc(i);
      top := TopIdx[AClass - Limits.Min + i];
    end;

    if AClass = Limits.Min then
      bottom := 0;

    oldtop := -1;
    oldbottom := -1;

    nextrecord := (top + bottom) div 2;

    while True do
    Begin
      pos := nextrecord * TheIndex.RecSize;
      start := PCardinal(data + pos)^;
      if ip < start then
        Top := nextrecord
      else
      begin
        inc(pos, 4);
        pEnd := PCardinal(data + pos)^;
        if ip > pEnd then
          // No need for whatever is at the bottom
          bottom := nextrecord
        else
        begin
          // Wohoooo!
          bottom := nextrecord;
          inc(pos, 4);
          Result := PWord(Data + Pos)^;
          break;
        end;
      end;

      nextrecord := ( top + bottom ) div 2;
      if (top = oldtop) and (bottom = oldbottom) then
        // We can't find it
        break;
      oldtop := top;
      oldbottom := bottom;
    end;
  end;
end;

end.
