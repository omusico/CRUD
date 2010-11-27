--- CRUD Libraries 0.1 beta -------
Admin area Scaffolding libraries for Zend Framework 1.x
Tested with Zend Framework v1.8 - v1.11
--------------------------

This is a naked zend framework application (built with zend tool)
with the minimum settings and files to make working the sample application


-----------------------------------
 HOW TO ADD A NEW CRUD UNIT (model+controller+views)

1) create MODEL extending Crud_Model_DbTable_Abstract
set at least $_name pointing to the existing sql table
2) create FORM extending Crud_Forms_Abstract
2) create CONTROLLER extending
3) (optional) customize view scripts (index, add, delete, update)







-----------------------
   FILES TO ADD TO AN EXISTING APP
-----------------------

In case you want to use these functionalities on an EXISTING zend app,
here it is a list of the files/settings added

- application.ini
autoloadernamespaces[] = Crud
- whole directory library/Crud
-