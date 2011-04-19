object Form1: TForm1
  Left = 361
  Top = 250
  BorderIcons = [biSystemMenu]
  BorderStyle = bsSingle
  Caption = 'IP to Country'
  ClientHeight = 132
  ClientWidth = 261
  Color = clBtnFace
  Font.Charset = DEFAULT_CHARSET
  Font.Color = clWindowText
  Font.Height = -11
  Font.Name = 'MS Sans Serif'
  Font.Style = []
  OldCreateOrder = False
  Position = poScreenCenter
  OnCreate = FormCreate
  OnDestroy = FormDestroy
  PixelsPerInch = 96
  TextHeight = 13
  object Label1: TLabel
    Left = 16
    Top = 16
    Width = 54
    Height = 13
    Caption = 'IP Address:'
  end
  object lblResult: TLabel
    Left = 16
    Top = 80
    Width = 145
    Height = 41
    AutoSize = False
    Font.Charset = DEFAULT_CHARSET
    Font.Color = clWindowText
    Font.Height = -11
    Font.Name = 'MS Sans Serif'
    Font.Style = []
    ParentFont = False
    WordWrap = True
  end
  object Image1: TImage
    Left = 200
    Top = 78
    Width = 20
    Height = 20
    Center = True
  end
  object lblCC: TLabel
    Left = 176
    Top = 82
    Width = 5
    Height = 13
    Font.Charset = DEFAULT_CHARSET
    Font.Color = clWindowText
    Font.Height = -11
    Font.Name = 'MS Sans Serif'
    Font.Style = [fsBold]
    ParentFont = False
  end
  object btnLookup: TButton
    Left = 175
    Top = 32
    Width = 75
    Height = 25
    Caption = 'Lookup'
    Default = True
    Enabled = False
    TabOrder = 1
    OnClick = btnLookupClick
  end
  object txtIpAddress: TEdit
    Left = 16
    Top = 32
    Width = 145
    Height = 21
    TabOrder = 0
    OnChange = txtIpAddressChange
    OnEnter = txtIpAddressEnter
  end
  object chkDownloadFlag: TCheckBox
    Left = 16
    Top = 56
    Width = 153
    Height = 17
    Caption = 'Download flags for country.'
    Checked = True
    State = cbChecked
    TabOrder = 2
  end
end
