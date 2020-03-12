<?php

namespace LukasKleinschmidt\Channels;

use Kirby\Cms\Page;

class Channels
{
    /**
     * Checks wether a page is a channel parent
     *
     * @param \Kirby\Cms\Page $page
     * @return boolean
     */
    public static function isParent(Page $page): bool
    {
        $options  = static::options();
        $template = (string) $page->intendedTemplate();

        return array_key_exists($template, $options);
    }

    /**
     * Checks wether a page is a channel page
     *
     * @param \Kirby\Cms\Page $page
     * @return boolean
     */
    public static function isPage(Page $page): bool
    {
        $options  = static::options();
        $template = (string) $page->intendedTemplate();

        foreach ($options as $value) {
            if (in_array($template, (array) $value) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the channel options
     *
     * @return array
     */
    public static function options(): array
    {
        return option('lukaskleinschmidt.channels', []);
    }

    /**
     * Returns blueprints defined in the channel options
     *
     * @return array
     */
    public static function pages(): array
    {
        $options = static::options();
        $pages   = [];

        foreach ($options as $key => $value) {
            array_push($pages, $key, ...(array) $value);
        }

        return array_unique($pages);
    }
}
