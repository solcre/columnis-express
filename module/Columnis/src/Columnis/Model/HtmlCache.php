<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Columnis\Model;

use Exception as BaseException;
use GlobIterator;
use Zend\Cache\Exception;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Stdlib\ErrorHandler;
use ArrayObject;
use Zend\Cache\Storage\Adapter\FilesystemOptions;

class HtmlCache extends Filesystem {
    
    protected $extension = '.html';
    
    function getExtension() {
        return $this->extension;
    }
    function setExtension($extension) {
        $this->extension = $extension;
    }

        
    /**
     * Set options.
     *
     * @param  array|\Traversable|FilesystemOptions $options
     * @return Filesystem
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!$options instanceof FilesystemOptions) {
            $options = new FilesystemOptions($options);
        }

        return parent::setOptions($options);
    }

    /**
     * Get options.
     *
     * @return FilesystemOptions
     * @see setOptions()
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new FilesystemOptions());
        }
        return $this->options;
    }

    /* FlushableInterface */

    /**
     * Flush the whole storage
     *
     * @throws Exception\RuntimeException
     * @return bool
     */
    public function flush()
    {
        $flags = GlobIterator::SKIP_DOTS | GlobIterator::CURRENT_AS_PATHNAME;
        $dir   = $this->getOptions()->getCacheDir();
        $clearFolder = null;
        $clearFolder = function ($dir) use (& $clearFolder, $flags) {
            $it = new GlobIterator($dir . DIRECTORY_SEPARATOR . '*', $flags);
            foreach ($it as $pathname) {
                if ($it->isDir()) {
                    $clearFolder($pathname);
                    rmdir($pathname);
                } else {
                    unlink($pathname);
                }
            }
        };

        ErrorHandler::start();
        $clearFolder($dir);
        $error = ErrorHandler::stop();
        if ($error) {
            throw new Exception\RuntimeException("Flushing directory '{$dir}' failed", 0, $error);
        }

        return true;
    }

    /* ClearExpiredInterface */

    /**
     * Remove expired items
     *
     * @return bool
     *
     * @triggers clearExpired.exception(ExceptionEvent)
     */
    public function clearExpired()
    {
        $options   = $this->getOptions();
        $namespace = $options->getNamespace();
        $prefix    = ($namespace === '') ? '' : $namespace . $options->getNamespaceSeparator();

        $flags = GlobIterator::SKIP_DOTS | GlobIterator::CURRENT_AS_FILEINFO;
        $path  = $options->getCacheDir()
            . str_repeat(DIRECTORY_SEPARATOR . $prefix . '*', $options->getDirLevel())
            . DIRECTORY_SEPARATOR . $prefix . '*' . $this->getExtension();
        $glob = new GlobIterator($path, $flags);
        $time = time();
        $ttl  = $options->getTtl();

        ErrorHandler::start();
        foreach ($glob as $entry) {
            $mtime = $entry->getMTime();
            if ($time >= $mtime + $ttl) {
                $pathname = $entry->getPathname();
                unlink($pathname);

                $tagPathname = substr($pathname, 0, -4) . '.tag';
                if (file_exists($tagPathname)) {
                    unlink($tagPathname);
                }
            }
        }
        $error = ErrorHandler::stop();
        if ($error) {
            $result = false;
            return $this->triggerException(
                __FUNCTION__,
                new ArrayObject(),
                $result,
                new Exception\RuntimeException('Failed to clear expired items', 0, $error)
            );
        }

        return true;
    }

    

        /**
     * Remove items matching given tags.
     *
     * If $disjunction only one of the given tags must match
     * else all given tags must match.
     *
     * @param string[] $tags
     * @param  bool  $disjunction
     * @return bool
     */
    public function clearByTags(array $tags, $disjunction = false)
    {
        if (!$tags) {
            return true;
        }

        $tagCount  = count($tags);
        $options   = $this->getOptions();
        $namespace = $options->getNamespace();
        $prefix    = ($namespace === '') ? '' : $namespace . $options->getNamespaceSeparator();

        $flags = GlobIterator::SKIP_DOTS | GlobIterator::CURRENT_AS_PATHNAME;
        $path  = $options->getCacheDir()
            . str_repeat(DIRECTORY_SEPARATOR . $prefix . '*', $options->getDirLevel())
            . DIRECTORY_SEPARATOR . $prefix . '*.tag';
        $glob = new GlobIterator($path, $flags);

        foreach ($glob as $pathname) {
            $diff = array_diff($tags, explode("\n", $this->getFileContent($pathname)));

            $rem  = false;
            if ($disjunction && count($diff) < $tagCount) {
                $rem = true;
            } elseif (!$disjunction && !$diff) {
                $rem = true;
            }

            if ($rem) {
                unlink($pathname);

                $datPathname = substr($pathname, 0, -4) . $this->getExtension();
                if (file_exists($datPathname)) {
                    unlink($datPathname);
                }
            }
        }

        return true;
    }

    /* IterableInterface */

    /**
     * Get the storage iterator
     *
     * @return FilesystemIterator
     */
    public function getIterator()
    {
        $options   = $this->getOptions();
        $namespace = $options->getNamespace();
        $prefix    = ($namespace === '') ? '' : $namespace . $options->getNamespaceSeparator();
        $path      = $options->getCacheDir()
            . str_repeat(DIRECTORY_SEPARATOR . $prefix . '*', $options->getDirLevel())
            . DIRECTORY_SEPARATOR . $prefix . '*' . $this->getExtension();
        return new FilesystemIterator($this, $path, $prefix);
    }


    /**
     * Internal method to get an item.
     *
     * @param  string  $normalizedKey
     * @param  bool $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetItem(& $normalizedKey, & $success = null, & $casToken = null)
    {
        if (!$this->internalHasItem($normalizedKey)) {
            $success = false;
            return null;
        }

        try {
            $filespec = $this->getFileSpec($normalizedKey);
            $data     = $this->getFileContent($filespec . $this->getExtension());

            // use filemtime + filesize as CAS token
            if (func_num_args() > 2) {
                $casToken = filemtime($filespec . $this->getExtension()) . filesize($filespec . $this->getExtension());
            }
            $success  = true;
            return $data;

        } catch (BaseException $e) {
            $success = false;
            throw $e;
        }
    }

    /**
     * Internal method to get multiple items.
     *
     * @param  array $normalizedKeys
     * @return array Associative array of keys and values
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetItems(array & $normalizedKeys)
    {
        $keys    = $normalizedKeys; // Don't change argument passed by reference
        $result  = array();
        while ($keys) {

            // LOCK_NB if more than one items have to read
            $nonBlocking = count($keys) > 1;
            $wouldblock  = null;

            // read items
            foreach ($keys as $i => $key) {
                if (!$this->internalHasItem($key)) {
                    unset($keys[$i]);
                    continue;
                }

                $filespec = $this->getFileSpec($key);
                $data     = $this->getFileContent($filespec . $this->getExtension(), $nonBlocking, $wouldblock);
                if ($nonBlocking && $wouldblock) {
                    continue;
                } else {
                    unset($keys[$i]);
                }

                $result[$key] = $data;
            }

            // TODO: Don't check ttl after first iteration
            // $options['ttl'] = 0;
        }

        return $result;
    }

    protected function internalHasItem(& $normalizedKey)
    {
        $file = $this->getFileSpec($normalizedKey) . $this->getExtension();
        if (!file_exists($file)) {
            return false;
        }

        $ttl = $this->getOptions()->getTtl();
        if ($ttl) {
            ErrorHandler::start();
            $mtime = filemtime($file);
            $error = ErrorHandler::stop();
            if (!$mtime) {
                throw new Exception\RuntimeException("Error getting mtime of file '{$file}'", 0, $error);
            }

            if (time() >= ($mtime + $ttl)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get metadata
     *
     * @param string $key
     * @return array|bool Metadata on success, false on failure
     */
    public function getMetadata($key)
    {
        $options = $this->getOptions();
        if ($options->getReadable() && $options->getClearStatCache()) {
            clearstatcache();
        }

        return parent::getMetadata($key);
    }

    /**
     * Get metadatas
     *
     * @param array $keys
     * @param array $options
     * @return array Associative array of keys and metadata
     */
    public function getMetadatas(array $keys, array $options = array())
    {
        $options = $this->getOptions();
        if ($options->getReadable() && $options->getClearStatCache()) {
            clearstatcache();
        }

        return parent::getMetadatas($keys);
    }

    /**
     * Get info by key
     *
     * @param string $normalizedKey
     * @return array|bool Metadata on success, false on failure
     */
    protected function internalGetMetadata(& $normalizedKey)
    {
        if (!$this->internalHasItem($normalizedKey)) {
            return false;
        }

        $options  = $this->getOptions();
        $filespec = $this->getFileSpec($normalizedKey);
        $file     = $filespec . $this->getExtension();

        $metadata = array(
            'filespec' => $filespec,
            'mtime'    => filemtime($file)
        );

        if (!$options->getNoCtime()) {
            $metadata['ctime'] = filectime($file);
        }

        if (!$options->getNoAtime()) {
            $metadata['atime'] = fileatime($file);
        }

        return $metadata;
    }

    /**
     * Internal method to get multiple metadata
     *
     * @param  array $normalizedKeys
     * @return array Associative array of keys and metadata
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetMetadatas(array & $normalizedKeys)
    {
        $options = $this->getOptions();
        $result  = array();

        foreach ($normalizedKeys as $normalizedKey) {
            $filespec = $this->getFileSpec($normalizedKey);
            $file     = $filespec . $this->getExtension();

            $metadata = array(
                'filespec' => $filespec,
                'mtime'    => filemtime($file),
            );

            if (!$options->getNoCtime()) {
                $metadata['ctime'] = filectime($file);
            }

            if (!$options->getNoAtime()) {
                $metadata['atime'] = fileatime($file);
            }

            $result[$normalizedKey] = $metadata;
        }

        return $result;
    }

    /**
     * Internal method to store an item.
     *
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @return bool
     * @throws Exception\ExceptionInterface
     */
    protected function internalSetItem(& $normalizedKey, & $value)
    {
        $filespec = $this->getFileSpec($normalizedKey);
        $this->prepareDirectoryStructure($filespec);

        // write data in non-blocking mode
        $wouldblock = null;
        $this->putFileContent($filespec . $this->getExtension(), $value, true, $wouldblock);

        // delete related tag file (if present)
        $this->unlink($filespec . '.tag');

        // Retry writing data in blocking mode if it was blocked before
        if ($wouldblock) {
            $this->putFileContent($filespec . $this->getExtension(), $value);
        }

        return true;
    }

    /**
     * Internal method to store multiple items.
     *
     * @param  array $normalizedKeyValuePairs
     * @return array Array of not stored keys
     * @throws Exception\ExceptionInterface
     */
    protected function internalSetItems(array & $normalizedKeyValuePairs)
    {
        $oldUmask    = null;

        // create an associated array of files and contents to write
        $contents = array();
        foreach ($normalizedKeyValuePairs as $key => & $value) {
            $filespec = $this->getFileSpec($key);
            $this->prepareDirectoryStructure($filespec);

            // *.ext file
            $contents[$filespec . $this->getExtension()] = & $value;

            // *.tag file
            $this->unlink($filespec . '.tag');
        }

        // write to disk
        while ($contents) {
            $nonBlocking = count($contents) > 1;
            $wouldblock  = null;

            foreach ($contents as $file => & $content) {
                $this->putFileContent($file, $content, $nonBlocking, $wouldblock);
                if (!$nonBlocking || !$wouldblock) {
                    unset($contents[$file]);
                }
            }
        }

        // return OK
        return array();
    }

    /**
     * Set an item only if token matches
     *
     * It uses the token received from getItem() to check if the item has
     * changed before overwriting it.
     *
     * @param  mixed  $token
     * @param  string $key
     * @param  mixed  $value
     * @return bool
     * @throws Exception\ExceptionInterface
     * @see    getItem()
     * @see    setItem()
     */
    public function checkAndSetItem($token, $key, $value)
    {
        $options = $this->getOptions();
        if ($options->getWritable() && $options->getClearStatCache()) {
            clearstatcache();
        }

        return parent::checkAndSetItem($token, $key, $value);
    }

    /**
     * Internal method to set an item only if token matches
     *
     * @param  mixed  $token
     * @param  string $normalizedKey
     * @param  mixed  $value
     * @return bool
     * @throws Exception\ExceptionInterface
     * @see    getItem()
     * @see    setItem()
     */
    protected function internalCheckAndSetItem(& $token, & $normalizedKey, & $value)
    {
        if (!$this->internalHasItem($normalizedKey)) {
            return false;
        }

        // use filemtime + filesize as CAS token
        $file  = $this->getFileSpec($normalizedKey) . $this->getExtension();
        $check = filemtime($file) . filesize($file);
        if ($token !== $check) {
            return false;
        }

        return $this->internalSetItem($normalizedKey, $value);
    }

    /**
     * Reset lifetime of an item
     *
     * @param  string $key
     * @return bool
     * @throws Exception\ExceptionInterface
     *
     * @triggers touchItem.pre(PreEvent)
     * @triggers touchItem.post(PostEvent)
     * @triggers touchItem.exception(ExceptionEvent)
     */
    public function touchItem($key)
    {
        $options = $this->getOptions();
        if ($options->getWritable() && $options->getClearStatCache()) {
            clearstatcache();
        }

        return parent::touchItem($key);
    }

    /**
     * Reset lifetime of multiple items.
     *
     * @param  array $keys
     * @return array Array of not updated keys
     * @throws Exception\ExceptionInterface
     *
     * @triggers touchItems.pre(PreEvent)
     * @triggers touchItems.post(PostEvent)
     * @triggers touchItems.exception(ExceptionEvent)
     */
    public function touchItems(array $keys)
    {
        $options = $this->getOptions();
        if ($options->getWritable() && $options->getClearStatCache()) {
            clearstatcache();
        }

        return parent::touchItems($keys);
    }

    /**
     * Internal method to reset lifetime of an item
     *
     * @param  string $normalizedKey
     * @return bool
     * @throws Exception\ExceptionInterface
     */
    protected function internalTouchItem(& $normalizedKey)
    {
        if (!$this->internalHasItem($normalizedKey)) {
            return false;
        }

        $filespec = $this->getFileSpec($normalizedKey);

        ErrorHandler::start();
        $touch = touch($filespec . $this->getExtension());
        $error = ErrorHandler::stop();
        if (!$touch) {
            throw new Exception\RuntimeException("Error touching file '{$filespec}.'" . $this->getExtension(), 0, $error);
        }

        return true;
    }

    /**
     * Remove an item.
     *
     * @param  string $key
     * @return bool
     * @throws Exception\ExceptionInterface
     *
     * @triggers removeItem.pre(PreEvent)
     * @triggers removeItem.post(PostEvent)
     * @triggers removeItem.exception(ExceptionEvent)
     */
    public function removeItem($key)
    {
        $options = $this->getOptions();
        if ($options->getWritable() && $options->getClearStatCache()) {
            clearstatcache();
        }

        return parent::removeItem($key);
    }

    /**
     * Remove multiple items.
     *
     * @param  array $keys
     * @return array Array of not removed keys
     * @throws Exception\ExceptionInterface
     *
     * @triggers removeItems.pre(PreEvent)
     * @triggers removeItems.post(PostEvent)
     * @triggers removeItems.exception(ExceptionEvent)
     */
    public function removeItems(array $keys)
    {
        $options = $this->getOptions();
        if ($options->getWritable() && $options->getClearStatCache()) {
            clearstatcache();
        }

        return parent::removeItems($keys);
    }

    /**
     * Internal method to remove an item.
     *
     * @param  string $normalizedKey
     * @return bool
     * @throws Exception\ExceptionInterface
     */
    protected function internalRemoveItem(& $normalizedKey)
    {
        $filespec = $this->getFileSpec($normalizedKey);
        if (!file_exists($filespec . $this->getExtension())) {
            return false;
        } else {
            $this->unlink($filespec . $this->getExtension());
            $this->unlink($filespec . '.tag');
        }
        return true;
    }

    protected function getFileSpec($normalizedKey) {
        $options = $this->getOptions();
        $namespace = $options->getNamespace();
        $prefix = ($namespace === '') ? '' : $namespace;
        $path = $options->getCacheDir() . DIRECTORY_SEPARATOR;
        return $path . $prefix . DIRECTORY_SEPARATOR . $normalizedKey;
    }
    
}
