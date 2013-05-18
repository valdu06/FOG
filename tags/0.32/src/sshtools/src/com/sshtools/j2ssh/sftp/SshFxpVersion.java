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

package com.sshtools.j2ssh.sftp;

import java.io.IOException;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;

import com.sshtools.j2ssh.io.ByteArrayReader;
import com.sshtools.j2ssh.io.ByteArrayWriter;
import com.sshtools.j2ssh.io.UnsignedInteger32;
import com.sshtools.j2ssh.subsystem.SubsystemMessage;
import com.sshtools.j2ssh.transport.InvalidMessageException;

/**
 *
 *
 * @author $author$
 * @version $Revision: 1.1.1.1 $
 */
public class SshFxpVersion
    extends SubsystemMessage {
  /**  */
  public static final int SSH_FXP_VERSION = 2;
  private UnsignedInteger32 version;
  private Map extended = null;

  /**
   * Creates a new SshFxpVersion object.
   */
  public SshFxpVersion() {
    super(SSH_FXP_VERSION);
  }

  /**
   * Creates a new SshFxpVersion object.
   *
   * @param version
   * @param extended
   */
  public SshFxpVersion(UnsignedInteger32 version, Map extended) {
    super(SSH_FXP_VERSION);
    this.version = version;
    this.extended = extended;
  }

  /**
   *
   *
   * @return
   */
  public UnsignedInteger32 getVersion() {
    return version;
  }

  /**
   *
   *
   * @return
   */
  public Map getExtended() {
    return extended;
  }

  /**
   *
   *
   * @param bar
   *
   * @throws IOException
   * @throws InvalidMessageException
   */
  public void constructMessage(ByteArrayReader bar) throws IOException,
      InvalidMessageException {
    version = bar.readUINT32();
    extended = new HashMap();

    String key;
    String value;

    while (bar.available() > 0) {
      key = bar.readString();
      value = bar.readString();
      extended.put(key, value);
    }
  }

  /**
   *
   *
   * @return
   */
  public String getMessageName() {
    return "SSH_FXP_INIT";
  }

  /**
   *
   *
   * @param baw
   *
   * @throws IOException
   * @throws InvalidMessageException
   */
  public void constructByteArray(ByteArrayWriter baw) throws IOException,
      InvalidMessageException {
    baw.writeUINT32(version);

    if (extended != null) {
      if (extended.size() > 0) {
        Iterator it = extended.entrySet().iterator();
        Map.Entry entry;

        while (it.hasNext()) {
          entry = (Map.Entry) it.next();
          baw.writeString( (String) entry.getKey());
          baw.writeString( (String) entry.getValue());
        }
      }
    }
  }
}
