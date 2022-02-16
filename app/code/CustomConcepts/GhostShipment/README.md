## CustomConcept Ghost Shipment Module

#### Overview

   This module provides functionality for the "ghost" shipment. Namely:
   1. 'Ghost' shipment creation
   2. Set an order to the "Processing" status if it has 'ghost' shipment
   3. Hiding shipments with the "GHOST" status
   4. Completing an order and notifying a customer when the "LABL" status was received
   5. Re-booking functionality. If the ghost shipment creation end ups with exception - 
   the shipment gets status "NEW" and will be able to book again.
 

#### Technical
   - the class GhostShipmentCreator - provides n.1 of functionality mentioned in the overview. 
   - the observer Observer\InvoicePaidAfter listen the event "cc_transsmart_invoice_paid_after" and invokes GhostShipmentCreator. 
   - the observer Observer\OrderSavedAfter will be used in the future. 
   - the plugin Plugin\Sales\ResourceModel\Order\Grid provides n.2 of functionality.  
   - the plugin Plugin\UiComponent\DataProvider\Reporting provides n.3 of functionality. It prevents loading 'ghost' shipment to the order shipment grid.
   - the class Model\Shipment\Synchronizer provides n.4 of functionality. It is overriding (by a preference) of Bluebirdday\TranssmartSmartConnectExtension\Model\Shipment\Synchronizer.
   - the class Model\Transsmart\Adapter can be used in the future. It provides a single shipment retrieval request.
   - the class Controller\Adminhtml\Shipment - overriding of Bluebirdday\TranssmartSmartConnectExtension Book controller.
   
   
