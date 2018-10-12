<?php

use REW\Phinx\Seed\AbstractSeed;
use REW\Seed\Faker\Blog\CategoryFaker;
use REW\Seed\Faker\Blog\CommentFaker;
use REW\Seed\Faker\Blog\EntryFaker;
use REW\Seed\Faker\Blog\LinkFaker;
use REW\Seed\Faker\Blog\TagFaker;

/**
 * BlogSeeder
 * - 5 main categories
 * - N(0-3) sub-categories
 * - 5 blog links
 * - 20 blog tags
 * - 15 blog entries
 * - N(0-5) comments
 */
class BlogSeeder extends AbstractSeed
{

    /**
     * Blog category table
     * @var string
     */
    const TABLE_CATEGORIES = 'blog_categories';

    /**
     * Blog comments table
     * @var string
     */
    const TABLE_COMMENTS = 'blog_comments';

    /**
     * Blog entry table
     * @var string
     */
    const TABLE_ENTRIES = 'blog_entries';

    /**
     * Blog link table
     * @var string
     */
    const TABLE_LINKS = 'blog_links';

    /**
     * Blog tag table
     * @var string
     */
    const TABLE_TAGS = 'blog_tags';

    /**
     * Fake blog data
     * @return void
     */
    public function run()
    {

        // Blog categories
        $categoryLinks = [];
        $categoryFaker = new CategoryFaker;
        $categoryTable = self::TABLE_CATEGORIES;
        $categories = $categoryFaker->generate(5);
        foreach ($categories as $category) {
            // Insert main category to database
            $this->insert($categoryTable, $category);
            $categoryLinks[$category['link']] = $category['link'];

            // Add some random subcategories?
            if ($subcategories = rand(0, 3)) {
                foreach ($categoryFaker->generate($subcategories, [
                    'parent' => $category['link']
                ]) as $subcategory) {
                    $this->insert($categoryTable, $subcategory);
                    $categoryLinks[$subcategory['link']] = $subcategory['link'];
                }
            }
        }

        // Blog tags
        $tagLinks = [];
        $tagFaker = new TagFaker;
        $tagTable = self::TABLE_TAGS;
        $tags = $tagFaker->generate(20);
        foreach ($tags as $tag) {
            $this->insert($tagTable, $tag);
            $tagLinks[$tag['link']] = $tag['link'];
        }

        // Generate fake blog links
        $linkFaker = new LinkFaker;
        $linkTable = self::TABLE_LINKS;
        $links = $linkFaker->generate(5);
        foreach ($links as $link) {
            $this->insert($linkTable, $link);
        }

        // Generate fake blog comments
        $commentFaker = new CommentFaker;
        $commentTable = self::TABLE_COMMENTS;

        // Generate fake blog entries
        $entryFaker = new EntryFaker;
        $entryTable = self::TABLE_ENTRIES;
        $entries = $entryFaker->generate(15);
        foreach ($entries as $entry) {
            // Assign categories
            if (rand(1, 3) === 1) {
                shuffle($categoryLinks);
                $categories = array_slice($categoryLinks, 0, rand(1, 3));
                $entry['categories'] = implode(',', $categories);
            }

            // Assign blog tags
            if (rand(1, 2) === 1) {
                shuffle($tagLinks);
                $tags = array_slice($tagLinks, 0, rand(1, 5));
                $entry['tags'] = implode(',', $tags);
            }

            // Insert blog entry to database
            $this->insert($entryTable, $entry);

            // Add random blog comments
            if ($comments = rand(0, 5)) {
                $entryId = $this->lastInsertId();
                $comments = $commentFaker->generate($comments, ['entry' => $entryId]);
                if (!empty($comments)) {
                    $this->insert($commentTable, $comments);
                }
            }
        }
    }
}
