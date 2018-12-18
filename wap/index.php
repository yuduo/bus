<?php
//入口文件，单一入口
//载入核心启动类
include "framework/core/Framework.class.php";
//实例化对象并调用方法
// $app = new Framework();
// $app->run();
//echo getcwd();
Framework::run();

