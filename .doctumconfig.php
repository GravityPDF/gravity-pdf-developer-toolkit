<?php

/**
 * Sami Configuration File
 *
 * @internal see https://github.com/FriendsOfPHP/Sami
 */

use Doctum\Doctum;
use Doctum\Parser\Filter\PublicFilter;

return new Doctum(
	__DIR__ . '/src/',
	[
		'build_dir'           => __DIR__ . '/dev-documentation',
		'cache_dir'           => __DIR__ . '/tmp/doctum',
		'filter'              => new PublicFilter(),
		'include_parent_data' => false,
	]
);