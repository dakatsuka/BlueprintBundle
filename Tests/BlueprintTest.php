<?php

namespace Dakatsuka\BlueprintBundle\Tests;

use Dakatsuka\BlueprintBundle\Blueprint;

class BlueprintTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testSequence()
    {
        $blueprint = new Blueprint();
        $this->assertEquals(1, $blueprint->sequence());
        $this->assertEquals(2, $blueprint->sequence());
        $this->assertEquals(3, $blueprint->sequence());
    }

    public function testBuild()
    {
        Blueprint::register('post', 'Dakatsuka\BlueprintBundle\Tests\Entity\Post', function($post, $blueprint) {
            $post->setTitle('title'.$blueprint->sequence());
            $post->setBody('Body');
        });

        $blueprint = new Blueprint();
        $post = $blueprint->build('post');

        $this->assertInstanceOf('Dakatsuka\BlueprintBundle\Tests\Entity\Post', $post);
        $this->assertRegExp('/^title[0-9]+$/', $post->getTitle());
        $this->assertEquals('Body', $post->getBody());
    }

    public function testBuildWithOptions()
    {
        Blueprint::register('post', 'Dakatsuka\BlueprintBundle\Tests\Entity\Post', function($post, $blueprint) {
            $post->setTitle('title'.$blueprint->sequence());
            $post->setBody('Body');
        });

        $blueprint = new Blueprint();
        $post = $blueprint->build('post', array('body' => 'Article'));

        $this->assertInstanceOf('Dakatsuka\BlueprintBundle\Tests\Entity\Post', $post);
        $this->assertRegExp('/^title[0-9]+$/', $post->getTitle());
        $this->assertEquals('Article', $post->getBody());
    }

    public function testCreate()
    {
        Blueprint::register('post', 'Dakatsuka\BlueprintBundle\Tests\Entity\Post', function($post, $blueprint) {
            $post->setTitle('title'.$blueprint->sequence());
            $post->setBody('Body');
        });

        $em = \Mockery::mock('Doctrine\ORM\EntityManager');
        $em->shouldReceive('persist')->once();
        $em->shouldReceive('flush')->once();
        $em->shouldReceive('refresh')->once();
        $em->shouldReceive('detach')->once();

        $blueprint = new Blueprint();
        $blueprint->setEntityManager($em);

        $post = $blueprint->create('post', array('body' => 'Article'));

        $this->assertInstanceOf('Dakatsuka\BlueprintBundle\Tests\Entity\Post', $post);
        $this->assertRegExp('/^title[0-9]+$/', $post->getTitle());
        $this->assertEquals('Article', $post->getBody());
    }

    public function testCreateWithOtherEntityManager()
    {
        Blueprint::register('post', 'Dakatsuka\BlueprintBundle\Tests\Entity\Post', function($post, $blueprint) {
            $post->setTitle('title'.$blueprint->sequence());
            $post->setBody('Body');
        });

        $em = \Mockery::mock('Doctrine\ORM\EntityManager');
        $em->shouldReceive('persist')->once();
        $em->shouldReceive('flush')->once();
        $em->shouldReceive('refresh')->once();
        $em->shouldReceive('detach')->once();

        $blueprint = new Blueprint();

        $post = $blueprint->create('post', array(), $em);

        $this->assertInstanceOf('Dakatsuka\BlueprintBundle\Tests\Entity\Post', $post);
        $this->assertRegExp('/^title[0-9]+$/', $post->getTitle());
        $this->assertEquals('Body', $post->getBody());
    }
}