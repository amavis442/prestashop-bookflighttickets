prestashop-booking
==================

Book module for airplane tickets


Prestashop override function is buggy

You can install an override from modules/override but if you want to remove and reinstall the module, then
you will get an error like "Class ControllerOverrideOriginalXXXXXX does not exist".
You will have to manually remove the override from /override/<The override> and
delete /cache/class_index.php file. See http://stackoverflow.com/questions/18996528/how-to-remove-override-when-uninstalling-the-module-in-prestashop
for more information.

Another bug is that not all lines from the override will be copied to /override/filename. This really sucks and makes the whole override from a module
useless :(.

Installation:

1. Copy booking/override/classes/Product to /override/classes/Product.php
2. Delete /modules/booking/override
3. Normal module installation via admin

