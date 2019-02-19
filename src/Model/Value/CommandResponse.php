<?php
declare(strict_types=1);

namespace Eos\ComView\Client\Model\Value;

use Eos\ComView\Client\Model\Common\CollectionInterface;

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
     * @var CollectionInterface
     */
    private $result;

    /**
     * @param string $status
     * @param CollectionInterface $result
     */
    public function __construct(string $status, CollectionInterface $result)
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
     * @return CollectionInterface
     * @todo because value objects are immutable, an array should be returned instead of a mutable collection
     */
    public function getResult(): CollectionInterface
    {
        return $this->result;
    }


}
