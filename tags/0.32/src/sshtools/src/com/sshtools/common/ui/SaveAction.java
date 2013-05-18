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

import java.awt.event.KeyEvent;
import javax.swing.Action;
import javax.swing.KeyStroke;

/**
 *
 *
 * @author $author$
 * @version $Revision: 1.1.1.1 $
 */
public class SaveAction
    extends StandardAction {
  private final static String ACTION_COMMAND_KEY_SAVE = "save-command";
  private final static String NAME_SAVE = "Save";
  private final static String SMALL_ICON_SAVE =
      "/com/sshtools/common/ui/save.png";
  private final static String LARGE_ICON_SAVE = "";
  private final static String SHORT_DESCRIPTION_SAVE =
      "Save the current connection";
  private final static String LONG_DESCRIPTION_SAVE =
      "Save the current connection properties to file";
  private final static int MNEMONIC_KEY_SAVE = 'S';

  /**
   * Creates a new SaveAction object.
   */
  public SaveAction() {
    putValue(Action.NAME, NAME_SAVE);
    putValue(Action.SMALL_ICON, getIcon(SMALL_ICON_SAVE));
    putValue(LARGE_ICON, getIcon(LARGE_ICON_SAVE));
    putValue(Action.SHORT_DESCRIPTION, SHORT_DESCRIPTION_SAVE);
    putValue(Action.ACCELERATOR_KEY,
             KeyStroke.getKeyStroke(KeyEvent.VK_S, KeyEvent.ALT_MASK));
    putValue(Action.LONG_DESCRIPTION, LONG_DESCRIPTION_SAVE);
    putValue(Action.MNEMONIC_KEY, new Integer(MNEMONIC_KEY_SAVE));
    putValue(Action.ACTION_COMMAND_KEY, ACTION_COMMAND_KEY_SAVE);
    putValue(StandardAction.MENU_NAME, "File");
    putValue(StandardAction.MENU_ITEM_GROUP, new Integer(0));
    putValue(StandardAction.ON_MENUBAR, new Boolean(true));
    putValue(StandardAction.MENU_ITEM_WEIGHT, new Integer(50));
    putValue(StandardAction.ON_TOOLBAR, new Boolean(true));
    putValue(StandardAction.TOOLBAR_GROUP, new Integer(0));
    putValue(StandardAction.TOOLBAR_WEIGHT, new Integer(20));
  }
}
