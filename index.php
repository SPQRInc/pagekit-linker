<?php

use Pagekit\Application;
use Spqr\Linker\Event\RouteListener;
use Spqr\Linker\Event\StatisticListener;
use Spqr\Linker\Plugin\LinkerPlugin;


return [
	'name' => 'spqr/linker',
	'type' => 'extension',
	'main' => function( Application $app ) {
	},
	
	'autoload' => [
		'Spqr\\Linker\\' => 'src'
	],
	
	'nodes'  => [
		'linker' => [
			'name'       => '@linker',
			'label'      => 'Linker',
			'controller' => 'Spqr\\Linker\\Controller\\SiteController',
			'protected'  => true,
			'frontpage'  => false
		]
	],
	'routes' => [
		'/linker'     => [
			'name'       => '@linker',
			'controller' => [
				'Spqr\\Linker\\Controller\\LinkerController',
				'Spqr\\Linker\\Controller\\TargetController',
				'Spqr\\Linker\\Controller\\StatisticController',
				'Spqr\\Linker\\Controller\\SiteController'
			]
		],
		'/api/linker' => [
			'name'       => '@linker/api',
			'controller' => [
				'Spqr\\Linker\\Controller\\TargetApiController',
				'Spqr\\Linker\\Controller\\StatisticApiController'
			]
		]
	],
	
	'widgets' => [],
	
	'menu' => [
		'linker'           => [
			'label'  => 'Linker',
			'url'    => '@linker/target',
			'active' => '@linker/target*',
			'icon'   => 'spqr/linker:icon.svg'
		],
		'linker: targets'  => [
			'parent' => 'linker',
			'label'  => 'Targets',
			'icon'   => 'spqr/linker:icon.svg',
			'url'    => '@linker/target',
			'access' => 'linker: manage targets',
			'active' => '@linker/target*'
		],
		'linker: statistics'  => [
			'parent' => 'linker',
			'label'  => 'Statistics',
			'icon'   => 'spqr/linker:icon.svg',
			'url'    => '@linker/statistic',
			'access' => 'linker: manage statistics',
			'active' => '@linker/statistic*'
		],
		'linker: settings' => [
			'parent' => 'linker',
			'label'  => 'Settings',
			'url'    => '@linker/settings',
			'access' => 'linker: manage settings'
		]
	],
	
	'permissions' => [
		'linker: manage settings' => [
			'title' => 'Manage settings'
		],
		'linker: manage targets'  => [
			'title' => 'Manage targets'
		],
		'linker: manage statistics'  => [
			'title' => 'Manage statistics'
		]
	],
	
	'settings' => '@linker/settings',
	
	'resources' => [
		'spqr/linker:' => ''
	],
	
	'config' => [
		'target'         => '_blank',
		'nofollow'       => true,
		'redirect'       => 301,
		'limit'          => 1,
		'href_class'     => '',
		'mask'           => true,
		'exclusions'     => [ 'a', 'pre', 'code', 'img', 'script', 'style' ],
		'statistics'     => [
			'collect_statistics' => true,
			'collect_views'      => false,
			'collect_clicks'     => true,
			'collect_ips'        => false,
			'collect_referrer'   => false
		],
		'items_per_page' => 20,
	],
	
	'events' => [
		'boot'         => function( $event, $app ) {
			$app->subscribe(
				new LinkerPlugin,
				new RouteListener,
				new StatisticListener()
			);
		},
		'site'         => function( $event, $app ) {
		},
		'view.scripts' => function( $event, $scripts ) use ( $app ) {
			$scripts->register( 'target-marker', 'spqr/linker:app/bundle/target-marker.js', '~target-edit' );
		}
	]
];