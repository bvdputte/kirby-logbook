<?php

use bvdputte\kirbyLogbook\logbook;

return [
    'logbook' => function () {
        $accessPermission = (function() {
            return logbook::hasAccess(kirby()->user()) ? true : 'disabled';
        })();

        return [
            'label' => 'LogBook', // label for the menu and the breadcrumb
            'icon' => 'book', // icon for the menu and breadcrumb
            // optional replacement for the breadcrumb label
            // 'breadcrumbLabel' => function () {
            //     return 'LogBook';
            // },
            'disabled' => false,
            'menu' => $accessPermission, // show / hide from the menu
            'link' => 'logbook', // link to the main area view
            'views' => [
                [
                    'pattern' => 'logbook', //`panel` slug is automatically prepended
                    'action' => function () {
                        return [
                            'component' => 'k-logbook-view',  //the Vue component can be defined in the `index.js` of your plugin
                            'title' => 'LogBook', // the document title for the current view
                            'props' => [
                                'title' => 'LogBook',
                                'logfiles' => logbook::getLogfiles(),
                                'selectedLogfile' => logbook::getDefaultLogfile(),
                                'maxLogLines' => logbook::getMaxLogLines(),
                                'logData' => logbook::tail(),
                                'hasKirbyLogPlugin' => logbook::hasKirbyLogPlugin(),
                                'paginationSize' => logbook::getPaginationSize()
                            ],
                        ];
                    }
                ]
            ]
        ];
    }
];
