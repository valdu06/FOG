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

package com.sshtools.j2ssh.transport;

import com.sshtools.j2ssh.SshException;

/**
 * <p>
 * Thrown by message store when a message is already registered
 * </p>
 *
 * @author Lee David Painter
 * @version $Revision: 1.1.1.1 $
 *
 * @since 0.2.0
 */
public class MessageAlreadyRegisteredException
    extends SshException {
  /**
   * <p>
   * Constructs the exception.
   * </p>
   *
   * @param messageId the id of the message already registered
   *
   * @since 0.2.0
   */
  public MessageAlreadyRegisteredException(Integer messageId) {
    super("Message Id " + messageId.toString() + " is already registered");
  }
}
