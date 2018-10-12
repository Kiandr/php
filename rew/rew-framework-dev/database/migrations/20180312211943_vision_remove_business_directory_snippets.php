<?php

use REW\Phinx\Migration\AbstractMigration;

class VisionRemoveBusinessDirectorySnippets extends AbstractMigration
{

    /**
     * Migrate Up
     * @return void
     */
    public function up()
    {
        $this->execute(
            "DELETE FROM `snippets`
            WHERE `name` IN (
            'business-directory-add-listing-formatting',
            'business-directory-add-listing-page',
            'business-directory-intro');"
        );
    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        $this->execute(
            "INSERT INTO `snippets` (`agent`, `name`, `code`, `type`) VALUES
            (1, 'business-directory-add-listing-formatting', 'Please note that if you want to use separate paragraphs (line breaks) in here, you will need to <a href=\"/contact.php\">contact us</a> with your desired formatting.', 'cms'),
            (1, 'business-directory-add-listing-page', '<div class=\"msg notice\">\r\n    <p><strong>Attention:</strong> We moderate all listing submissions. If your listing is not meant to be <strong>useful for our local audience</strong>, it will not be approved, so please don''t waste your time. If your business is legitimately local but we think your listing is intended only for the \"backlink\", we will put a \"nofollow\" attribute on your link so that search engine benefits do not apply.</p>\r\n</div>', 'cms'),
            (1, 'business-directory-intro', '<div class=\"msg notice\">\r\n    <p>If you know of a business or organization that should appear in our directory, please <a href=\"/directory/add/\">add it</a>!</p>\r\n</div>', 'cms');"
        );
    }

}