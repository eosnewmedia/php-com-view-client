<?php
declare(strict_types=1);

namespace Eos\ComView\Client\Model\Value;

use Eos\ComView\Client\Model\Common\CollectionInterface;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class CommandRequest
{
    /**
     * @var string
     */
    private $command;

    /**
     * @var CollectionInterface
     */
    private $parameters;

    /**
     * @param string $command
     * @param CollectionInterface $parameters
     */
    public function __construct(string $command, CollectionInterface $parameters)
    {
        $this->command = $command;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return CollectionInterface
     * @todo because value objects are immutable, an array should be returned instead of a mutable collection
     */
    public function getParameters(): CollectionInterface
    {
        return $this->parameters;
    }


}
