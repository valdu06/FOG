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

package com.sshtools.j2ssh;

/**
 * <p>
 * Runtime exception's thrown by the J2SSH application framework.
 * </p>
 *
 * @author Lee David Painter
 * @version $Revision: 1.1.1.1 $
 *
 * @since 0.2.0
 */
public class SshRuntimeException
    extends RuntimeException {
  /**
   * <p>
   * Constructs a runtime exception.
   * </p>
   *
   * @param msg the error message
   *
   * @since 0.2.0
   */
  public SshRuntimeException(String msg) {
    super(msg);
  }
}
