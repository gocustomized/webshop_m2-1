## CustomConcept TranssmartSmartConnect Module

Overview

   This module overrides the Bluebirdday_TranssmartSmartConnect module. The reasons are:
   1. A pickup location rate should copy from current tablerate method
   2. Pickup locations should fetch from the transsmart for current carrier (from current tablerate)
   3. All FE logic overridden similar to M1.
  
Technical
   - the controller Controller\Pickup\Location  provides n.1, n.2 of mentioned functionality.
   
