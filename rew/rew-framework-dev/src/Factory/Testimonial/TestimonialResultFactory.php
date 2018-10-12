<?php
namespace REW\Factory\Testimonial;

use REW\Model\Testimonial\Search\TestimonialResult;

class TestimonialResultFactory
{
    /**
     * @param array $data
     * @return TestimonialResult
     */
    public function createFromArray(array $data)
    {
        $testimonialResult = new TestimonialResult();
        $testimonialResult = $testimonialResult->withClient((isset($data['client']) ? $data['client'] : null));
        $testimonialResult = $testimonialResult->withTestimonial((isset($data['testimonial']) ? $data['testimonial'] : null));
        return $testimonialResult;
    }
}
