program Ip2cGui;

uses
  Forms,
  Ip2Country in '..\Ip2Country.pas',  
  MainForm in 'MainForm.pas' {Form1};

{$R *.res}

begin
  Application.Initialize;
  Application.CreateForm(TForm1, Form1);
  Application.Run;
end.
