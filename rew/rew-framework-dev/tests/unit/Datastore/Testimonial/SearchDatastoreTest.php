<?php
namespace REW\Test\Datastore\Testimonial;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Datastore\Testimonial\SearchDatastore;
use REW\Model\Testimonial\Search\TestimonialRequest;
use REW\Model\Testimonial\Search\TestimonialResult;
use REW\Factory\Testimonial\TestimonialResultFactory;
use Mockery as m;

class SearchDatastoreTest extends \Codeception\Test\Unit
{
    /**
     * @var SearchDatastore
     */
    protected $searchDatastore;

    /**
     * @var \REW\Core\Interfaces\Factories\DBFactoryInterface
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
     * @return void
     */
    public function setUp()
    {
        $this->dbFactory = m::mock(DBFactoryInterface::class);
        $this->settings = m::mock(SettingsInterface::class);
        $this->testimonialResultFactory = new TestimonialResultFactory;
    }

    /**
     * @return void
     */
    public function testGetTestimonials()
    {
        $mockedStmt = m::mock(\PDOStatement::class);
        $mockedStmt->shouldReceive('execute')
            ->withAnyArgs()
            ->andReturn(true);
        $mockedStmt->shouldReceive('fetchAll')
            ->andReturn($this->getDemoData());
        $mockedPdo = m::mock(\PDO::class);
        $mockedPdo->shouldReceive('prepare')
            ->with('SELECT `t`.`client`, `t`.`testimonial` FROM `testimonials` `t` ORDER BY RAND()')
            ->andReturn($mockedStmt);
        $this->dbFactory->shouldReceive('get')
            ->andReturn($mockedPdo);

        // Build Request
        $testimonialRequest = (new TestimonialRequest())->withFields(['client', 'testimonial']);

        // Conduct Search
        $datastore = new SearchDatastore($this->dbFactory, $this->settings, $this->testimonialResultFactory);

        $results = $datastore->getTestimonials($testimonialRequest);
        $this->assertCount(count($this->getDemoData()), $results);

        // Validate result contents.
        foreach ($results as $result)
        {
            $found = false;
            foreach ($this->getDemoData() as $datum) {
                if ($datum['client'] == $result->getClient()
                    && $datum['testimonial'] == $result->getTestimonial()) {
                    $found = true;
                }
            }
            $this->assertTrue($found);
        }

        // Try again, with a smaller limit
        $testimonialRequest = $testimonialRequest->withLimit(1);

        // Conduct Search
        $datastore = new SearchDatastore($this->dbFactory, $this->settings, $this->testimonialResultFactory);

        // Validate that the limit is adjusted in the query
        $mockedPdo->shouldReceive('prepare')
            ->with('SELECT `t`.`client`, `t`.`testimonial` FROM `testimonials` `t` ORDER BY RAND() LIMIT 1')
            ->andReturn($mockedStmt);
        $this->dbFactory->shouldReceive('get')
            ->andReturn($mockedPdo);
        $datastore->getTestimonials($testimonialRequest);

    }

    /**
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * @return array
     */
    protected function getDemoData()
    {
        return [[
            'client' => 'Party Parrot',
            'testimonial' => 'Most good.',
        ], [
            'client' => 'Fast Parrot',
            'testimonial' => 'This dwelling is sufficient.',
        ]];
    }
}
