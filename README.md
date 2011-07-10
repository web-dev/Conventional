Conventional
============

A simple library that provides functionality to access php objects using path strings.

Why?
====
I've written parts of this library repeatedly throughout different projects, and you'll see simmilar classes and functions thouroughout almost every php framework. Rather than rewriting the wheel over and over, I thought to make a small library that will fit 90% of the need for string based object access.

Primarily this libarary is useful when resolving configuration strings against php objects and arrays.

Improvements Welcome
====================
I'll be the first to admit that this library is far from complete, so please fork and commit your improvements.

Examples of use
===============

    use WebDev\Conventional\Resolver;

    $array['my']['foo'] = "a-foo";
    $array['my']['bar'] = "a-bar";

    $resolver = new Resolver();
    echo $resolver($array,"my[foo]"); // a-foo
    echo $resolver($array,"my.bar"); // a-bar

    $object->my->foo = "o-foo";
    $object->setBar("o-bar");

    $resolver = new Resolver();
    echo $resolver->get($object,"my.foo"); // o-foo
    echo $resolver->get($object,"bar"); // o-bar via $object->getBar()

    use WebDev\Conventional\StringTransformer;

    $transform = new StringTransformer("my/{my.foo}/{bar}/{baz}",$object);
    echo $transform(); // my/o-foo/o-bar/{baz}