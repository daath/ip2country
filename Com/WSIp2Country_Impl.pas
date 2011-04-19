unit WSIp2Country_Impl;

{$WARN SYMBOL_PLATFORM OFF}

interface

uses
  ComObj, ActiveX, WSIp2Country_TLB, Ip2Country, StdVcl;

type
  TIp2Country = class(TAutoObject, IIp2Country)
  private
    Ip2c: Ip2Country.TIp2Country;
  protected
    function LoadDatabase(const Filename: WideString): WordBool; safecall;
    function Lookup(const IpStr: WideString): Integer; safecall;
    function Get_CountryCode(Param1: Integer): WideString; safecall;
  public
    destructor Destroy; override;
  end;

implementation

uses ComServ;

function TIp2Country.LoadDatabase(const Filename: WideString): WordBool;
begin
  Try
    if Assigned(ip2c) then
    begin
      Ip2c.Free;
      Ip2c := nil;
    end;

    Try
      Ip2c := Ip2Country.TIp2Country.Create(Filename);
    Except
      Ip2c := Nil;
    end;
  Finally
    Result := Ip2c <> nil;
  end;
end;

function TIp2Country.Lookup(const IpStr: WideString): Integer;
begin
  If Assigned(Ip2c) then
    Result := Ip2c.Lookup(IpStr)
  else
    Result := -1;
end;

function TIp2Country.Get_CountryCode(Param1: Integer): WideString;
begin
  If Assigned(Ip2c) then
    Result := Ip2c.CountryCode[Param1]
  else
    Result := '';
end;


destructor TIp2Country.Destroy;
begin
  If Assigned(Ip2c) then
    Ip2c.Free;
  inherited;
end;

initialization
  TAutoObjectFactory.Create(ComServer, TIp2Country, Class_Ip2Country,
    ciMultiInstance, tmApartment);
end.
