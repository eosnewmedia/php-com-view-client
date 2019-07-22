<?php
declare(strict_types=1);

namespace Eos\ComView\Client\Model;


/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class ViewRequest
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $pagination;

    /**
     * @var string|null
     */
    private $orderBy;

    /**
     * @param string $name
     * @param array $headers
     * @param array $parameters
     * @param array $pagination
     * @param null|string $orderBy
     */
    public function __construct(
        string $name,
        array $headers = [],
        array $parameters = [],
        array $pagination = [],
        ?string $orderBy = null
    ) {
        $this->name = $name;
        $this->headers = $headers;
        $this->parameters = $parameters;
        $this->pagination = $pagination;
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
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function getPagination(): array
    {
        return $this->pagination;
    }

    /**
     * @return null|string
     */
    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }
}
