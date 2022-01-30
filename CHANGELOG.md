# CHANGELOG

## Version 1.5
- Support for the purchase unit maintained in Grocy has been added by andreheuer (Big thanks!)
  - "UNIT" in .env is not required anymore


## Version 1.4
- Now the shopping list can be the source for BRING instead of the missing products. 
  - To enable, set SOURCE=shoppinglist in .env

## Version 1.3
- The missing amount of a product is added to the Bring list. 
  - Translation of the unit via Environment Variable UNIT


## Version 1.2

- Added possibility to skip products that are only partly missing. 
  - Environment Variable GROCYSKIPPARTLYINSTOCK 
- Added possibility to define a custom field to skip products that are partly missing. 
  This makes it possible, to add partly missing products to the bring list, except for specific products. 
  - Environment Variable GROCYSKIPPARTLYINSTOCKCUSTOM to enable/disable and HIDEPARTLYFROMBRING to define the user field.

## Version 1.1

- Added possiblity to hide missing products from the BRING list. 
  - Added Envionment Variable HIDEFROMBRING
  - Added function to check if the user field HIDEFROMBRING is true

