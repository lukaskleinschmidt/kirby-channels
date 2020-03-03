<?php

namespace LukasKleinschmidt\Uuid;

use Kirby\Cms\Site as BaseSite;

class Site extends BaseSite
{
    /**
     * Creates a main page
     *
     * @param array $props
     * @return \LukasKleinschmidt\Uuid\Page
     */
    public function createChild(array $props)
    {
        $props = array_merge($props, [
            'url'    => null,
            'num'    => null,
            'parent' => null,
            'site'   => $this,
        ]);

        return Page::create($props);
    }
}
