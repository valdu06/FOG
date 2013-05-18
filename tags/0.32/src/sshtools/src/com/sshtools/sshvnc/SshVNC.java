//Changes (c) STFC/CCLRC 2007
/*
 *  Sshtools - SshVNC
 *
 *  Copyright (C) 2002 Lee David Painter.
 *
 *  Written by: 2002 Lee David Painter <lee@sshtools.com>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Library General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public
 *  License along with this program; if not, write to the Free Software
 *  Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */
package com.sshtools.sshvnc;

import java.io.File;
import java.io.IOException;
import java.lang.reflect.Method;
import java.util.Properties;

import javax.swing.Icon;
import javax.swing.JOptionPane;

import com.sshtools.common.configuration.XmlConfigurationContext;
import com.sshtools.common.ui.OptionsTab;
import com.sshtools.common.ui.ResourceIcon;
import com.sshtools.common.ui.SshToolsApplication;
import com.sshtools.j2ssh.configuration.ConfigurationLoader;

public class SshVNC
    extends SshToolsApplication {
  public static File PREF_DIR;
  static {
    String val = ConfigurationLoader.checkAndGetProperty("user.home", null);
    if (val != null) {
      PREF_DIR = new File(val + File.separator + ".sshvnc");
    }
  }

  public SshVNC() {
    super(SshVNCPanel.class, SshVNCFrame.class);
  }

  public File getApplicationPreferencesDirectory() {
    return PREF_DIR;
  }

  public String getApplicationName() {
    return "SSHVnc";
  }

  public String getApplicationVersion() {
    return "sshvnc.properties";
  }

  public Icon getApplicationLargeIcon() {
    return new ResourceIcon("/com/sshtools/shift/largeshift.png");
  }

  public OptionsTab[] getAdditionalOptionTabs() {
    return null;
  }

  public String getAboutLicenseDetails() {
    return "SSHVnc is Licensed under the GPL (http://www.gnu.org/licenses)\n";
  }

  public String getAboutURL() {
    return "http://www.sshtools.com";
  }

  public String getAboutAuthors() {
    return "Lee David Painter\nRichard Pernavas\nBrett Smith";
  }

  public final static void main(String[] args) {
    try {
      /**
       * We use log4j as our logging implementation as it rules but lets not
       * fail if it aint around so we !
       */
      if (System.getProperty("log4j.properties") != null) {

        try {
          Properties properties = new Properties();
          properties.load(ConfigurationLoader.loadFile(System.getProperty(
              "log4j.properties")));

          try {
            Class cls = Class.forName("org.apache.log4j.PropertyConfigurator");
            Object obj = cls.newInstance();
            Method method = cls.getMethod("configure", new Class[] {Properties.class});
            method.invoke(obj, new Object[] {properties});
          }
          catch (Throwable ex) {
          }
        }
        catch (IOException ex) {
          configureBasicLogging();
        }
      }
      else {
        configureBasicLogging();
      }

      XmlConfigurationContext context = new XmlConfigurationContext();

      context.setAutomationConfigurationResource("automation.xml");
      context.setFailOnError(false);
      ConfigurationLoader.initialize(false, context);
      SshVNC sshvnc = new SshVNC();
      sshvnc.init(args);
      sshvnc.newContainer();
    }
    catch (Exception e) {
      e.printStackTrace();
      JOptionPane.showMessageDialog(null, e.getMessage(), "Error",
                                    JOptionPane.ERROR_MESSAGE);
      System.exit(0);
    }
  }

  private static void configureBasicLogging() {
    try {
      Class cls = Class.forName("org.apache.log4j.BasicConfigurator");
      Method method = cls.getMethod("configure", (Class)null);
      method.invoke(null, (Object)null);
    }
    catch (Throwable ex2) {
    }
  }

}
