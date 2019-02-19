<?php
declare(strict_types=1);


namespace Eos\ComView\Client\Helper;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 * @todo rename namespace from "Helper" to more specif one like "UuidGenerator"
 */
interface UuidGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string;
}
