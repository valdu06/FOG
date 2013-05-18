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

package com.sshtools.j2ssh.agent;

import java.io.IOException;

import com.sshtools.j2ssh.io.ByteArrayReader;
import com.sshtools.j2ssh.io.ByteArrayWriter;
import com.sshtools.j2ssh.subsystem.SubsystemMessage;
import com.sshtools.j2ssh.transport.InvalidMessageException;

class SshAgentRandom
    extends SubsystemMessage {
  /**  */
  public static final int SSH_AGENT_RANDOM = 213;
  private int length;

  /**
   * Creates a new SshAgentRandom object.
   */
  public SshAgentRandom() {
    super(SSH_AGENT_RANDOM);
  }

  /**
   * Creates a new SshAgentRandom object.
   *
   * @param length
   */
  public SshAgentRandom(int length) {
    super(SSH_AGENT_RANDOM);
    this.length = length;
  }

  /**
   *
   *
   * @return
   */
  public int getLength() {
    return length;
  }

  /**
   *
   *
   * @return
   */
  public String getMessageName() {
    return "SSH_AGENT_RANDOM";
  }

  /**
   *
   *
   * @param baw
   *
   * @throws java.io.IOException
   * @throws com.sshtools.j2ssh.transport.InvalidMessageException DOCUMENT
   *         ME!
   * @throws InvalidMessageException
   */
  public void constructByteArray(ByteArrayWriter baw) throws java.io.
      IOException,
      com.sshtools.j2ssh.transport.InvalidMessageException {
    try {
      baw.writeInt(length);
    }
    catch (IOException ioe) {
      throw new InvalidMessageException(ioe.getMessage());
    }
  }

  /**
   *
   *
   * @param bar
   *
   * @throws java.io.IOException
   * @throws com.sshtools.j2ssh.transport.InvalidMessageException DOCUMENT
   *         ME!
   * @throws InvalidMessageException
   */
  public void constructMessage(ByteArrayReader bar) throws java.io.IOException,
      com.sshtools.j2ssh.transport.InvalidMessageException {
    try {
      length = (int) bar.readInt();
    }
    catch (IOException ioe) {
      throw new InvalidMessageException(ioe.getMessage());
    }
  }
}
