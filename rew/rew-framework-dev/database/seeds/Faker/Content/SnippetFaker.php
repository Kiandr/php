<?php

namespace REW\Seed\Faker\Content;

use REW\Seed\Faker\AbstractFaker;

/**
 * SnippetFaker
 * @package REW\Seed\Faker
 */
class SnippetFaker extends AbstractFaker
{

    /**
     * CMS snippet type
     * @var string
     */
    const SNIPPET_TYPE_CMS = 'cms';

    /**
     * Fake Snippet
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        return [
            //'agent' => NULL,
            //'team' => NULL,
            'type' => self::SNIPPET_TYPE_CMS,
            'name' => $faker->unique()->slug(rand(1, 5)),
            'code' => sprintf('<p>%s</p>', implode('</p><p>', $faker->paragraphs(rand(1, 3))))
        ];
    }
}
