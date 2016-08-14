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


class Container
{
    private $s = array();

    public function __set($k, $c)
    {
        $this->s[$k] = $c;
    }

    public function __get($k)
    {
        // return $this->s[$k]($this);
        return $this->build($this->s[$k]);
    }

    /**
     * 自动绑定（Auto wiring）自动解析（Automatic Resolution）
     *
     * @param string $className
     * @return object
     * @throws Exception
     */
    public function build($className)
    {
        // 如果是匿名函数（Anonymous functions），也叫闭包函数（closures）
        if ($className instanceof Closure) {
            // 执行闭包函数，并将结果
            return $className($this);
        }

        /** @var ReflectionClass $reflector */
        $reflector = new ReflectionClass($className);

        // 检查类是否可实例化, 排除抽象类abstract和对象接口interface
        if (!$reflector->isInstantiable()) {
            throw new Exception("Can't instantiate this.");
        }

        /** @var ReflectionMethod $constructor 获取类的构造函数 */
        $constructor = $reflector->getConstructor();

        // 若无构造函数，直接实例化并返回
        if (is_null($constructor)) {
            return new $className;
        }

        // 取构造函数参数,通过 ReflectionParameter 数组返回参数列表
        $parameters = $constructor->getParameters();

        // 递归解析构造函数的参数
        $dependencies = $this->getDependencies($parameters);

        // 创建一个类的新实例，给出的参数将传递到类的构造函数。
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    public function getDependencies($parameters)
    {
        $dependencies = [];

        /** @var ReflectionParameter $parameter */
        foreach ($parameters as $parameter) {
            /** @var ReflectionClass $dependency */
            $dependency = $parameter->getClass();

            if (is_null($dependency)) {
                // 是变量,有默认值则设置默认值
                $dependencies[] = $this->resolveNonClass($parameter);
            } else {
                // 是一个类，递归解析
                $dependencies[] = $this->build($dependency->name);
            }
        }

        return $dependencies;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed
     * @throws Exception
     */
    public function resolveNonClass($parameter)
    {
        // 有默认值则返回默认值
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new Exception('I have no idea what to do here.');
    }
}


// ----
$c = new Container();

$c->bar = 'Bar';

$c->foo = function ($c) {
    return new Foo($c->bar);
};

// 从容器中取得Foo
$foo = $c->foo;

$foo->doSomething();

// ----
$di = new Container();

$di->foo = 'Foo';

$foo = $di->foo;

$foo->doSomething();