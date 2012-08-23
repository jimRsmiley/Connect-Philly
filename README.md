Connect Philly
==============

Connect Philly is a tool and website designed by Technically Philly for locating computer 
center locations in the Philadelphia area.  Text an address to (215) 240 - 7296 
and receive the closest computer center.

Computer centers may be added to the database on Connect Philly's website. <http://connect.technicallyphilly.com>

The in-progress mobile website is at <http://connect.technicallyphilly.com/m>

Implementation
==============

The SMS text message API is provided by [SMSified](http://smsified.com).

The code was developed with PHP 5.3.8 and the Zend Framework 1.11.

The computer centers are stored in a Google Fusion Table currently 
located [here](https://www.google.com/fusiontables/DataSource?snapid=S467324OMhp).

To Do
======

2. do something with the smsified callback functionality.  Right now the system
just records that an sms delivery seemingly failed.  Although there have been
instances where the message did succeed but smsified notified a delivery failure.

Team
====

Connect Philly is the vision of Technically Philly and the Freedom Rings 
Partnership, implemented by [Jim Smiley](http://twitter.com/jimRsmiley).
