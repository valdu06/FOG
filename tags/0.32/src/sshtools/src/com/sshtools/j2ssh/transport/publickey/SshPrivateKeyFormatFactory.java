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

package com.sshtools.j2ssh.transport.publickey;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Vector;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import com.sshtools.j2ssh.configuration.ConfigurationException;
import com.sshtools.j2ssh.configuration.ConfigurationLoader;
import java.util.Enumeration;
import java.util.Properties;
import java.net.URL;
import java.io.InputStream;
import java.util.Vector;
import com.sshtools.j2ssh.io.IOUtil;

/**
 *
 *
 * @author $author$
 * @version $Revision: 1.2 $
 */
public class SshPrivateKeyFormatFactory {
  private static String defaultFormat;
  private static HashMap formatTypes;
  private static Log log = LogFactory.getLog(SshPrivateKeyFormatFactory.class);
  private static Vector types;

  static {

    log.info("Loading private key formats");

    List formats = new ArrayList();
    types = new Vector();
    formatTypes = new HashMap();
    formats.add(SshtoolsPrivateKeyFormat.class.getName());
    defaultFormat = "SSHTools-PrivateKey-Base64Encoded";

    try {

      Enumeration enumr =
          ConfigurationLoader.getExtensionClassLoader().getResources("j2ssh.privatekey");
      URL url;
      Properties properties = new Properties();
      InputStream in;
      while(enumr!=null && enumr.hasMoreElements()) {
        url = (URL)enumr.nextElement();
        in = url.openStream();
        properties.load(in);
        IOUtil.closeStream(in);
        int num = 1;
        String name = "";
        Class cls;
        while(properties.getProperty("privatekey.name."
                                     + String.valueOf(num))
              != null) {
            name = properties.getProperty("privatekey.name."
                                          + String.valueOf(num));
            formats.add(properties.getProperty("privatekey.class."
                                               + String.valueOf(num)));



          num++;
        }
      }
    }
    catch (Throwable t) {
      log.info("Failed to load private extension key formats", t);
    }

    SshPrivateKeyFormat f;

    Iterator it = formats.iterator();
    String classname;

    while (it.hasNext()) {
      classname = (String) it.next();

      try {
        Class cls = ConfigurationLoader.getExtensionClass(classname);
        f = (SshPrivateKeyFormat) cls.newInstance();
        log.debug("Installing " + f.getFormatType()
                  + " private key format");
        formatTypes.put(f.getFormatType(), cls);
        types.add(f.getFormatType());
      }
      catch (Throwable t) {
        log.warn("Private key format implemented by " + classname
                 + " will not be available", t);
      }
    }


  }

  /**
   *
   *
   * @return
   */
  public static List getSupportedFormats() {
    return types;
  }


  public static void initialize() {

  }

  /**
   *
   *
   * @param type
   *
   * @return
   *
   * @throws InvalidSshKeyException
   */
  public static SshPrivateKeyFormat newInstance(String type) throws
      InvalidSshKeyException {
    try {
      if (formatTypes.containsKey(type)) {
        return (SshPrivateKeyFormat) ( (Class) formatTypes.get(type))
            .newInstance();
      }
      else {
        throw new InvalidSshKeyException("The format type " + type
                                         + " is not supported");
      }
    }
    catch (IllegalAccessException iae) {
      throw new InvalidSshKeyException(
          "Illegal access to class implementation of " + type);
    }
    catch (InstantiationException ie) {
      throw new InvalidSshKeyException(
          "Failed to create instance of format type " + type);
    }
  }

  /**
   *
   *
   * @return
   */
  public static String getDefaultFormatType() {
    return defaultFormat;
  }
}
