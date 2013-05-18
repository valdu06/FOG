//Changes (c) CCLRC 2006
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

package com.sshtools.j2ssh.authentication;

import java.io.FilePermission;
import com.sshtools.j2ssh.configuration.SshConnectionProperties;
import java.security.AccessControlException;
import java.security.AccessController;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import com.sshtools.j2ssh.configuration.ConfigurationException;
import com.sshtools.j2ssh.configuration.ConfigurationLoader;
import com.sshtools.j2ssh.transport.AlgorithmNotSupportedException;

/**
 *
 *
 * @author $author$
 * @version $Revision: 1.6 $
 */
public class SshAuthenticationClientFactory {
  private static Map auths;
  private static Log log = LogFactory.getLog(SshAuthenticationClientFactory.class);

  /**  */
  public final static String AUTH_PASSWORD = "password";

  /**  */
  public final static String AUTH_PK = "publickey";

  /**  */
  public final static String AUTH_KBI = "keyboard-interactive";

  /**  */
  public final static String AUTH_HOSTBASED = "hostbased";

  /**  */
  public final static String AUTH_GSS = "gssapi";
  /**  */
  public final static String AUTH_EKEYX = "external-keyx";

  static {
    auths = new HashMap();

    log.info("Loading supported authentication methods");

    auths.put(AUTH_PASSWORD, PasswordAuthenticationClient.class);

    //  Only allow key authentication if we are able to open local files
    try {
      if (System.getSecurityManager() != null) {
        AccessController.checkPermission(new FilePermission(
            "<<ALL FILES>>", "read"));
      }

      auths.put(AUTH_PK, PublicKeyAuthenticationClient.class);
    }
    catch (AccessControlException ace) {
      log.info(
          "The security manager prevents use of Public Key Authentication on the client");
    }

    auths.put(AUTH_KBI, KBIAuthenticationClient.class);
    auths.put(AUTH_GSS, com.sshtools.j2ssh.authentication.GSSAuthenticationClient.class);
    auths.put(AUTH_EKEYX, com.sshtools.j2ssh.authentication.EKEYXAuthenticationClient.class);

  }

  /**
   * Creates a new SshAuthenticationClientFactory object.
   */
  protected SshAuthenticationClientFactory() {
  }

  /**
   *
   */
  public static void initialize() {
  }

  /**
   *
   *
   * @return
   */
  public static List getSupportedMethods() {
    // Get the list of ciphers
    ArrayList list = new ArrayList(auths.keySet());

    // Return the list
    return list;
  }

  /**
   *
   *
   * @param methodName
   *
   * @return
   *
   * @throws AlgorithmNotSupportedException
   */
  public static SshAuthenticationClient newInstance(String methodName, SshConnectionProperties properties) throws
      AlgorithmNotSupportedException {
    try {
	SshAuthenticationClient n = (SshAuthenticationClient) ( (Class) auths.get(methodName)).newInstance();
	n.setProperties(properties);
	return n;
    }
    catch (Exception e) {
	e.printStackTrace();
      throw new AlgorithmNotSupportedException(methodName
                                               + " is not supported!");
    }
  }
}

