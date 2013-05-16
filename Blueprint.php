<?php

namespace Dakatsuka\BlueprintBundle;

use Doctrine\ORM\EntityManager;

/**
 * Class Blueprint
 *
 * @package Dakatsuka\BlueprintBundle
 */
class Blueprint
{
    /**
     * @var array
     */
    private static $blueprints = array();

    /**
     * @var int
     */
    private static $sequence = 0;

    /**
     * @var EntityManager
     */
    private static $em;

    /**
     * Register blueprint
     *
     * @param string $name
     * @param string $entity
     * @param Callable $callback
     *
     * inline example:
     *     Blueprint::register('user', 'Acme\DomainBundle\Entity\User', function($user) {
     *         $user->setEmail('.....');
     *         $user->setUsername('.....');
     *         $user->setPassword('.....');
     *     });
     */
    public static function register($name, $entity, Callable $callback)
    {
        static::$blueprints[$name] = array('entity' => '\\'.$entity, 'callback' => $callback);
    }

    /**
     * Dependency Injection
     *
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        static::$em = $em;
    }

    /**
     * Load blueprints from directory
     *
     * @param $path
     */
    public function loadFromDirectory($path)
    {
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                    if ($file !== '.' && $file !== '..' && $extension == 'php') {
                        require $path . '/' . $file;
                    }
                }
                closedir($dh);
            }
        }
    }

    /**
     * Create object based on blueprint
     *
     * @param string $name
     * @param array $params
     * @return object
     *
     * TODO: Error handling
     */
    public function create($name, $params = array())
    {
        $entity = $this->build($name, $params);

        static::$em->persist($entity);
        static::$em->flush();
        static::$em->refresh($entity);
        static::$em->clear($entity);

        return $entity;
    }

    /**
     * Build object based on blueprint
     *
     * @param string $name
     * @param array $params
     * @return object
     *
     * TODO: Error handling
     */
    public function build($name, $params = array())
    {
        $blueprint = static::$blueprints[$name];
        $className = $blueprint['entity'];
        $callback  = $blueprint['callback'];

        $entity = new $className();
        $callback($entity, $this);
        $this->overrideParameters($entity, $params);

        return $entity;
    }

    /**
     * Get sequence
     *
     * @return int
     */
    public function sequence()
    {
        static::$sequence++;
        return static::$sequence;
    }

    /**
     * Override parameters
     *
     * @param $entity
     * @param array $params
     */
    private function overrideParameters($entity, array $params)
    {
        foreach ($params as $key => $value) {
            call_user_func_array(array($entity, 'set' . ucfirst($key)), array($value));
        }
    }
}
