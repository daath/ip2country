unit MainForm;

interface

uses
  Windows, Messages, SysUtils, Variants, Classes, Graphics, Controls, Forms,
  Dialogs, StdCtrls, Ip2Country, GifImage, ExtCtrls;

type
  TForm1 = class(TForm)
    btnLookup: TButton;
    txtIpAddress: TEdit;
    Label1: TLabel;
    lblResult: TLabel;
    Image1: TImage;
    lblCC: TLabel;
    chkDownloadFlag: TCheckBox;
    procedure btnLookupClick(Sender: TObject);
    procedure FormCreate(Sender: TObject);
    procedure FormDestroy(Sender: TObject);
    procedure txtIpAddressChange(Sender: TObject);
    procedure txtIpAddressEnter(Sender: TObject);
  private
    { Private declarations }
    Function DownloadFlag(const CountryCode: string): Boolean;
  public
    { Public declarations }
    Ip2c: TIp2Country;
    FlagFilename: String;
  end;

var
  Form1: TForm1;

implementation
Uses UrlMon;

{$R *.dfm}

procedure TForm1.btnLookupClick(Sender: TObject);
Var
  CountryIndex: Integer;
  CountryCode: String;
begin
  Screen.Cursor := crHourGlass;
  try
    btnLookup.Enabled := False;
    lblCC.Caption := '';
    Image1.Picture.Graphic := nil;

    CountryIndex := Ip2c.Lookup(txtIpAddress.Text);
    CountryCode := Ip2c.CountryCode[CountryIndex];
    if CountryIndex = -1 then
    begin
      lblResult.Font.Color := clRed;
      lblResult.Caption := 'The ip Address could not be mapped to a country code';
    end
    else
    begin
      if (chkDownloadFlag.Checked) then
      begin
        if DownloadFlag(CountryCode) then
          image1.Picture.LoadFromFile(FlagFilename);
      end;

      lblResult.Font.Color := clBtnText;
      lblResult.Caption := 'The ip Address belongs to: ';
      lblCC.Caption := CountryCode;
    end;
  Finally
     Screen.Cursor := crDefault;
     btnLookup.Enabled := True;
  end;
  txtIpAddress.SetFocus;
end;

function TForm1.DownloadFlag(const CountryCode: string): Boolean;
Const
  BASE_URL = 'http://files.weirdsilence.net/images/flags/';
begin
   Result := URLDownloadToFile(nil,
        PChar(BASE_URL + LowerCase(CountryCode) + '.gif'),
        PChar(FlagFilename), 0, nil) = S_OK;
end;

procedure TForm1.FormCreate(Sender: TObject);
begin
  FlagFilename := ExtractFilePath(Application.ExeName) + 'flag.gif';
  // If you placed the ip2cntry.dat file in another directory than then
  // directory of this exe file, please edit the line below and put in then
  // full path to the database.
  ip2c := TIp2Country.Create('ip2cntry.dat');
end;

procedure TForm1.FormDestroy(Sender: TObject);
begin
  if FileExists(FlagFilename) then
    DeleteFile(FlagFilename);

  Ip2c.Free;
end;

procedure TForm1.txtIpAddressChange(Sender: TObject);
begin
  btnLookup.Enabled := txtIpAddress.Text <> '';
end;

procedure TForm1.txtIpAddressEnter(Sender: TObject);
begin
  txtIpAddress.SelectAll;
end;

end.
