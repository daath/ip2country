unit WSIp2Country_TLB;

// ************************************************************************ //
// WARNING                                                                    
// -------                                                                    
// The types declared in this file were generated from data read from a       
// Type Library. If this type library is explicitly or indirectly (via        
// another type library referring to this type library) re-imported, or the   
// 'Refresh' command of the Type Library Editor activated while editing the   
// Type Library, the contents of this file will be regenerated and all        
// manual modifications will be lost.                                         
// ************************************************************************ //

// PASTLWTR : 1.2
// File generated on 07-05-2004 17:54:58 from Type Library described below.

// ************************************************************************  //
// Type Lib: D:\Code\Delphi7\Apps\1Ws\Ip2Country_Com\Ip2CountryCom.tlb (1)
// LIBID: {96743BBF-A719-4727-9A92-7FCEA6F7DB96}
// LCID: 0
// Helpfile: 
// HelpString: Ip2CountryCom Library
// DepndLst: 
//   (1) v2.0 stdole, (C:\WINDOWS\System32\STDOLE2.TLB)
//   (2) v4.0 StdVCL, (C:\WINDOWS\System32\stdvcl40.dll)
// ************************************************************************ //
{$TYPEDADDRESS OFF} // Unit must be compiled without type-checked pointers. 
{$WARN SYMBOL_PLATFORM OFF}
{$WRITEABLECONST ON}
{$VARPROPSETTER ON}
interface

uses Windows, ActiveX, Classes, Graphics, StdVCL, Variants;
  

// *********************************************************************//
// GUIDS declared in the TypeLibrary. Following prefixes are used:        
//   Type Libraries     : LIBID_xxxx                                      
//   CoClasses          : CLASS_xxxx                                      
//   DISPInterfaces     : DIID_xxxx                                       
//   Non-DISP interfaces: IID_xxxx                                        
// *********************************************************************//
const
  // TypeLibrary Major and minor versions
  WSIp2CountryMajorVersion = 1;
  WSIp2CountryMinorVersion = 0;

  LIBID_WSIp2Country: TGUID = '{96743BBF-A719-4727-9A92-7FCEA6F7DB96}';

  IID_IIp2Country: TGUID = '{EC2A06D7-E3FE-4A12-A811-2FC3CFFF10C7}';
  CLASS_Ip2Country: TGUID = '{2F78CF2E-244A-490E-9EFE-2A72AE759173}';
type

// *********************************************************************//
// Forward declaration of types defined in TypeLibrary                    
// *********************************************************************//
  IIp2Country = interface;
  IIp2CountryDisp = dispinterface;

// *********************************************************************//
// Declaration of CoClasses defined in Type Library                       
// (NOTE: Here we map each CoClass to its Default Interface)              
// *********************************************************************//
  Ip2Country = IIp2Country;


// *********************************************************************//
// Interface: IIp2Country
// Flags:     (4416) Dual OleAutomation Dispatchable
// GUID:      {EC2A06D7-E3FE-4A12-A811-2FC3CFFF10C7}
// *********************************************************************//
  IIp2Country = interface(IDispatch)
    ['{EC2A06D7-E3FE-4A12-A811-2FC3CFFF10C7}']
    function LoadDatabase(const Filename: WideString): WordBool; safecall;
    function Lookup(const IpStr: WideString): Integer; safecall;
    function Get_CountryCode(Index: Integer): WideString; safecall;
    property CountryCode[Index: Integer]: WideString read Get_CountryCode;
  end;

// *********************************************************************//
// DispIntf:  IIp2CountryDisp
// Flags:     (4416) Dual OleAutomation Dispatchable
// GUID:      {EC2A06D7-E3FE-4A12-A811-2FC3CFFF10C7}
// *********************************************************************//
  IIp2CountryDisp = dispinterface
    ['{EC2A06D7-E3FE-4A12-A811-2FC3CFFF10C7}']
    function LoadDatabase(const Filename: WideString): WordBool; dispid 201;
    function Lookup(const IpStr: WideString): Integer; dispid 202;
    property CountryCode[Index: Integer]: WideString readonly dispid 203;
  end;

// *********************************************************************//
// The Class CoIp2Country provides a Create and CreateRemote method to          
// create instances of the default interface IIp2Country exposed by              
// the CoClass Ip2Country. The functions are intended to be used by             
// clients wishing to automate the CoClass objects exposed by the         
// server of this typelibrary.                                            
// *********************************************************************//
  CoIp2Country = class
    class function Create: IIp2Country;
    class function CreateRemote(const MachineName: string): IIp2Country;
  end;

implementation

uses ComObj;

class function CoIp2Country.Create: IIp2Country;
begin
  Result := CreateComObject(CLASS_Ip2Country) as IIp2Country;
end;

class function CoIp2Country.CreateRemote(const MachineName: string): IIp2Country;
begin
  Result := CreateRemoteComObject(MachineName, CLASS_Ip2Country) as IIp2Country;
end;

end.
