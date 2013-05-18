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
import com.sshtools.j2ssh.transport.publickey.SshKeyPairFactory;
import com.sshtools.j2ssh.transport.publickey.SshPublicKey;

class SshAgentDeleteKey
    extends SubsystemMessage {
  /**  */
  public static final int SSH_AGENT_DELETE_KEY = 207;
  SshPublicKey pubkey;
  String description;

  /**
   * Creates a new SshAgentDeleteKey object.
   */
  public SshAgentDeleteKey() {
    super(SSH_AGENT_DELETE_KEY);
  }

  /**
   * Creates a new SshAgentDeleteKey object.
   *
   * @param pubkey
   * @param description
   */
  public SshAgentDeleteKey(SshPublicKey pubkey, String description) {
    super(SSH_AGENT_DELETE_KEY);

    this.pubkey = pubkey;
    this.description = description;
  }

  /**
   *
   *
   * @return
   */
  public SshPublicKey getPublicKey() {
    return pubkey;
  }

  /**
   *
   *
   * @return
   */
  public String getDescription() {
    return description;
  }

  /**
   *
   *
   * @return
   */
  public String getMessageName() {
    return "SSH_AGENT_DELETE_KEY";
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
      baw.writeBinaryString(pubkey.getEncoded());
      baw.writeString(description);
    }
    catch (IOException ex) {
      throw new InvalidMessageException(ex.getMessage());
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
      pubkey = SshKeyPairFactory.decodePublicKey(bar.readBinaryString());
      description = bar.readString();
    }
    catch (IOException ex) {
      throw new InvalidMessageException(ex.getMessage());
    }
  }
}
