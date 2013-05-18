/**
 * SSHTools - Java SSH API The contents of this package has been derived from
 * the TelnetD library available from http://sourceforge.net/projects/telnetd
 * The original license of the source code is as follows: TelnetD library
 * (embeddable telnet daemon) Copyright (C) 2000 Dieter Wimberger This library
 * is free software; you can either redistribute it and/or modify it under the
 * terms of the GNU Lesser General Public License version 2.1,1999 as
 * published by the Free Software Foundation (see copy received along with the
 * library), or under the terms of the BSD-style license received along with
 * this library.
 */
package com.sshtools.daemon.terminal;

/**
 *
 *
 * @author $author$
 * @version $Revision: 1.1.1.1 $
 */
public class xterm
    extends BasicTerminal {
  /**
   *
   *
   * @return
   */
  public boolean supportsSGR() {
    return true;
  }

  //supportsSGR
  public boolean supportsScrolling() {
    return true;
  }

  //supportsScrolling
  public String getName() {
    return "xterm";
  }
}
//class xterm
