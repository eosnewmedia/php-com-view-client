<?php
declare(strict_types=1);

namespace Eos\ComView\Client\Model\Value;


/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class ViewResponse
{
    public const SUCCESS = 'success';
    public const ERROR = 'error';

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
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @param array $parameters
     * @param array $pagination
     * @param null|string $orderBy
     * @param array $data
     * @param int $statusCode
     */
    public function __construct(array $parameters, array $pagination, ?string $orderBy, array $data, int $statusCode)
    {
        $this->parameters = $parameters;
        $this->pagination = $pagination;
        $this->orderBy = $orderBy;
        $this->data = $data;
        $this->statusCode = $statusCode;
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

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        if ($this->getStatusCode() === 200) {
            return self::SUCCESS;
        }

        return self::ERROR;
    }
}
