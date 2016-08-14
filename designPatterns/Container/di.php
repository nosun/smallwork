<?php


 /******************************************************************
  *
  * 依赖注入 Dependency Injection
  *
  * 类 Boo 在初始化的时候, 因为依赖 Far, 所以先生成 Far, 通过构造方法
  * 中的申明, 自动注入。
  *
  ******************************************************************/

Class Far {

    public function __construct(){
        echo "far is here" . PHP_EOL;
    }

    public function doSomeThing(){
        echo __METHOD__ . PHP_EOL;
    }
}

Class Boo {

    protected $far;

    public function __construct(Far $far){
        $this->far = $far;
    }

    public function doSomeThing(){
        $this->far->doSomeThing();
        echo __METHOD__ . PHP_EOL;
    }
}

$boo = new Boo(new Far);

$boo->doSomeThing();
