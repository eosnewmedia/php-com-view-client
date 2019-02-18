<?php
declare(strict_types=1);


namespace Eos\ComView\Client\Helper;

use Ramsey\Uuid\Uuid;


/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class RamseyUuidGenerator implements UuidGeneratorInterface
{
    /**
     * @return string
     * @throws \Exception
     */
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
