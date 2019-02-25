<?php
declare(strict_types=1);

namespace Eos\ComView\Client\Model\Value;

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
     * @var array
     */
    private $parameters;

    /**
     * @param string $command
     * @param array $parameters
     */
    public function __construct(string $command, array $parameters = [])
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
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
