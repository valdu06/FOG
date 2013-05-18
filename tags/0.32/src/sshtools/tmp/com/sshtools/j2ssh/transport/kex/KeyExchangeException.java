//Changes (c) STFC 2007
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

package com.sshtools.j2ssh.transport.kex;

import com.sshtools.j2ssh.transport.TransportProtocolException;

/**
 *
 *
 * @author $author$
 * @version $Revision: 1.2 $
 */
public class KeyExchangeException
    extends TransportProtocolException {
  /**
   * Creates a new KeyExchangeException object.
   *
   * @param msg
   */
  public KeyExchangeException(String msg) {
    super(msg);
  }

  /**
   * Creates a new KeyExchangeException object.
   *
   * @param msg
   * @param ex
   */
  public KeyExchangeException(String msg, Throwable ex) {
    super(msg);
    exc = ex;
  }

    Throwable exc = null;

    public Throwable getCause() {
	return exc;
    }
}
