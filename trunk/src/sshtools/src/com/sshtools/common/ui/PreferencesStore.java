//Changes (c) STFC/CCLRC 2006-2007
/*
 *  SSHTools - Java SSH2 API
 *
 *  Copyright (C) 2002 Lee David Painter.
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Library General Public License
 *  as published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 *
 *  You may also distribute it and/or modify it under the terms of the
 *  Apache style J2SSH Software License. A copy of which should have
 *  been provided with the distribution.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  License document supplied with your distribution for more details.
 *
 */


/*
 * Note this is now where the following defaults are set:
 *
 * At compile-time the file res/common/default.properties is included in
 * in the distribution and this is read first, then the user' preferences
 * file (~/.sshterm/GSI-SSHTerm.settings), which can over-ride those in
 * the res/common/default.properties file, lastly 
 * ~/.sshterm/GSI-SSHTerm.properties is the file for automatic settings
 * saved by the terminal.
 *
 *
 */

package com.sshtools.common.ui;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.Properties;
import java.util.StringTokenizer;

import java.awt.Rectangle;
import javax.swing.JTable;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

/**
 *
 *
 * @author $author$
 * @version $Revision: 1.6 $
 */
public class PreferencesStore {
  /**  */
  protected static Log log = LogFactory.getLog(PreferencesStore.class);

  //
  private static File file;
  private static boolean storeAvailable;
  private static Properties preferences;
  private static Properties userDefaults;
  private static Properties defaultPreferences;

  //  Intialise the preferences
  static {
      userDefaults = new Properties();
      preferences = new Properties();
      defaultPreferences = new Properties();
  }

  /**
   *
   *
   * @param table
   * @param pref
   */
  public static void saveTableMetrics(JTable table, String pref) {
    for (int i = 0; i < table.getColumnModel().getColumnCount(); i++) {
      int w = table.getColumnModel().getColumn(i).getWidth();
      put(pref + ".column." + i + ".width", String.valueOf(w));
      put(pref + ".column." + i + ".position",
          String.valueOf(table.convertColumnIndexToModel(i)));
    }
  }

  /**
   *
   *
   * @param table
   * @param pref
   * @param defaultWidths
   *
   * @throws IllegalArgumentException
   */
  public static void restoreTableMetrics(JTable table, String pref,
                                         int[] defaultWidths) {
    //  Check the table columns may be resized correctly
    if (table.getAutoResizeMode() != JTable.AUTO_RESIZE_OFF) {
      throw new IllegalArgumentException(
          "Table AutoResizeMode must be JTable.AUTO_RESIZE_OFF");
    }

    //  Restore the table column widths and positions
    for (int i = 0; i < table.getColumnModel().getColumnCount(); i++) {
      try {
        table.moveColumn(table.convertColumnIndexToView(getInt(pref
            + ".column." + i + ".position", i)), i);
        table.getColumnModel().getColumn(i).setPreferredWidth(getInt(pref
            + ".column." + i + ".width",
            (defaultWidths == null)
            ? table.getColumnModel().getColumn(i).getPreferredWidth()
            : defaultWidths[i]));
      }
      catch (NumberFormatException nfe) {
      }
    }
  }

  /**
   *
   *
   * @return
   */
  public static boolean isStoreAvailable() {
    return storeAvailable;
  }

  /**
   *
   *
   * @param file
   */
  public static void init(File file) {
    PreferencesStore.file = file;

    //  Make sure the preferences directory exists, creating it if it doesn't
    File dir = file.getParentFile();

    if (!dir.exists()) {
      log.info("Creating SSHTerm preferences directory "
               + dir.getAbsolutePath());

      if (!dir.mkdirs()) {
        log.error("Preferences directory " + dir.getAbsolutePath()
                  + " could not be created. "
                  + "Preferences will not be stored");
      }
    }

    storeAvailable = dir.exists();

    try {
	log.info("loading default.properties");
	InputStream is = PreferencesStore.class.getResourceAsStream("/default.properties");

	if(is!=null) {
	    defaultPreferences.load(is);
	    is.close();
	    log.info("loaded default.properties");
	} else {
	    log.info("no default.properties");
	}
    }catch (IOException ioe) {
          log.error(ioe);
    }

    try {
	log.info("loading user defualts");
	File up = new File(dir, "GSI-SSHTerm.settings");
	InputStream is = new FileInputStream(up);

	if(is!=null) {
	    userDefaults.load(is);
	    is.close();
	    log.info("loaded user defualts");
	} else {
	    log.info("no user defualts");
	}
    }catch (IOException ioe) {
          log.warn("Could not load user defaults: "+ioe);
    }

    //  If the preferences file exists, then load it
    if (storeAvailable) {
      if (file.exists()) {
        InputStream in = null;

        try {
          in = new FileInputStream(file);
          preferences.load(in);
          storeAvailable = true;
        }
        catch (IOException ioe) {
          log.error(ioe);
        }
        finally {
          if (in != null) {
            try {
              in.close();
            }
            catch (IOException ioe) {
            }
          }
        }
      }
      //  Otherwise create it
      else {
        savePreferences();
      }
    }
    else {
      log.warn("Preferences store not available.");
    }
  }

  /**
   *
   */
  public static void savePreferences() {
    if (file == null) {
      log.error(
          "Preferences not saved as PreferencesStore has not been initialise.");
    }
    else {
      OutputStream out = null;

      try {
        out = new FileOutputStream(file);
        preferences.store(out, "SSHTerm preferences");
        log.info("Preferences written to " + file.getAbsolutePath());
        storeAvailable = true;
      }
      catch (IOException ioe) {
        log.error(ioe);
      }
      finally {
        if (out != null) {
          try {
            out.close();
          }
          catch (IOException ioe) {
          }
        }
      }
    }
  }

  /**
   *
   *
   * @param name
   * @param def
   *
   * @return
   */
  public static String get(String name, String def) {
    String a = preferences.getProperty(name, null);
    if(a==null) a = userDefaults.getProperty(name, null);
    if(a==null) {
	return defaultPreferences.getProperty(name, def);
    } else return a;
  }

  /**
   *
   *
   * @param name
   *
   * @return
   */
  public static String get(String name) {
      return get(name, null);
  }


  /**
   *
   *
   * @param name
   * @param val
   */
  public static void put(String name, String val) {
    preferences.put(name, val);
  }

  /**
   *
   *
   * @param name
   * @param def
   *
   * @return
   */
  public static Rectangle getRectangle(String name, Rectangle def) {
    String s = get(name);

    if ( (s == null) || s.equals("")) {
      return def;
    }
    else {
      StringTokenizer st = new StringTokenizer(s, ",");
      Rectangle r = new Rectangle();

      try {
        r.x = Integer.parseInt(st.nextToken());
        r.y = Integer.parseInt(st.nextToken());
        r.width = Integer.parseInt(st.nextToken());
        r.height = Integer.parseInt(st.nextToken());
      }
      catch (NumberFormatException nfe) {
        log.warn("Preference is " + name + " is badly formatted", nfe);
      }

      return r;
    }
  }

  /**
   *
   *
   * @param name
   * @param val
   */
  public static void putRectangle(String name, Rectangle val) {
    preferences.put(name,
                    (val == null) ? ""
                    : (val.x + "," + val.y + "," + val.width + ","
                       + val.height));
  }

  /**
   *
   *
   * @param name
   * @param def
   *
   * @return
   */
  public static int getInt(String name, int def) {
    String s = get(name);

    if ( (s != null) && !s.equals("")) {
      try {
        return Integer.parseInt(s);
      }
      catch (NumberFormatException nfe) {
        log.warn("Preference is " + name + " is badly formatted", nfe);
      }
    }

    return def;
  }

  /**
   *
   *
   * @param name
   * @param def
   *
   * @return
   */
  public static double getDouble(String name, double def) {
    String s = get(name);

    if ( (s != null) && !s.equals("")) {
      try {
        return Double.parseDouble(s);
      }
      catch (NumberFormatException nfe) {
        log.warn("Preference is " + name + " is badly formatted", nfe);
      }
    }

    return def;
  }

  /**
   *
   *
   * @param name
   * @param val
   */
  public static void putInt(String name, int val) {
    preferences.put(name, String.valueOf(val));
  }

  /**
   *
   *
   * @param name
   * @param val
   */
  public static void putDouble(String name, double val) {
    preferences.put(name, String.valueOf(val));
  }

  /**
   *
   *
   * @param name
   * @param def
   *
   * @return
   */
  public static boolean getBoolean(String name, boolean def) {
    return get(name, String.valueOf(def)).equals("true");
  }

  /**
   *
   *
   * @param name
   * @param val
   */
  public static void putBoolean(String name, boolean val) {
    preferences.put(name, String.valueOf(val));
  }

  /**
   *
   *
   * @param name
   *
   * @return
   */
  public static boolean preferenceExists(String name) {
    return preferences.containsKey(name) || defaultPreferences.containsKey(name) || userDefaults.containsKey(name);
  }

  /**
   *
   *
   * @param name
   *
   * @return
   */
  public static boolean removePreference(String name) {
    boolean exists = preferenceExists(name);
    preferences.remove(name);
    return exists;
  }
}
