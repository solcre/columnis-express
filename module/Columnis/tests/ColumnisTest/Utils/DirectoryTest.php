<?php

namespace ColumnisTest\Utils;

use PHPUnit_Framework_TestCase;
use Columnis\Utils\Directory as DirectoryUtils;
use ColumnisTest\Bootstrap;

class DirectoryTest extends PHPUnit_Framework_TestCase {
    
    public function testRecursiveSearchByExtension() {
        $files = array(
            Bootstrap::getTestFilesDir() . 'directory-tests/somefilefor.test',
            Bootstrap::getTestFilesDir() . 'directory-tests/anotherfilefor.test',
            Bootstrap::getTestFilesDir() . 'directory-tests/for-recursive/recursivefilefor.test',
            Bootstrap::getTestFilesDir() . 'directory-tests/for-recursive/anotherfilefor.test',
            Bootstrap::getTestFilesDir() . 'directory-tests/for-recursive/more-recursive/morerecursivefilefor.test',
            Bootstrap::getTestFilesDir() . 'directory-tests/for-recursive/more-recursive/anotherfilefor.test'
        );
        
        $search = DirectoryUtils::recursiveSearchByExtension(Bootstrap::getTestFilesDir(), 'test');
        sort($search);
        sort($files);
        $this->assertEquals($files, $search);
    }
    /**
     * @expectedException \Exception
     */
    public function testRecursiveSearchByExtensionWithUnexistantPath() {
        DirectoryUtils::recursiveSearchByExtension('some/unexistant/path', 'test');
    }
    
    public function testIs_subpath() {
        $folder = Bootstrap::getRandString();
        $subfolder = Bootstrap::getRandString();
        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . $folder;
        $subpath = $path . DIRECTORY_SEPARATOR . $subfolder;

        $create = mkdir($path);
        $this->assertTrue($create);

        $create2 = mkdir($subpath);
        $this->assertTrue($create2);


        $res = DirectoryUtils::is_subpath($path, $subpath);
        $this->assertTrue($res);

        $deleted = rmdir($subpath);
        $this->assertTrue($deleted);

        $deleted2 = rmdir($path);
        $this->assertTrue($deleted2);
    }

}
