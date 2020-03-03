<?php

@include_once __DIR__ . '/vendor/autoload.php';

use LukasKleinschmidt\Uuid\Page;
use LukasKleinschmidt\Uuid\PageRules;

Kirby::plugin('lukaskleinschmidt/uuid', [
    'hooks' => [
        'system.loadPlugins:after' => function () {
            $kirby  = kirby();

            if ($kirby->multilang() === false) {
                return;
            }

            $models = $kirby->extensions('pageModels');
            $pages  = $kirby->blueprints('pages');

            // patch pages that don't have a custom page model yet
            $keys = array_filter($pages, function ($key) use ($models) {
                return ! array_key_exists($key, $models);
            });

            $kirby->extend([
                'pageModels' => array_fill_keys($keys, Page::class),
            ]);

            $kirby->extend([
                'hooks' =>[
                    'page.create:before' => function ($page) {
                        PageRules::create($page);
                    }
                ]
            ]);
        },
    ]
]);
