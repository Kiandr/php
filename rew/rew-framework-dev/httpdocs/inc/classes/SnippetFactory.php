<?php

use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\Snippet\ResultInterface;
use REW\Core\Interfaces\Factories\SnippetFactoryInterface;

class SnippetFactory implements SnippetFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Snippet constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Load snippet. This is a temp wrapper for rew_snippet
     * @param string $match
     * @param int $agentID
     * @param array $inputRow Usually used for page queries.
     * @return ResultInterface
     */
    public function lookup($match, $agentID = null, array $inputRow = array())
    {

        global $row;
        if ($inputRow) {
            $row = $inputRow;
        }
        $output = rew_snippet($match, false, $agentID);

        $result = $this->container->make(ResultInterface::class, ['name' => $match, 'html' => $output, 'row' => $row]);

        return $result;
    }
}
