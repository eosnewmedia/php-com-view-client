<?php
declare(strict_types=1);

namespace Eos\ComView\Client\Model\Value;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class CommandResponse
{
    /**
     * @var string
     */
    private $status;

    /**
     * @var array
     */
    private $result;

    /**
     * @param string $status
     * @param array $result
     */
    public function __construct(string $status, array $result)
    {
        $this->status = $status;
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }
}
