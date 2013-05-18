//Changes (c) 2007 STFC
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

import java.io.IOException;

import com.sshtools.j2ssh.util.State;

/**
 *
 *
 * @author $author$
 * @version $Revision: 1.2 $
 */
public class TransportProtocolState
    extends State {
  /**  */
  public final static int UNINITIALIZED = 1;

  /**  */
  public final static int NEGOTIATING_PROTOCOL = 2;

  /**  */
  public final static int PERFORMING_KEYEXCHANGE = 3;

  /**  */
  public final static int CONNECTED = 4;

  /**  */
  public final static int DISCONNECTED = 5;

  /**  */
  public IOException lastError;

  /**  */
  public String reason = "";

  /**
   * Creates a new TransportProtocolState object.
   */
  public TransportProtocolState() {
    super(UNINITIALIZED);
  }

  /**
   *
   *
   * @param lastError
   */
  public void setLastError(IOException lastError) {
    this.lastError = lastError;
  }

  /**
   *
   *
   * @return
   */
  public boolean hasError() {
    return lastError != null;
  }

  /**
   *
   *
   * @return
   */
  public IOException getLastError() {
    return lastError;
  }

  /**
   *
   *
   * @param reason
   */
  public void setDisconnectReason(String reason) {
    this.reason = reason;
  }

  /**
   *
   *
   * @return
   */
  public String getDisconnectReason() {
    return reason;
  }

  /**
   *
   *
   * @param state
   *
   * @return
   */
  public boolean isValidState(int state) {
    return ( (state == UNINITIALIZED) || (state == NEGOTIATING_PROTOCOL)
            || (state == PERFORMING_KEYEXCHANGE) || (state == CONNECTED)
            || (state == DISCONNECTED));
  }
}
