<?php

/**
 * Sami Configuration File
 *
 * @internal see https://github.com/FriendsOfPHP/Sami
 */

use Sami\Sami;
use Sami\Parser\Filter\PublicFilter;

return new Sami(
	__DIR__ . '/src/',
	[
		'build_dir' => __DIR__ . '/dev-documentation',
		'cache_dir' => __DIR__ . '/tmp',
		'filter'    => new PublicFilter(),
	]
);