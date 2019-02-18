<?php
declare(strict_types=1);

namespace Eos\ComView\Client\Model\Value;

use Eos\ComView\Client\Model\Common\KeyValueCollectionInterface;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class ViewRequest
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var KeyValueCollectionInterface
     */
    private $parameters;


    /**
     * @var KeyValueCollectionInterface
     */
    private $pagiantion;

    /**
     * @var string|null
     */
    private $orderBy;

    /**
     * @param string $name
     * @param KeyValueCollectionInterface $parameters
     * @param KeyValueCollectionInterface $pagiantion
     * @param null|string $orderBy
     */
    public function __construct(string $name, KeyValueCollectionInterface $parameters, KeyValueCollectionInterface $pagiantion, ?string $orderBy)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->pagiantion = $pagiantion;
        $this->orderBy = $orderBy;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * @return KeyValueCollectionInterface
     */
    public function getParameters(): KeyValueCollectionInterface
    {
        return $this->parameters;
    }

    /**
     * @return KeyValueCollectionInterface
     */
    public function getPagiantion(): KeyValueCollectionInterface
    {
        return $this->pagiantion;
    }

    /**
     * @return null|string
     */
    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }


}
