<?php
declare(strict_types=1);


namespace Eos\ComView\Client\Model\Common;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */

interface CollectionInterface extends \Countable
{
    /**
     * @return array
     */
    public function all(): array;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @return int
     */
    public function count(): int;
}
