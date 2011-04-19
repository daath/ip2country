library Ip2CountryCom;

uses
  ComServ,
  WSIp2Country_TLB in 'WSIp2Country_TLB.pas',
  WSIp2Country_Impl in 'WSIp2Country_Impl.pas' {Ip2Country: CoClass};

exports
  DllGetClassObject,
  DllCanUnloadNow,
  DllRegisterServer,
  DllUnregisterServer;

{$R *.TLB}

{$R *.RES}

begin
end.
