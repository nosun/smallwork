<?php

 /*******************************************************************************
 * 依赖注入容器 Dependency Injection Container
 *
 * 如果一个组件有很多依赖， 我们需要创建多个参数的setter方法​​来传递依赖关系，或者建立一个多个参数的
 * 构造函数来传递它们，另外在使用组件前还要每次都创建依赖，这让我们的代码像这样不易维护。
 *
 * DI Container 就是为了解决这个问题。
 *
 * 管理应用程序中的『全局』对象（包括实例化、处理依赖关系）.
 * 可以延时加载对象（仅用到时才创建对象）.
 * 促进编写可重用、可测试和松耦合的代码.
 *
 * 使用了延迟静态绑定 static:: 方法
 *
 *
 *******************************************************************************/

class Bim
{
    public function doSomething()
    {
        echo __METHOD__ . PHP_EOL;
    }
}

class Bar
{
    private $bim;

    public function __construct(Bim $bim)
    {
        $this->bim = $bim;
    }

    public function doSomething()
    {
        $this->bim->doSomething();
        echo __METHOD__ . PHP_EOL;
    }
}

class Foo
{
    private $bar;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }

    public function doSomething()
    {
        $this->bar->doSomething();
        echo __METHOD__ . PHP_EOL;
    }
}


class IoC
{
    protected static $registry = [];

    public static function bind($name, Callable $resolver)
    {
        static::$registry[$name] = $resolver;
    }

    public static function make($name)
    {
        if (isset(static::$registry[$name])) {
            $resolver = static::$registry[$name];
            return $resolver();
        }
        throw new Exception('Alias does not exist in the IoC registry.');
    }
}

IoC::bind('bim', function () {
    return new Bim();
});

IoC::bind('bar', function () {
    return new Bar(IoC::make('bim'));
});

IoC::bind('foo', function () {
    return new Foo(IoC::make('bar'));
});


// 从容器中取得Foo
$foo = IoC::make('foo');
$foo->doSomething();