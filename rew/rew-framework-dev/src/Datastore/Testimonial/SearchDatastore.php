<?php
namespace REW\Datastore\Testimonial;

use REW\Factory\Testimonial\TestimonialResultFactory;
use REW\Model\Testimonial\Search\TestimonialRequest;
use REW\Model\Testimonial\Search\TestimonialResult;
use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;

class SearchDatastore
{
    /**
     * @var DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var TestimonialResultFactory
     */
    protected $testimonialResultFactory;

    /**
     * @param DBFactoryInterface $dbFactory
     * @param SettingsInterface $settings
     */
    public function __construct(DBFactoryInterface $dbFactory, SettingsInterface $settings, TestimonialResultFactory $testimonialResultFactory)
    {
        $this->testimonialResultFactory = $testimonialResultFactory;
        $this->dbFactory = $dbFactory;
        $this->settings = $settings;
    }

    /**
     * @param TestimonialRequest $testimonialRequest
     * @return TestimonialResult[]
     * @throws \PDOException If a database error occurs
     * @throws \Exception If database handler can't be provisioned
     */
    public function getTestimonials(TestimonialRequest $testimonialRequest)
    {
        $db = $this->dbFactory->get();

        // Select query fields
        $selectFields = implode(', ', array_map(function($field) {
            return sprintf('`t`.`%s`', $field);
        }, $testimonialRequest->getFields()));

        // Limit query
        $queryLimit = '';
        $limit = $testimonialRequest->getLimit();
        if (!is_null($limit)) {
            $queryLimit = sprintf(' LIMIT %d', $limit);
        }


        // Get Testimonials
        $testimonialQuery = sprintf('SELECT %s FROM `testimonials` `t` ORDER BY RAND()%s', $selectFields, $queryLimit);
        $stmt = $db->prepare($testimonialQuery);
        $stmt->execute();
        $testimonialRows = $stmt->fetchAll();

        $testimonials = [];
        foreach ($testimonialRows as $testimonialRow) {
            $testimonials[] = $this->testimonialResultFactory->createFromArray($testimonialRow);
        }

        return $testimonials;
    }

}
