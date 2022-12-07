<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

/*
 * Fix phpstan errors when mocking final classes:
 * "Return type of call to method PHPUnit\Framework\MockObject\MockBuilder<Lee\Request\Client>::getMock() contains unresolvable type."
 *
 * The following code fixes the problem and removes the final keywords from
 * test classes at runtime before phpstan starts doing static analyzing.
 *
 * See the following link for more information:
 * @link https://github.com/phpstan/phpstan-phpunit/issues/57
 */

DG\BypassFinals::enable();
