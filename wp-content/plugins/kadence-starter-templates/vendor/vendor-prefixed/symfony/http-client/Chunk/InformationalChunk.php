<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceStarterTemplates\Symfony\Component\HttpClient\Chunk;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
class InformationalChunk extends DataChunk
{
    private $status;

    public function __construct(int $statusCode, array $headers)
    {
        $this->status = [$statusCode, $headers];
    }

    /**
     * {@inheritdoc}
     */
    public function getInformationalStatus(): ?array
    {
        return $this->status;
    }
}
