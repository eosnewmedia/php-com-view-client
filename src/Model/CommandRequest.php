<?php
declare(strict_types=1);

namespace Eos\ComView\Client\Model;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class CommandRequest
{
    /**
     * @var array
     */
    private $headers;

    /**
     * @var Command[]
     */
    private $commands;

    /**
     * @param array $headers
     * @param Command[] $commands
     */
    public function __construct(array $headers, array $commands)
    {
        $this->headers = $headers;
        $this->commands = $commands;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return Command[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }
}
