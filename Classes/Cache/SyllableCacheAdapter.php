<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 11.07.16
 * Time: 09:47
 */

namespace Syllable\Cache;


use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class SyllableCacheAdapter implements \Syllable_Cache_Interface
{
    /**
     * @var FrontendInterface
     */
    private $t3Cache;

    /**
     * SyllableCacheAdapter constructor.
     * @param FrontendInterface $t3Cache
     */
    public function __construct(FrontendInterface $t3Cache)
    {
        $this->t3Cache = $t3Cache;
    }

    public function __set($key, $value)
    {
        $this->t3Cache->set($key, $value);
    }

    public function __get($key)
    {
        return $this->t3Cache->get($key);
    }

    public function __isset($key)
    {
        return $this->t3Cache->has($key);
    }

    public function __unset($key)
    {
        return $this->t3Cache->remove($key);
    }

    public function open($language)
    {
    }

    public function close()
    {
    }
}