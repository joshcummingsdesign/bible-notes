<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by kadencewp on 07-March-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocks\Symfony\Component\HttpFoundation\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use KadenceWP\KadenceBlocks\Symfony\Component\HttpFoundation\Request;
use KadenceWP\KadenceBlocks\Symfony\Component\HttpFoundation\Response;

/**
 * Asserts that the response is in the given format.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ResponseFormatSame extends Constraint
{
    private $request;
    private $format;

    public function __construct(Request $request, ?string $format)
    {
        $this->request = $request;
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return 'format is '.($this->format ?? 'null');
    }

    /**
     * @param Response $response
     *
     * {@inheritdoc}
     */
    protected function matches($response): bool
    {
        return $this->format === $this->request->getFormat($response->headers->get('Content-Type'));
    }

    /**
     * @param Response $response
     *
     * {@inheritdoc}
     */
    protected function failureDescription($response): string
    {
        return 'the Response '.$this->toString();
    }

    /**
     * @param Response $response
     *
     * {@inheritdoc}
     */
    protected function additionalFailureDescription($response): string
    {
        return (string) $response;
    }
}
