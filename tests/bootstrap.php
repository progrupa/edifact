<?php

call_user_func(function() {
    if ( ! is_file($autoloadFile = __DIR__.'/../../../../vendor/autoload.php')) {
        throw new \RuntimeException('Did not find vendor/autoload.php. Did you run "composer install --dev"?');
    }

    $loader = require $autoloadFile;
    $loader->add('EDI\Tests', __DIR__);

    \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace('EDI\Annotations', __DIR__.'/../src');
});
