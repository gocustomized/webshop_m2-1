## CustomConcept TranssmartSmartConnectExtension Module

Overview

   This module overrides the Bluebirdday_TranssmartSmartConnectExtension module. The reasons are:
   1. Adding a "Contact" node for a booking request
   2. Overriding a "State" node (from full name to it code) for a booking request
   3. Overriding an "Incoterms" node (only for US) for a booking request
  
Technical
   - the plugin Plugin\Model\Shipment\Creator (by afterCreateDataObject method) provides n.1, n.2, n.3 of mentioned functionality.
   
