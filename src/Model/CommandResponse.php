<?php
declare(strict_types=1);

namespace Eos\ComView\Client\Model;

use LogicException;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class CommandResponse
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

    /**
     * @param string $id
     * @return Command
     */
    public function getCommand(string $id): Command
    {
        foreach ($this->commands as $command) {
            if ($command->getId() === $id) {
                return $command;
            }
        }

        throw new LogicException('The requested command has never been registered!');
    }
}
