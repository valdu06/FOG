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

package com.sshtools.common.mru;

import java.io.File;

import java.awt.Component;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import javax.swing.Action;
import javax.swing.JMenu;
import javax.swing.JMenuItem;
import javax.swing.event.ListDataEvent;
import javax.swing.event.ListDataListener;

/**
 *
 *
 * @author $author$
 * @version $Revision: 1.1.1.1 $
 */
public class MRUMenu
    extends JMenu
    implements ListDataListener, ActionListener {
  private MRUListModel model;

  /**
   * Creates a new MRUMenu object.
   *
   * @param action
   * @param model
   */
  protected MRUMenu(Action action, MRUListModel model) {
    super(action);
    init(model);
  }

  /**
   * Creates a new MRUMenu object.
   *
   * @param text
   * @param model
   */
  protected MRUMenu(String text, MRUListModel model) {
    super(text);
    init(model);
  }

  private void init(MRUListModel model) {
    this.model = model;
    rebuildMenu();
    model.addListDataListener(this);
  }

  /**
   *
   */
  public void cleanUp() {
    model.removeListDataListener(this);
  }

  /**
   *
   *
   * @param e
   */
  public void intervalAdded(ListDataEvent e) {
    rebuildMenu();
  }

  /**
   *
   *
   * @param e
   */
  public void intervalRemoved(ListDataEvent e) {
    rebuildMenu();
  }

  /**
   *
   *
   * @param e
   */
  public void contentsChanged(ListDataEvent e) {
    rebuildMenu();
  }

  /**
   *
   *
   * @param evt
   */
  public void actionPerformed(ActionEvent evt) {
    fireActionPerformed(evt);
  }

  private void rebuildMenu() {
    Component[] c = getMenuComponents();

    for (int i = 0; (c != null) && (i < c.length); i++) {
      ( (JMenuItem) c[i]).removeActionListener(this);
      remove(c[i]);
    }

    for (int i = 0; i < model.getSize(); i++) {
      File f = (File) model.getElementAt(i);
      JMenuItem m = new JMenuItem(f.getName());
      m.setActionCommand(f.getAbsolutePath());
      m.setToolTipText(f.getAbsolutePath());
      m.addActionListener(this);
      add(m);
    }

    setEnabled(model.getSize() > 0);
    validate();
  }
}
