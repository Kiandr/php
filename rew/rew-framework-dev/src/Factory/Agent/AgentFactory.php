<?php

namespace REW\Factory\Agent;

use REW\Core\Interfaces\FormatInterface;
use REW\Model\Agent\Search\AgentResult;

/**
 * AgentFactory
 * @package REW\Factory\Agent
 */
class AgentFactory
{

    /**
     * @var string
     */
    const AGENT_LINK_PATTERN = '/agent/%s/';

    /**
     * @var string
     */
    const AGENT_IMAGE_PATTERN = '/uploads/agents/%s';

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
     * @return AgentResult
     */
    public function createFromArray(array $data)
    {
        $agentResult = new AgentResult();
        $agentResult = $agentResult
            ->withId(!empty($data['id']) ? $data['id'] : null)
            ->withFirstName(!empty($data['first_name']) ? $data['first_name'] : null)
            ->withLastName(!empty($data['last_name']) ? $data['last_name'] : null)
            ->withEmail(!empty($data['email']) ? $data['email'] : null)
            ->withTitle(!empty($data['title']) ? $data['title'] : null)
            ->withOfficeId(!empty($data['office']) ?$data['office'] : null)
            ->withOfficePhone(!empty($data['office_phone']) ?$data['office_phone'] : null)
            ->withHomePhone(!empty($data['home_phone']) ? $data['home_phone'] : null)
            ->withCellPhone(!empty($data['cell_phone']) ? $data['cell_phone'] : null)
            ->withFax(!empty($data['fax']) ? $data['fax']: null)
            ->withRemarks(!empty($data['remarks']) ? $data['remarks'] : null)
            ->withDisplay(!empty($data['display'] === 'Y') ? true : false)
            ->withDisplayFeature(!empty($data['display_feature'] === 'Y') ? true : false)
            ->withAgentId(!empty($data['agent_id']) ? $data['agent_id'] : null);

        // Generate link to agent details page
        $firstName = $agentResult->getFirstName();
        $lastName = $agentResult->getLastName();
        if ($firstName || $lastName) {
            $fullName = implode(' ', [$firstName, $lastName]);
            $agentResult = $agentResult->withLink(
                sprintf(static::AGENT_LINK_PATTERN,
                    $this->format->slugify($fullName)
                )
            );
        }

        // Generate URL to agent image
        if (!empty($data['image'])) {
            $agentResult = $agentResult->withImage(
                sprintf(static::AGENT_IMAGE_PATTERN,
                    $data['image']
                )
            );
        }

        return $agentResult;
    }
}
