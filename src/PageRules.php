<?php

namespace LukasKleinschmidt\Uuid;

use Kirby\Cms\Page;
use Kirby\Cms\PageRules as BasePageRules;
use Kirby\Exception\DuplicateException;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Exception\PermissionException;
use Kirby\Toolkit\Str;

class PageRules extends BasePageRules
{
    public static function create(Page $page): bool
    {
        $slug = $page->content()->slug();

        if (Str::length($slug) < 1) {
            throw new InvalidArgumentException([
                'key' => 'page.slug.invalid',
            ]);
        }

        if ($page->exists() === true) {
            throw new DuplicateException([
                'key'  => 'page.draft.duplicate',
                'data' => [
                    'slug' => $page->slug()
                ]
            ]);
        }

        if ($page->permissions()->create() !== true) {
            throw new PermissionException([
                'key' => 'page.create.permission',
                'data' => [
                    'slug' => $page->slug()
                ]
            ]);
        }

        $siblings = $page->parentModel()->children();
        $drafts   = $page->parentModel()->drafts();

        if ($duplicate = $siblings->find($slug)) {
            throw new DuplicateException([
                'key'  => 'page.duplicate',
                'data' => ['slug' => $slug]
            ]);
        }

        if ($duplicate = $drafts->find($slug)) {
            throw new DuplicateException([
                'key'  => 'page.draft.duplicate',
                'data' => ['slug' => $slug]
            ]);
        }

        return true;
    }
}
