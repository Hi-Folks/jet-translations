<?php

namespace HiFolks\JetTranslations;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HiFolks\JetTranslations\Skeleton\SkeletonClass
 */
class JetTranslationsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'jet-translations';
    }
}
