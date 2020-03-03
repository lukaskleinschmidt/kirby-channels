<?php

namespace LukasKleinschmidt\Uuid;

use Kirby\Exception\DuplicateException;
use Kirby\Toolkit\Dir;
use Kirby\Toolkit\Str;
use Ramsey\Uuid\Uuid;

trait PageActions
{
    /**
     * Changes the slug/uid of the page
     *
     * @param string $slug
     * @param string $languageCode
     * @return self
     */
    public function changeSlug(string $slug, string $languageCode = null)
    {
        // always sanitize the slug
        $slug = Str::slug($slug);

        if ($language = $this->kirby()->language($languageCode)) {
            if (!$language) {
                throw new NotFoundException('The language: "' . $languageCode . '" does not exist');
            }
        }

        return $this->commit('changeSlug', [$this, $slug, $languageCode], function ($page, $slug, $languageCode) {
            // remove the slug if it's the same as the folder name
            if ($slug === $page->uid()) {
                $slug = null;
            }

            return $page->save(['slug' => $slug], $languageCode);
        });
    }

    /**
     * Copies the page to a new parent
     *
     * @param array $options
     * @return \Kirby\Cms\Page
     */
    public function copy(array $options = [])
    {
        $slug        = $options['slug']      ?? $this->slug();
        $isDraft     = $options['isDraft']   ?? $this->isDraft();
        $parent      = $options['parent']    ?? null;
        $parentModel = $options['parent']    ?? $this->site();
        $num         = $options['num']       ?? null;
        $children    = $options['children']  ?? false;
        $files       = $options['files']     ?? false;

        // clean up the slug
        $slug = Str::slug($slug);

        if ($parentModel->findPageOrDraft($slug)) {
            throw new DuplicateException([
                'key'  => 'page.duplicate',
                'data' => [
                    'slug' => $slug
                ]
            ]);
        }

        $tmp = new static([
            'isDraft' => $isDraft,
            'num'     => $num,
            'parent'  => $parent,
            'slug'    => (string) Uuid::uuid4(),
        ]);

        $ignore = [];

        // don't copy files
        if ($files === false) {
            foreach ($this->files() as $file) {
                $ignore[] = $file->root();

                // append all content files
                array_push($ignore, ...$file->contentFiles());
            }
        }

        Dir::copy($this->root(), $tmp->root(), $children, $ignore);

        $copy = $parentModel->clone()->findPageOrDraft($tmp->slug());

        // update all slugs
        if ($this->kirby()->multilang() === true) {
            foreach ($this->kirby()->languages() as $language) {
                $copy = $copy->save([
                    'slug' => $language->isDefault() ? $slug : null
                ], $language->code());
            }
        }

        // add copy to siblings
        if ($isDraft === true) {
            $parentModel->drafts()->append($copy->id(), $copy);
        } else {
            $parentModel->children()->append($copy->id(), $copy);
        }

        return $copy;
    }

    /**
     * Creates and stores a new page
     *
     * @param array $props
     * @return Page
     */
    public static function create(array $props): Page
    {
        $slug = Str::slug($props['slug'] ?? $props['content']['title'] ?? null);

        $props['content']['slug'] = $slug;
        $props['slug'] = (string) Uuid::uuid4();

        return parent::create($props);
    }
}
