<?php
declare(strict_types=1);


namespace Eos\ComView\Client\Model\Common;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 * @todo I do not think we need this interface or an implementation of this interface because there is no added value over a simple array
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
     * @todo why should this method defined? it is not used anywhere in the implementation and is already defined by the \Countable interface...
     * @return int
     */
    public function count(): int;
}
