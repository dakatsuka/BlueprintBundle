# Blueprint Bundle [![Build Status](https://travis-ci.org/dakatsuka/BlueprintBundle.png?branch=master)](https://travis-ci.org/dakatsuka/BlueprintBundle)

The bundle provides a way to manage test data for the Doctrine ORM.

## Installation

Add this lines to your composer.json:

```json
{
    "require": {
        "dakatsuka/blueprint-bundle": "1.1.0"
    },
}
```

And then execute:

```bash
$ php composer.phar install
```

And import a BlueprintBundle to AppKernel.php:

```php
if (in_array($this->getEnvironment(), array('dev', 'test'))) {
    $bundles[] = new Dakatsuka\BlueprintBundle\DakatsukaBlueprintBundle();
}
```


## Usage

src/Acme/BlogBundle/Tests/Blueprints/post.php:

```php
namespace Acme\BlogBundle\Tests\Blueprints;

use Dakatsuka\BlueprintBundle\Blueprint;

Blueprint::register('post', 'Acme\BlogBundle\Entity\Post', function($post, $blueprint) {
    $post->setTitle('Title'.$blueprint->sequence());
    $post->setBody('BodyBodyBody');
});
```

src/Acme/BlogBundle/Tests/Blueprints/comment.php:
```php
namespace Acme\BlogBundle\Tests\Blueprints;

use Dakatsuka\BlueprintBundle\Blueprint;

Blueprint::register('comment', 'Acme\BlogBundle\Entity\Comment', function($comment, $blueprint) {
    $comment->setPost($blueprint->create('post'));
    $comment->setBody('CommentCommentComment');
});
```

How to use:
```php
static::$kernel = static::createKernel();
static::$kernel->boot();
static::$container = static::$kernel->getContainer();

$blueprint = static::$container->get('dakatsuka.blueprint');
$blueprint->loadFromDirectory(static::$kernel->getRootDir() . '/../src/Acme/BlogBundle/Tests/Blueprints');

$post = $blueprint->create('post');
$this->assertEquals('Title1', $post->getTitle());
$this->assertEquals('BodyBodyBody', $post->getBody());

$comment = $blueprint->create('comment');
$this->assertEquals('CommentCommentComment', $comment->getBody());
$this->assertEquals('Title2', $comment->getPost()->getTitle());

// optional
$comment2 = $blueprint->create('comment', array('post' => $post));
$this->assertSame($post, $comment2->getPost());
```

## Tips
Nested blueprint (required cascade={"persist"} option):

```php
Blueprint::register('post', 'Acme\BlogBundle\Entity\Post', function($post, $blueprint) {
    $post->setTitle('Title'.$blueprint->sequence());
    $post->setBody('BodyBodyBody');
    $post->getComments()->add($blueprint->build('comment', array('post' => $post));
    $post->getComments()->add($blueprint->build('comment', array('post' => $post));
    $post->getComments()->add($blueprint->build('comment', array('post' => $post));
});
```

## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request

### Test

```bash
$ make phpunit
$ make test
```

## Copyright

Copyright (C) 2013 Dai Akatsuka, released under the MIT License.
