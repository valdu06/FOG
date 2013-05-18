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

package com.sshtools.daemon.configuration;

/**
 *
 *
 * @author $author$
 * @version $Revision: 1.1.1.1 $
 */
public class AllowedSubsystem {
  private String type;
  private String name;
  private String provider;

  /**
   * Creates a new AllowedSubsystem object.
   *
   * @param type
   * @param name
   * @param provider
   */
  public AllowedSubsystem(String type, String name, String provider) {
    this.type = type;
    this.name = name;
    this.provider = provider;
  }

  /**
   *
   *
   * @return
   */
  public String getType() {
    return type;
  }

  /**
   *
   *
   * @return
   */
  public String getName() {
    return name;
  }

  /**
   *
   *
   * @return
   */
  public String getProvider() {
    return provider;
  }
}
