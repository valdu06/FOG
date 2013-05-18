//Changes (c) STFC 2007
/*
 *  Sshtools - SSHTerm
 *
 *  The contents of this package have been derived from the Java
 *  Telnet/SSH Applet from http://javassh.org. The files have been
 *  modified and are supplied under the terms of the original license.
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Library General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public
 *  License along with this program; if not, write to the Free Software
 *  Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */
package com.sshtools.sshterm.emulation;

import javax.swing.JScrollBar;

public interface VDUDisplay {
  public void checkedClearSelection();

  public void redraw();

  public void setVDUBuffer(VDUBuffer buffer);

  public VDUBuffer getVDUBuffer();

  public JScrollBar getScrollBar();
}
