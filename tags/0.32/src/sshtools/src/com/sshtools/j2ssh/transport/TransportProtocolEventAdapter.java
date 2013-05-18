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

/**
 * <p>
 * Title:
 * </p>
 *
 * <p>
 * Description:
 * </p>
 *
 * <p>
 * Copyright: Copyright (c) 2003
 * </p>
 *
 * <p>
 * Company:
 * </p>
 *
 * @author Lee David Painter
 * @version $Id: TransportProtocolEventAdapter.java,v 1.1.1.1 2005/12/23 14:24:43 mv23 Exp $
 */
public class TransportProtocolEventAdapter
    implements TransportProtocolEventHandler {
  /**
   * Creates a new TransportProtocolEventAdapter object.
   */
  public TransportProtocolEventAdapter() {
  }

  /**
   *
   *
   * @param transport
   */
  public void onSocketTimeout(TransportProtocol transport) {
  }

  /**
   *
   *
   * @param transport
   */
  public void onDisconnect(TransportProtocol transport) {
  }

  /**
   *
   *
   * @param transport
   */
  public void onConnected(TransportProtocol transport) {
  }
}
