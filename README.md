# Blueprint Bundle

The bundle provides a way to manage test data for the Doctrine ORM.

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

Blueprint::register('comment', 'Acme\BlogBundle\Entity\Comment', function($coment, $blueprint) {
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
$post->getTitle(); // 'Title1'
$post->getBody();  // 'BodyBodyBody'

$comment = $blueprint->create('comment');
$comment->getBody();              // 'CommentCommentComment'
$comment->getPost()->getTitle();  // 'Title2'

// optional
$comment2 = $blueprint->create('comment', array('post' => $post));
$comment2->getPost()->getTitle(); // Title1
```

## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request

## Copyright

Copyright (C) 2013 Dai Akatsuka, released under the MIT License.
