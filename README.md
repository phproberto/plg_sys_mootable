PLG_SYS_MOOTABLE
==============  

System plugin to enable/disable mootools by menu item.  

When Bootstrap comes to Joomla! users need a tool to avoid Mootools loading by default in Joomla 2.5.x  

Features   
---------------  
* Mootools can be enabled or disabled by default.
* You can enable/disable Mootools when editing menu items.
* Nulls any window.addEvent calls.  
* Removes JCaption calls.  
* You can enable Mootools for specific components.  
* By default the plugin enables Mootools for frontend conten edition and com_users (login, profile edition, etc.).  

Version 
---------------
**Plugin version:** 1.1.2  

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
v.1.1.2 -> Solve `header` field conflicts with K2. Thanks Joe Campbell  
v.1.1.1 -> Use onBeforeCompileHead trigger for cleanup  
v.1.1.0 -> Improve flexibility of the asset control + easier management  
v.1.0.8 -> Fixed autoenable for content creation & friendly URLs enabled. Autoenable for com_users.  
v.1.0.7 -> Added autocomponent enabling. Thanks to [[Hans Kuijpers!](http://www.linkedin.com/in/hans2103/)]  
v.1.0.6 -> Added dutch language. Thanks to [[Rene Kreijveld!](http://www.renekreijveld.nl/)]  
v.1.0.5 -> Auto mootools enable for content edition. Joomla! 3.0 compatible  
v.1.0.4 -> Added german language. Thanks to [[Johannes Hock!](http://www.adhocgrafx.de/)]  
v.1.0.3 -> Added portuguese (Brasil) language. Thanks to [[Mary Mar Alejo!](http://www.marymaralejo.com/)]
v.1.0.2 -> Remove JCaption calls, null window.addEvent calls and add a manual script disabler  
v.1.0.1 -> Added polish language. Thanks to Pawel Frankowski!  
v.1.0.0 -> First stable version  
