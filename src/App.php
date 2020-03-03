<?php

namespace LukasKleinschmidt\Uuid;

use Kirby\Cms\App as BaseApp;

class App extends BaseApp
{
    /**
     * Initializes and returns the Site object
     *
     * @return \LukasKleinschmidt\Uuid\Site
     */
    public function site()
    {
        return $this->site = $this->site ?? new Site([
            'errorPageId' => $this->options['error'] ?? 'error',
            'homePageId'  => $this->options['home']  ?? 'home',
            'kirby'       => $this,
            'url'         => $this->url('index'),
        ]);
    }
}
