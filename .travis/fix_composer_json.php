<?php

$composerFilePath = '/../composer.json';

copy(__DIR__.$composerFilePath, __DIR__.'/../composer.json.bak');

$composerJson = json_decode(file_get_contents(__DIR__.$composerFilePath), true);

$composerJson['config']['platform']['ext-amqp'] = '1.9.3';

file_put_contents(__DIR__.$composerFilePath, json_encode($composerJson, JSON_PRETTY_PRINT));
