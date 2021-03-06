<?php

namespace PiedWeb\CMSBundle\EventListener;

use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @require dataManager
 * cacheManager
 * filterManager
 */
trait MediaCacheGeneratorTrait
{
    protected function generateCache($path)
    {
        //todo: get it from parameters (config) ?!
        foreach (['small_thumb', 'thumb', 'height_300', 'xs', 'sm', 'md', 'lg', 'xl', 'default'] as $filter) {
            $this->storeImageInCache($path, $filter);
        }
    }

    protected function storeImageInCache($path, $filter)
    {
        try {
            try {
                $binary = $this->dataManager->find($filter, $path);
            } catch (NotLoadableException $e) {
                if ($defaultImageUrl = $this->dataManager->getDefaultImageUrl($filter)) {
                    return $defaultImageUrl;
                }

                throw new NotFoundHttpException('Source image could not be found', $e);
            }

            $this->cacheManager->store(
                $this->filterManager->applyFilter($binary, $filter),
                $path,
                $filter
            );
        } catch (\RuntimeException $e) {
            $msg = 'Unable to create image for path "%s" and filter "%s". '.'Message was "%s"';
            throw new \RuntimeException(sprintf($msg, $path, $filter, $e->getMessage()), 0, $e);
        }
    }
}
