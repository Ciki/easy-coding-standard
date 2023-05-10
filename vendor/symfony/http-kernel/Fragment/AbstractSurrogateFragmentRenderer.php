<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202305\Symfony\Component\HttpKernel\Fragment;

use ECSPrefix202305\Symfony\Component\HttpFoundation\Request;
use ECSPrefix202305\Symfony\Component\HttpFoundation\Response;
use ECSPrefix202305\Symfony\Component\HttpKernel\Controller\ControllerReference;
use ECSPrefix202305\Symfony\Component\HttpKernel\HttpCache\SurrogateInterface;
use ECSPrefix202305\Symfony\Component\HttpKernel\UriSigner;
/**
 * Implements Surrogate rendering strategy.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class AbstractSurrogateFragmentRenderer extends RoutableFragmentRenderer
{
    /**
     * @var \Symfony\Component\HttpKernel\HttpCache\SurrogateInterface|null
     */
    private $surrogate;
    /**
     * @var \Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface
     */
    private $inlineStrategy;
    /**
     * @var \Symfony\Component\HttpKernel\UriSigner|null
     */
    private $signer;
    /**
     * The "fallback" strategy when surrogate is not available should always be an
     * instance of InlineFragmentRenderer.
     *
     * @param FragmentRendererInterface $inlineStrategy The inline strategy to use when the surrogate is not supported
     */
    public function __construct(SurrogateInterface $surrogate = null, FragmentRendererInterface $inlineStrategy, UriSigner $signer = null)
    {
        $this->surrogate = $surrogate;
        $this->inlineStrategy = $inlineStrategy;
        $this->signer = $signer;
    }
    /**
     * {@inheritdoc}
     *
     * Note that if the current Request has no surrogate capability, this method
     * falls back to use the inline rendering strategy.
     *
     * Additional available options:
     *
     *  * alt: an alternative URI to render in case of an error
     *  * comment: a comment to add when returning the surrogate tag
     *
     * Note, that not all surrogate strategies support all options. For now
     * 'alt' and 'comment' are only supported by ESI.
     *
     * @see Symfony\Component\HttpKernel\HttpCache\SurrogateInterface
     * @param string|\Symfony\Component\HttpKernel\Controller\ControllerReference $uri
     */
    public function render($uri, Request $request, array $options = []) : Response
    {
        if (!$this->surrogate || !$this->surrogate->hasSurrogateCapability($request)) {
            if ($uri instanceof ControllerReference && $this->containsNonScalars($uri->attributes)) {
                throw new \InvalidArgumentException('Passing non-scalar values as part of URI attributes to the ESI and SSI rendering strategies is not supported. Use a different rendering strategy or pass scalar values.');
            }
            return $this->inlineStrategy->render($uri, $request, $options);
        }
        if ($uri instanceof ControllerReference) {
            $uri = $this->generateSignedFragmentUri($uri, $request);
        }
        $alt = $options['alt'] ?? null;
        if ($alt instanceof ControllerReference) {
            $alt = $this->generateSignedFragmentUri($alt, $request);
        }
        $tag = $this->surrogate->renderIncludeTag($uri, $alt, $options['ignore_errors'] ?? \false, $options['comment'] ?? '');
        return new Response($tag);
    }
    private function generateSignedFragmentUri(ControllerReference $uri, Request $request) : string
    {
        return (new FragmentUriGenerator($this->fragmentPath, $this->signer))->generate($uri, $request);
    }
    private function containsNonScalars(array $values) : bool
    {
        foreach ($values as $value) {
            if (\is_scalar($value) || null === $value) {
                continue;
            }
            if (!\is_array($value) || $this->containsNonScalars($value)) {
                return \true;
            }
        }
        return \false;
    }
}
