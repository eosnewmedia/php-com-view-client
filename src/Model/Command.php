<?php
declare(strict_types=1);

namespace Eos\ComView\Client\Model;

use LogicException;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class Command
{
    public const STATUS_SUCCESS = 'SUCCESS';
    public const STATUS_ERROR = 'ERROR';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $command;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var bool
     */
    private $executed = false;

    /**
     * @var string
     */
    private $status;

    /**
     * @var array
     */
    private $result;

    /**
     * @param string $command
     * @param array $parameters
     * @param string|null $id
     */
    public function __construct(string $command, array $parameters = [], ?string $id = null)
    {
        $this->id = $id ?? sha1(uniqid(self::class, true));
        $this->command = $command;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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

    /**
     * @return bool
     */
    public function isExecuted(): bool
    {
        return $this->executed;
    }

    /**
     * @param string $status
     * @param array|null $result
     */
    public function markExecuted(string $status, ?array $result = null): void
    {
        if ($this->isExecuted()) {
            throw new LogicException('This command has already been executed!');
        }

        $this->executed = true;

        $this->status = $status;
        $this->result = $result;
        if (!is_array($this->result)) {
            $this->result = [];
        }
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        if (!$this->isExecuted()) {
            throw new LogicException('This command has not been executed!');
        }

        return $this->status;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        if (!$this->isExecuted()) {
            throw new LogicException('This command has not been executed!');
        }

        return $this->result;
    }
}
