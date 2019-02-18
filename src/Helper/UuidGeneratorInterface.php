<?php
declare(strict_types=1);


namespace Eos\ComView\Client\Helper;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
interface UuidGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string;
}
