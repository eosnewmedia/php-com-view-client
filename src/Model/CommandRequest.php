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
     * @param Command[] $commands
     * @param array $headers
     */
    public function __construct(array $commands, array $headers = [])
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
