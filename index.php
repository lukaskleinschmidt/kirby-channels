<?php

@include_once __DIR__ . '/vendor/autoload.php';

use LukasKleinschmidt\Channels\Page;
use LukasKleinschmidt\Channels\PageRules;
use LukasKleinschmidt\Channels\Channels;

Kirby::plugin('lukaskleinschmidt/channels', [
    'hooks' => [
        'system.loadPlugins:after' => function () {
            $kirby = kirby();

            if ($kirby->multilang() === false) {
                return;
            }

            $models = $kirby->extensions('pageModels');
            $pages  = Channels::pages();

            // patch pages that don't use a custom page model
            $keys = array_filter($pages, function ($key) use ($models) {
                return array_key_exists($key, $models) === false;
            });

            $kirby->extend([
                'pageModels' => array_fill_keys($keys, Page::class),
                'hooks' => [
                    'page.create:before' => function ($page) {
                        if (Channels::isParent($page)) {
                            PageRules::create($page);
                        }
                    }
                ]
            ]);
        },
    ]
]);
