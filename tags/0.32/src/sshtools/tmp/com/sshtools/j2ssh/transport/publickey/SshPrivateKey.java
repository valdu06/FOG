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

package com.sshtools.j2ssh.transport.publickey;

/**
 *
 *
 * @author $author$
 * @version $Revision: 1.1.1.1 $
 */
public abstract class SshPrivateKey {
  /**
   * Creates a new SshPrivateKey object.
   */
  public SshPrivateKey() {
  }

  /**
   *
   *
   * @return
   */
  public abstract String getAlgorithmName();

  /**
   *
   *
   * @return
   */
  public abstract int getBitLength();

  /**
   *
   *
   * @return
   */
  public abstract byte[] getEncoded();

  /**
   *
   *
   * @return
   */
  public abstract SshPublicKey getPublicKey();

  /**
   *
   *
   * @param data
   *
   * @return
   */
  public abstract byte[] generateSignature(byte[] data) throws
      InvalidSshKeySignatureException;
}
