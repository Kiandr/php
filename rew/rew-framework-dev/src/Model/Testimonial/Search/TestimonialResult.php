<?php
namespace REW\Model\Testimonial\Search;

class TestimonialResult
{
    /**
     * @var string
     */
    protected $testimonial;

    /**
     * @var string
     */
    protected $client;

    /**
     * @param string $testimonial
     * @return self
     */
    public function withTestimonial($testimonial)
    {
        $clone = clone $this;
        $clone->testimonial = $testimonial;
        return $clone;
    }

    /**
     * @return string
     */
    public function getTestimonial()
    {
        return $this->testimonial;
    }

    /**
     * @param string $client
     * @return self
     */
    public function withClient($client)
    {
        $clone = clone $this;
        $clone->client = $client;
        return $clone;
    }

    /**
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }
}
