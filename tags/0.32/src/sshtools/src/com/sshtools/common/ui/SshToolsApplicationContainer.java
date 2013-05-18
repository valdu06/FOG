//Changes (c) STFC/CCLRC 2007
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

package com.sshtools.common.ui;

import java.awt.Component;

/**
 *
 *
 * @author $author$
 * @version $Revision: 1.3 $
 */
public interface SshToolsApplicationContainer {
  /**
   *
   *
   * @param application
   * @param panel
   *
   * @throws SshToolsApplicationException
   */
  public void init(SshToolsApplication application,
                   SshToolsApplicationPanel panel) throws
      SshToolsApplicationException;

  /**
   *
   *
   * @return
   */
  public SshToolsApplicationPanel getApplicationPanel();

  /**
   *
   */
  public void closeContainer();

  /**
   *
   *
   * @param visible
   */
  public void setContainerVisible(boolean visible);

  /**
   *
   *
   * @param title
   */
  public void setContainerTitle(String title);

  /**
   *
   *
   * @return
   */
  public boolean isContainerVisible();

    public Component getWindow();
}
