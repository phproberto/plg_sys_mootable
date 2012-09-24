PLG_SYS_MOOTABLE
==============  

System plugin to enable/disable mootools by menu item.  

When Bootstrap comes to Joomla! users need a tool to avoid Mootools loading by default in Joomla 2.5.x  

Features   
---------------  
* Mootools can be enabled or disabled by default.
* To enable/disable Mootools just edit the menu and set "Mootools enable/disable" to No.

Version 
---------------
**Plugin version:** 1.0.2  


Install
---------------
Clone this repository or just download from:  
[[Download Zip](https://github.com/phproberto/plg_sys_mootable/zipball/master)]  
[[Download tar.gz](https://github.com/phproberto/plg_sys_mootable/tarball/master)]  
Then install normally throught Joomla! Extension Manager as package (if you downloaded the compressed version) or from folder (if you cloned the repository).  

In plugin management search "System - Mootools Enabler/Disabler", enable the plugin and set in its preferences the default Mootools mode.  

When editing a menu you will see a new pane called "Mootools enable/disable". Adjust there if you want to load or not Mootools for this menu item.

Release history 
---------------
v.1.0.2 -> Remove JCaption calls, null window.addEvent calls and add a manual script disabler  
v.1.0.1 -> Added polish language. Thanks to Pawel Frankowski!  
v.1.0.0 -> First stable version  
