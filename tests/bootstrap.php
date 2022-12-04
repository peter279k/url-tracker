<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

// remove all final keywords from our test classes to be able to create mock classes for them
DG\BypassFinals::enable();

/**
 * Returns an accessible reflected property for the giving class property.
 *
 * @throws LogicException
 */
function getReflectedProperty(string $className, string $name): ReflectionProperty
{
    if (!class_exists($className)) {
        throw new LogicException("Sorry, you cannot get a reflection property in the class '$className', because it does not exist.");
    }

    $reflection = new ReflectionClass($className);
    $property = $reflection->getProperty($name);
    $property->setAccessible(true);

    return $property;
}
