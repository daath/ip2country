/*****************************************************************************
//
// IP2Country benchmark app for IP2Country module
//
// Copyright (C) 2004  L. Petersen, Weird Silence, www.weirdsilence.net
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
*****************************************************************************/

using System;
using System.IO;
using System.Windows.Forms;
using WeirdSilence.Net.Ip2Country;

namespace MyForm {
    public class CreatedForm : System.Windows.Forms.Form
    {
        private System.Windows.Forms.Label listinfo;
        private System.Windows.Forms.Label perSec;
        private System.Windows.Forms.CheckBox cbUnresolvedOnly;
        private System.Windows.Forms.Button button;
        private System.Windows.Forms.CheckBox cbOutput;
        private System.Windows.Forms.ListBox serverlist;
        private ip2country ic;
        private String[] ips;
        private String[] argv;
        
        public CreatedForm(String[] args)
        {
            argv = args;
            InitializeComponent();
        }
        
        // THIS METHOD IS MAINTAINED BY THE FORM DESIGNER
        // DO NOT EDIT IT MANUALLY! YOUR CHANGES ARE LIKELY TO BE LOST
        void InitializeComponent() {
            this.serverlist = new System.Windows.Forms.ListBox();
            this.cbOutput = new System.Windows.Forms.CheckBox();
            this.button = new System.Windows.Forms.Button();
            this.cbUnresolvedOnly = new System.Windows.Forms.CheckBox();
            this.perSec = new System.Windows.Forms.Label();
            this.listinfo = new System.Windows.Forms.Label();
            this.SuspendLayout();
            // 
            // serverlist
            // 
            this.serverlist.Location = new System.Drawing.Point(8, 64);
            this.serverlist.Name = "serverlist";
            this.serverlist.Size = new System.Drawing.Size(400, 381);
            this.serverlist.TabIndex = 2;
            // 
            // cbOutput
            // 
            this.cbOutput.Location = new System.Drawing.Point(8, 40);
            this.cbOutput.Name = "cbOutput";
            this.cbOutput.TabIndex = 5;
            this.cbOutput.Text = "Disable output";
            this.cbOutput.CheckedChanged += new System.EventHandler(this.CbOutputCheckedChanged);
            // 
            // button
            // 
            this.button.Location = new System.Drawing.Point(328, 16);
            this.button.Name = "button";
            this.button.TabIndex = 0;
            this.button.Text = "Bechmark";
            this.button.Click += new System.EventHandler(this.ButtonClick);
            // 
            // cbUnresolvedOnly
            // 
            this.cbUnresolvedOnly.Location = new System.Drawing.Point(112, 40);
            this.cbUnresolvedOnly.Name = "cbUnresolvedOnly";
            this.cbUnresolvedOnly.Size = new System.Drawing.Size(160, 24);
            this.cbUnresolvedOnly.TabIndex = 6;
            this.cbUnresolvedOnly.Text = "Only show unresolved IPs";
            // 
            // perSec
            // 
            this.perSec.Location = new System.Drawing.Point(8, 24);
            this.perSec.Name = "perSec";
            this.perSec.Size = new System.Drawing.Size(312, 16);
            this.perSec.TabIndex = 4;
            // 
            // listinfo
            // 
            this.listinfo.Location = new System.Drawing.Point(8, 8);
            this.listinfo.Name = "listinfo";
            this.listinfo.Size = new System.Drawing.Size(312, 16);
            this.listinfo.TabIndex = 1;
            // 
            // CreatedForm
            // 
            this.AutoScaleBaseSize = new System.Drawing.Size(5, 13);
            this.ClientSize = new System.Drawing.Size(416, 476);
            this.Controls.Add(this.cbUnresolvedOnly);
            this.Controls.Add(this.cbOutput);
            this.Controls.Add(this.perSec);
            this.Controls.Add(this.serverlist);
            this.Controls.Add(this.listinfo);
            this.Controls.Add(this.button);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.MaximizeBox = false;
            this.Name = "CreatedForm";
            this.Load += new System.EventHandler(this.CreatedFormLoad);
            this.ResumeLayout(false);
        }
        void CreatedFormLoad(object sender, System.EventArgs e)
        {
            int n;
            using ( StreamReader sr = new StreamReader( "Servers.lst" ))
            {
                String line;
                while ( ( line = sr.ReadLine() ) != null )
                {
                    //if ( !serverlist.Items.Contains( line ) )
                        serverlist.Items.Add( line );
                }
                n = serverlist.Items.Count;
                ips = new String[ n ];
                serverlist.Items.CopyTo( ips, 0 );
                listinfo.Text = "There are " + n.ToString() + " unique IPs in the list.";
                sr.Close();
            }
            /*
            using ( StreamWriter sw = new StreamWriter( "Servers.lst" ))
            {
                for ( i = 0; i < n; i++ )
                    sw.WriteLine( ips[ i ] );
                sw.Close();
            }
            */
            bool cacheIt = true;
            if (argv.Length == 1)
                if (argv[0] == "/nocache")
                    cacheIt = false;
            ic = new ip2country( "ip2cntry.dat", cacheIt );
        }
        
        void ButtonClick(object sender, System.EventArgs e)
        {
            int cnt = 0;
            button.Enabled = false;
            if (!cbOutput.Checked)
                serverlist.Items.Clear();
            int n = ips.Length;
            TimeSpan span;
            DateTime end;
            DateTime start = DateTime.Now;
            int[] result = new int[n];
            for ( int i = 0; i < n; i++ )
                result[ i ] = ic.lookup( ips[ i ] );
            end = DateTime.Now;
            span = new TimeSpan( end.Ticks - start.Ticks );
            perSec.Text = span.TotalSeconds.ToString() + " secs runtime. " + Math.Round(n / span.TotalSeconds).ToString() + " lookups per second.";
            if (!cbOutput.Checked)
                for ( int i = 0; i < n; i++ )
                    switch ( result[ i ] )
                    {
                        case -1:
                            goto case -2;
                        case -2:
                            cnt++;
                            serverlist.Items.Add( cnt.ToString() + ". " + ips[ i ] + " = N/A" );
                            break;
                        default:
                            if (!cbUnresolvedOnly.Checked)
                            {
                                cnt++;
                                serverlist.Items.Add( cnt.ToString() + ". " + ips[ i ] + " = " + ic.countryCode( result[ i ] ) );
                            }
                            break;
                    }
            button.Enabled = true;
        }

        public static void Main(String[] argv)
        {
            Application.Run(new CreatedForm(argv));
        }

        void CbOutputCheckedChanged(object sender, System.EventArgs e)
        {
            cbUnresolvedOnly.Enabled = !cbOutput.Checked;
            serverlist.Enabled = !cbOutput.Checked;
        }

    }
}
