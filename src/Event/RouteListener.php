<?php

namespace Spqr\Linker\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Spqr\Linker\UrlResolver;

class RouteListener implements EventSubscriberInterface
{
	
	/**
	 * Registers permalink route alias.
	 */
	public function onConfigureRoute( $event, $route )
	{
		if ( $route->getName() == '@linker/id' ) {
			App::routes()->alias(
				dirname( $route->getPath() ) . '/{slug}/{marker_id}',
				'@linker/id',
				[ '_resolver' => 'Spqr\Linker\UrlResolver' ]
			);
		}
	}
	
	/**
	 * Clears resolver cache.
	 */
	public function clearCache()
	{
		App::cache()->delete( UrlResolver::CACHE_KEY );
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function subscribe()
	{
		return [
			'route.configure'      => 'onConfigureRoute',
			'model.target.saved'   => 'clearCache',
			'model.target.deleted' => 'clearCache',
			'model.marker.saved'   => 'clearCache',
			'model.marker.deleted' => 'clearCache'
		];
	}
}