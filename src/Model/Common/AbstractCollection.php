<?php
declare(strict_types=1);


namespace Eos\ComView\Client\Model\Common;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class AbstractCollection implements CollectionInterface
{
    /**
     * @var array
     */
    protected $collection;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->collection = $data;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->collection;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \count($this->collection);
    }
}
