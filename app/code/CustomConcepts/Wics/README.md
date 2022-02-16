## CustomConcept Wics Integration Module

#### Overview

   The module provides functionality for updating products quantities from the WICS
 

#### Technical
   - the class Model/WicsAdapter provides requests API for the WICS
   - the class Model/Data/Response presents the WICS response for the list items request 
   - the class Service/Synchronizer uses by cron and CLI command (SynchronizeItemCommand) for items updating
   - the class Service/WicsItemUpdater contains all logic related to updating stock items by a WICS response
   - the class Model/SlackAdapter provides API for sending messages to the Slack
   - Model/Sync, Model/SyncManagement, Model/SyncRepository - classes that serve the cc_wics_sync table


#### Debug
    All operations that related to the WICS integration - logs to the cc_wics.log file
    The cc_wics_sync table contains all syncronization attempts with the statuses
   
