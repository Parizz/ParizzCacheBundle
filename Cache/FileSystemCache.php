<?php

namespace Parizz\CacheBundle\Cache;

use Doctrine\Common\Cache\CacheProvider;

/**
 * Filesystem cache driver.
 */
class FilesystemCache extends CacheProvider
{
    private $path;

    /**
     * Sets the path.
     *
     * @param string $path
     */
    public function setPath($path)
    {
        if (!is_dir($path) && !mkdir($path, 0777, true)) {
            throw new \RuntimeException('Unable to create the "'.$path.'" directory.');
        }

        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return unserialize(file_get_contents($this->getFullPath($id)));
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return file_exists($this->getFullPath($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = false)
    {
        return (bool) file_put_contents($this->getFullPath($id), serialize($data));
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return @unlink($this->getFullPath($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return false;
    }

    /**
    * {@inheritdoc}
    */
    protected function doGetStats()
    {
        return null;
    }

    private function getFullPath($id)
    {
        return $this->path . '/' . md5($id);
    }
}