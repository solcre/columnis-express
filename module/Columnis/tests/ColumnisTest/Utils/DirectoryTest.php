<?php

namespace ColumnisTest\Utils;

use PHPUnit_Framework_TestCase;
use Columnis\Utils\Directory as DirectoryUtils;

class DirectoryTest extends PHPUnit_Framework_TestCase {

    private function getRandString() {
        $randNameAr = array_merge(range('a', 'z'), range(0, 9), array('-', '_'));
        shuffle($randNameAr);
        return implode('', array_splice($randNameAr, rand(0, (count($randNameAr) - 1))));
    }

    public function testIs_subpath() {
        $folder = $this->getRandString();
        $subfolder = $this->getRandString();
        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . $folder;
        $subpath = $path . DIRECTORY_SEPARATOR . $subfolder;

        $create = mkdir($path);
        $this->assertEquals($create, true);

        $create2 = mkdir($subpath);
        $this->assertEquals($create2, true);


        $res = DirectoryUtils::is_subpath($path, $subpath);
        $this->assertEquals($res, true);

        $deleted = rmdir($subpath);
        $this->assertEquals($deleted, true);

        $deleted2 = rmdir($path);
        $this->assertEquals($deleted2, true);
    }

}
