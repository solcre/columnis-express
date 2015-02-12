<?php

error_reporting(E_ALL);
chdir(__DIR__);

include 'Bootstrap.php';

ColumnisTest\Bootstrap::init();
ColumnisTest\Bootstrap::chroot();
