<?php

use REW\Phinx\Seed\AbstractSeed;
use REW\Seed\Faker\Content\LinkFaker;
use REW\Seed\Faker\Content\PageFaker;
use REW\Seed\Faker\Content\SnippetFaker;
use REW\Seed\Faker\Content\TestimonialFaker;

// TODO: add featured communities

/**
 * ContentSeeder
 * - 10 snippets
 * - 50 pages
 * - 10 links
 * - 10 testimonials
 */
class ContentSeeder extends AbstractSeed
{

    /**
     * Page table
     * @var string
     */
    const TABLE_PAGES = 'pages';

    /**
     * Link table
     * @var string
     */
    const TABLE_LINKS = 'pages';

    /**
     * Snippet table
     * @var string
     */
    const TABLE_SNIPPETS = 'snippets';

    /**
     * Testimonial table
     * @var string
     */
    const TABLE_TESTIMONIALS = 'testimonials';

    /**
     * Fake content
     * @return void
     */
    public function run()
    {

        // Generate fake snippets
        $snippetFaker = new SnippetFaker;
        $snippetTable = self::TABLE_SNIPPETS;
        $snippets = $snippetFaker->generate(10);
        foreach ($snippets as $snippet) {
            $this->insert($snippetTable, $snippet);
        }

        // Generate fake pages
        $pageFaker = new PageFaker;
        $pageTable = self::TABLE_PAGES;
        $pages = $pageFaker->generate(50);
        foreach ($pages as $page) {
            $this->insert($pageTable, $page);
            // TODO: add snippet to content
            // TODO: add sub-pages
            //$page['category'];
            //$page['is_main_cat'] = 'f';
            //$page['category_order'];
            //$page['subcategory_order'];
        }

        // Generate fake links
        $linkFaker = new LinkFaker;
        $linkTable = self::TABLE_LINKS;
        $links = $linkFaker->generate(10);
        foreach ($links as $link) {
            $this->insert($linkTable, $link);
            //$link['category'];
            //$link['category_order'];
            //$link['subcategory_order'];
        }

        // Generate fake testimonials
        $testimonialFaker = new TestimonialFaker;
        $testimonialTable = self::TABLE_TESTIMONIALS;
        $testimonials = $testimonialFaker->generate(10);
        foreach ($testimonials as $testimonial) {
            $this->insert($testimonialTable, $testimonial);
        }
    }
}
