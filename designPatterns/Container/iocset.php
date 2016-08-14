<?php


 /******************************************************************
  *
  * 控制反转 Inversion of Control
  *
  * 一般的情况, 当初始化 A 对象时需要用到 B 对象时, 称为 A 对象对 B 对象有依赖,
  * B 对象是 A 对象的一个成员属性或者成员属性所依赖的配置, 这种情况我们称作为高层级
  * 的对象依赖于低层级的对象.
  *
  * 这种写法的问题在于, 因为 A 依赖于 B, 如果在类中写死了构造的话,一旦业务调整,需
  * 要将 B 替换成 C 就需要对 A 进行改动, 抑或者 B 类中的方法有改动, 需要去同时修改
  * A类.
  *
  * 控制反转主要解决这类问题, 让底层的对象依赖高层的对象, 把控制关系反转过来.
  *
  * 怎么实现呢? 通过接口,契约来实现,让实现依赖于抽象.
  * 通俗来说,需要首先根据业务需要, 根据 A 对象的需要抽象出 A 与 B 之间的接口.
  * B 的实现需要遵照 接口进行实现. 这样在 A 初始化的时候,可以通过在构造函数中申明构造
  * 函数依赖的接口来实现注入.
  *
  * Setter 方法, 实现依赖注入, 可以通过 set 方法注入, 也可以通过 属性注入.
  *
  ******************************************************************/

interface ISWriter{
    public function write($text);
}

Class ISFar{

    protected $writer;
    public function __construct(){}

    // 通过 set 方法实现依赖注入
    public function set(ISWriter $writer){
        $this->writer = $writer;
    }

    public function doWrite($text){
        $this->writer->write($text);
    }
}

class ISWriterA implements ISWriter{
    public function write($text){
        echo __CLASS__.':'.$text.PHP_EOL;
    }
}

class ISWriterB implements ISWriter{
    public function write($text){
        echo __CLASS__.":".$text.PHP_EOL;
    }
}

$far = new ISFar();

$far->set(new ISWriterA());
$far->doWrite('hello, world');

$far->set(new ISWriterB());
$far->doWrite('hello, world');
