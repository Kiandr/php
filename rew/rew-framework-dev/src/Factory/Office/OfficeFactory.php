<?php

namespace REW\Factory\Office;

use REW\Core\Interfaces\FormatInterface;
use REW\Model\Office\Search\OfficeResult;

/**
 * OfficeFactory
 * @package REW\Factory\Office
 */
class OfficeFactory
{

    /**
     * @var string
     */
    const OFFICE_LINK_PATTERN = '/office/%s/';

    /**
     * @var string
     */
    const OFFICE_IMAGE_PATTERN = '/uploads/offices/%s';

    /**
     * @var FormatInterface
     */
    protected $format;

    /**
     * @param FormatInterface $format
     */
    public function __construct(FormatInterface $format) {
        $this->format = $format;
    }

    /**
     * @param array $data
     * @return OfficeResult
     */
    public function createFromArray(array $data)
    {
        $officeResult = new OfficeResult();
        $officeResult = $officeResult
            ->withId(!empty($data['id']) ? $data['id'] : null)
            ->withTitle(!empty($data['title']) ? $data['title'] : null)
            ->withDescription(!empty($data['description']) ? $data['description'] : null)
            ->withEmail(!empty($data['email']) ? $data['email'] : null)
            ->withPhone(!empty($data['phone']) ? $data['phone'] : null)
            ->withFax(!empty($data['fax']) ? $data['fax'] : null)
            ->withAddress(!empty($data['address']) ? $data['address'] : null)
            ->withCity(!empty($data['city']) ? $data['city'] : null)
            ->withState(!empty($data['state']) ? $data['state'] : null)
            ->withZip(!empty($data['zip']) ? $data['zip'] : null)
            ->withDisplay(!empty($data['display']) ? $data['display'] : null)
            ->withSort(!empty($data['sort']) ? $data['sort'] : null);

        // Generate link to office details page
        $title = $officeResult->getTitle();
        if ($title) {
            $officeResult = $officeResult->withLink(
                sprintf(static::OFFICE_LINK_PATTERN,
                    $this->format->slugify($title)
                )
            );
        }

        // Generate URL to office image
        if (!empty($data['image'])) {
            $officeResult = $officeResult->withImage(
                sprintf(static::OFFICE_IMAGE_PATTERN,
                    $data['image']
                )
            );
        }

        return $officeResult;
    }
}
