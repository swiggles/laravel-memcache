<?php

class MemcacheStoreTest extends PHPUnit_Framework_TestCase
{

    public function testGetReturnsNullWhenNotFound()
    {
        $memcache = $this->getMock('Memcache', array('get'));
        $memcache->expects($this->once())->method('get')->with($this->equalTo('foo:bar'))->will(
            $this->returnValue(null)
        );
        $store = new Swiggles\Memcache\MemcacheStore($memcache, 'foo');
        $this->assertNull($store->get('bar'));
    }


    public function testMemcacheValueIsReturned()
    {
        $memcache = $this->getMock('Memcache', array('get'));
        $memcache->expects($this->once())->method('get')->will($this->returnValue('bar'));
        $store = new Swiggles\Memcache\MemcacheStore($memcache);
        $this->assertEquals('bar', $store->get('foo'));
    }


    public function testSetMethodProperlyCallsMemcache()
    {
        $memcache = $this->getMock('Memcache', array('set'));
        $memcache->expects($this->once())->method('set')->with(
            $this->equalTo('foo'),
            $this->equalTo('bar'),
            false,
            $this->equalTo(60)
        );
        $store = new Swiggles\Memcache\MemcacheStore($memcache);
        $store->put('foo', 'bar', 60);
    }


    public function testIncrementMethodProperlyCallsMemcache()
    {
        $memcache = $this->getMock('Memcache', array('increment'));
        $memcache->expects($this->once())->method('increment')->with($this->equalTo('foo'), $this->equalTo(5));
        $store = new Swiggles\Memcache\MemcacheStore($memcache);
        $store->increment('foo', 5);
    }


    public function testDecrementMethodProperlyCallsMemcache()
    {
        $memcache = $this->getMock('Memcache', array('decrement'));
        $memcache->expects($this->once())->method('decrement')->with($this->equalTo('foo'), $this->equalTo(5));
        $store = new Swiggles\Memcache\MemcacheStore($memcache);
        $store->decrement('foo', 5);
    }


    public function testStoreItemForeverProperlyCallsMemcached()
    {
        $memcache = $this->getMock('Memcache', array('set'));
        $memcache->expects($this->once())->method('set')->with(
            $this->equalTo('foo'),
            $this->equalTo('bar'),
            $this->equalTo(0)
        );
        $store = new Swiggles\Memcache\MemcacheStore($memcache);
        $store->forever('foo', 'bar');
    }


    public function testForgetMethodProperlyCallsMemcache()
    {
        $memcache = $this->getMock('Memcache', array('delete'));
        $memcache->expects($this->once())->method('delete')->with($this->equalTo('foo'));
        $store = new Swiggles\Memcache\MemcacheStore($memcache);
        $store->forget('foo');
    }

}