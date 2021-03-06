<?php
 
$functions = array(
        'local_PLUGINNAME_FUNCTIONNAME' => array( // local_PLUGINNAME_FUNCTIONNAME is the name of the web service function that the client will call.                                                                                
                'classname'   => 'component\external[\optional\sub\namespaces\classname', // create this class in componentdir/classes/external
                'methodname'  => 'FUNCTIONNAME', // implement this function into the above class
                'description' => 'This documentation will be displayed in the generated API documentation 
                                          (Administration > Plugins > Webservices > API documentation)',
                'type'        => 'write', // the value is 'write' if your function does any database change, otherwise it is 'read'.
                'ajax'        => true, // true/false if you allow this web service function to be callable via ajax
                'capabilities'  => 'moodle/xxx:yyy, addon/xxx:yyy',  // List the capabilities required by the function (those in a require_capability() call) (missing capabilities are displayed for authorised users and also for manually created tokens in the web interface, this is just informative).
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile')    // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included. Services created manually via the Moodle interface are not supported.
        )
);

// OPTIONAL
// During the plugin installation/upgrade, Moodle installs these services as pre-build services. 
// A pre-build service is not editable by administrator.
$services = array(
        'MY SERVICE' => array(
                'functions' => array ('local_PLUGINNAME_FUNCTIONNAME'), 
                'restrictedusers' => 0, // if 1, the administrator must manually select which user can use this service. 
                                                   // (Administration > Plugins > Web services > Manage services > Authorised users)
                'enabled'=>1, // if 0, then token linked to this service won't work
                'shortname'=>'myservice' //the short name used to refer to this service from elsewhere including when fetching a token
        )
);