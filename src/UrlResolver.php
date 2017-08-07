<?php

namespace Spqr\Linker;

use Pagekit\Application as App;
use Pagekit\Routing\ParamsResolverInterface;
use Spqr\Linker\Model\Target;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class UrlResolver implements ParamsResolverInterface
{
	const CACHE_KEY = 'spqr.linker.routing';
	
	/**
	 * @var bool
	 */
	protected $cacheDirty = false;
	
	/**
	 * @var array
	 */
	protected $cacheEntries;
	
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->cacheEntries = App::cache()->fetch( self::CACHE_KEY ) ? : [];
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function match( array $parameters = [] )
	{
		if ( isset( $parameters[ 'id' ] ) ) {
			return $parameters;
		}
		
		if ( !isset( $parameters[ 'slug' ] ) ) {
			App::abort( 404, 'Target not found.' );
		}
		
		$slug = $parameters[ 'slug' ];
		
		$id = false;
		
		foreach ( $this->cacheEntries as $entry ) {
			if ( $entry[ 'slug' ] === $slug ) {
				$id = $entry[ 'id' ];
			}
		}
		
		if ( !$id ) {
			
			if ( !$target = Target::where( compact( 'slug' ) )->first() ) {
				App::abort( 404, 'Target not found.' );
			}
			
			$this->addCache( $target );
			$id = $target->id;
		}
		
		$parameters[ 'id' ] = $id;
		
		return $parameters;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function generate( array $parameters = [] )
	{
		$id = $parameters[ 'id' ];
		
		if ( !isset( $this->cacheEntries[ $id ] ) ) {
			
			if ( !$target = Target::where( compact( 'id' ) )->first() ) {
				throw new RouteNotFoundException( 'Target not found.' );
			}
			
			$this->addCache( $target );
		}
		
		$meta                 = $this->cacheEntries[ $id ];
		$parameters[ 'slug' ] = $meta[ 'slug' ];
		
		unset( $parameters[ 'id' ] );
		
		return $parameters;
	}
	
	public function __destruct()
	{
		if ( $this->cacheDirty ) {
			App::cache()->save( self::CACHE_KEY, $this->cacheEntries );
		}
	}
	
	protected function addCache( $item )
	{
		$this->cacheEntries[ $item->id ] = [
			'id'     => $item->id,
			'slug'   => $item->slug,
			'year'   => $item->date->format( 'Y' ),
			'month'  => $item->date->format( 'm' ),
			'day'    => $item->date->format( 'd' ),
			'hour'   => $item->date->format( 'H' ),
			'minute' => $item->date->format( 'i' ),
			'second' => $item->date->format( 's' ),
		];
		
		$this->cacheDirty = true;
	}
}