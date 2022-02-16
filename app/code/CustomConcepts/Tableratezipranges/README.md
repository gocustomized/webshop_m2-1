## CustomConcept Tableratezipranges Module

#### Overview

   This module extends the Tablerate shipment method by adding zip ranges. 
   1. Module adds 'dest_zip_to' column to the 'shipping_tablerate' table and all needful classes to handle this data. 
   2. Additionally, the module overrides the Bluebirdday pickup location functionality (the fixed rate calculation part). All rates for locations calculated from the tablerate. 

#### Technical
- the plugin Plugin\Transsmart\Rate\Response\ParserPlugin provides n.2 of functionality.
