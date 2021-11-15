<?php

use bvdputte\kirbyLogbook\logbook;

return [
    'logbook' => function () {
        $accessPermission = logbook::hasAccess(kirby()->user());

        return [
            'label' => 'LogBook', // label for the menu and the breadcrumb
            'icon' => 'book', // icon for the menu and breadcrumb
            // optional replacement for the breadcrumb label
            // 'breadcrumbLabel' => function () {
            //     return 'LogBook';
            // },
            'disabled' => false,
            'menu' => $accessPermission ? true : 'disabled', // Enable/disable from the menu
            'link' => 'logbook', // link to the main area view
            'views' => [
                [
                    'pattern' => 'logbook', //`panel` slug is automatically prepended
                    'action' => function () {
                        // Block unauthorized access
                        logbook::checkAccess(kirby()->user());

                        return [
                            'component' => 'k-logbook-area',  //the Vue component can be defined in the `index.js` of your plugin
                            'title' => 'LogBook', // the document title for the current view
                            'props' => [
                                'title' => 'LogBook',
                                'logfiles' => logbook::getLogfiles(),
                                'selectedLogfile' => logbook::getDefaultLogfile(),
                                'hasKirbyLogPlugin' => logbook::hasKirbyLogPlugin(),
                                'maxLogLines' => logbook::getMaxLogLines(),
                                'limit' => logbook::getPaginationSize()
                            ],
                        ];
                    }
                ]
            ]
        ];
    }
];
