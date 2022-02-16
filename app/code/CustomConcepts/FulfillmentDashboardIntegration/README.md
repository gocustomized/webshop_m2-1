## CustomConcept Fulfillment Dashboard Integration Module

#### Overview

   The module provides functionality for shipment creation in the Fulfillment Dashboard
 

#### Technical
   - the class Model/Adapter provides request API for the Fulfillment Dashboard shipment creation
   - the plugin Plugin\Model\Transsmart\Adapter sends reques to the Dashboard (before Transsmart booking).  
   - the plugin Plugin\Shipment\Creator adjusts shipment data for the Dashboard, such as - added articleId and SKU.
   - the class Service\DashboardShipmentCreator - contain all logic for the dashboard shipment creation.


#### Debug
    All operations that related to the dashboard shipment creation - logs to a cc_dashboard_integration.log file
   
