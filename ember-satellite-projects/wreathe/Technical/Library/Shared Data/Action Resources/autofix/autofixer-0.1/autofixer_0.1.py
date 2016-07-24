#! /usr/bin/env python
# -*- coding: utf-8 -*-
#Autofixer GUI. Version 0.1, 30 July 2013; derived from the Installer, version 0.11

try:
  import pygtk
  pygtk.require('2.0')
except:
  pass
try:
  import gtk
  import gtk.glade
except:
  print('GTK not available')
  sys.exit(1)

class Autofixer:

  def on_window1_destroy(self, object, data=None):
    print "quit with cancel"
    gtk.main_quit()

  def on_btn_quit_clicked(self, menuitem, data=None):
    print "quit from menu"
    gtk.main_quit()

  def __init__(self):
    self.gladefile = "/usr/bin/wxn/Action Resources/autofix/autofixer-0.1/ui_0.1.glade"
    self.builder = gtk.Builder()
    self.builder.add_from_file(self.gladefile)
    self.builder.connect_signals(self)
    self.window = self.builder.get_object("window1")
    gtk.Window.fullscreen(self.window)

    self.window.show()

if __name__ == "__main__":
  main = Autofixer()
  gtk.main()