<?php

use bvdputte\kirbyLogbook\logbook;

return [
    [
        'pattern' => 'kirbylogbook/(:any)/(:any?)',
        'action' => function($logName) {
            // Restrict unauthenticated access
            if (!logbook::hasAccess(kirby()->user())) go('error', 404);

            // Restrict access to restricted logfiles
            if (!in_array($logName, logbook::getLogfiles())) go('error', 404);

            // Filter server side
            // $filter = isset($this->arguments()[1]) ? $this->arguments()[1] : null;
            // if ($filter && !in_array($filter, option('bvdputte.logbook.logLevels'))) {
            //     $filter = null;
            // }

            return json_encode(logbook::tail($logName/*, $filter*/));
        }
    ]
];
