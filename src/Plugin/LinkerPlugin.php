<?php

namespace Spqr\Linker\Plugin;

use Pagekit\Application as App;
use Pagekit\Content\Event\ContentEvent;
use Pagekit\Event\EventSubscriberInterface;
use Spqr\Linker\Model\Statistic;
use Spqr\Linker\Model\Target;
use Sunra\PhpSimple\HtmlDomParser;


class LinkerPlugin implements EventSubscriberInterface
{
	/**
	 * @var
	 */
	protected $hrefclass;
	
	/**
	 * @var
	 */
	protected $exclusions;
	
	/**
	 * @var
	 */
	protected $limit;
	
	/**
	 * @var
	 */
	protected $target_window;
	
	
	/**
	 * LinkerPlugin constructor.
	 */
	public function __construct()
	{
		$config              = App::module( 'spqr/linker' )->config();
		$class               = $config[ 'href_class' ];
		$this->hrefclass     = ( $class ? "class='$class'" : "" );
		$this->exclusions    = ( $config[ 'exclusions' ] ? $config[ 'exclusions' ] : [ 'a' ] );
		$this->limit         = $config[ 'limit' ];
		$this->target_window = $config[ 'target' ];
	}
	
	/**
	 * Content plugins callback.
	 *
	 * @param ContentEvent $event
	 */
	public function onContentPlugins( ContentEvent $event )
	{
		$content = $event->getContent();
		
		if ( $content ) {
			foreach ( $this->getTargets() as $target ) {
				$content = $this->replace( $target, $content );
			}
			$event->setContent( $content );
		}
	}
	
	/**
	 * @return array
	 */
	private function getTargets()
	{
		$query   = Target::where( [ 'status = ?' ], [ Target::STATUS_PUBLISHED ] );
		$targets = $query->related( 'marker' )->get();
		
		return $targets;
	}
	
	/**
	 * @param $target
	 * @param $content
	 *
	 * @return mixed|string
	 */
	private function replace( $target, $content )
	{
		$target_window = ( $target->get( 'target' ) ? $target->get( 'target' ) : $this->target_window );
		$hrefclass     = ( $target->get( 'href_class' ) ? $target->get( 'href_class' ) : $this->hrefclass );
		$nofollow      = ( $target->get( 'nofollow' ) ? "rel='nofollow'" : '' );
		
		foreach ( $target->marker as $marker ) {
			if ( $target->get( 'mask' ) ) {
				$url = App::url( '@linker/id', [ 'id' => $target->id, 'marker_id' => $marker->id ], 'base' );
			} else {
				$url = $target->target_url;
			}
			
			switch ( $marker->type ) {
				case 'url' :
					$content = $this->replaceUrl(
						$content,
						$marker,
						$url
					);
					
					break;
				case 'string' :
					$replace =
						"<a href='$url' title='\$0' class='$hrefclass' target='$target_window' $nofollow>\$0</a>";
					$content = $this->replaceString(
						$content,
						$marker,
						$replace,
						$this->exclusions
					);
					break;
				case 'default' : break;
				default:
					$replace = "<a href='$url' class='$hrefclass' target='$target_window' $nofollow>\$0</a>";
					$content = $this->replaceString(
						$content,
						$marker,
						$replace,
						$this->exclusions
					);
			}
		}
		
		return $content;
	}
	
	/**
	 * @param       $content
	 * @param       $marker
	 * @param       $replace
	 * @param array $excludedParents
	 *
	 * @return mixed
	 */
	private function replaceString( $content, $marker, $replace, $excludedParents = [] )
	{
		$dom = HtmlDomParser::str_get_html(
			$content,
			true,
			true,
			DEFAULT_TARGET_CHARSET,
			false,
			DEFAULT_BR_TEXT,
			DEFAULT_SPAN_TEXT
		);
		
		$count = 0;
		
		foreach ( $dom->find( 'text' ) as $element ) {
			if ( !in_array( $element->parent()->tag, $excludedParents ) ) {
				if ( ( $this->limit > 0 ) ) {
					if ( $this->limit > $count ) {
						$tmp = preg_replace(
							'/(?<!\w)' . preg_quote( $marker->value, "/" ) . '(?!\w)/i',
							$replace,
							$element->innertext,
							1
						);
						if ( $tmp != $element->innertext ) {
							$count++;
							$this->addStatistics( $marker );
						}
						$element->innertext = $tmp;
					}
				} else {
					$element->innertext = preg_replace(
						'/(?<!\w)' . preg_quote( $marker->value, "/" ) . '(?!\w)/i',
						$replace,
						$element->innertext,
						1
					);
					$this->addStatistics( $marker );
				}
			}
		}
		
		return $dom->save();
	}
	
	/**
	 * @param $content
	 * @param $marker
	 * @param $replace
	 *
	 * @return mixed|string
	 */
	private function replaceUrl( $content, $marker, $replace )
	{
		$dom = HtmlDomParser::str_get_html(
			$content,
			true,
			true,
			DEFAULT_TARGET_CHARSET,
			false,
			DEFAULT_BR_TEXT,
			DEFAULT_SPAN_TEXT
		);
		
		$target_window = ( $marker->target->get( 'target' ) ? $marker->target->get( 'target' ) : $this->target_window );
		$hrefclass     =
			( $marker->target->get( 'href_class' ) ? $marker->target->get( 'href_class' ) : $this->hrefclass );
		$nofollow      = ( $marker->target->get( 'nofollow' ) ? true : false );
		
		foreach ( $dom->find( 'a' ) as $element ) {
			if ( $element->href == $marker->value ) {
				$element->href = $replace;
				if ( $element->innertext ) {
					$element->title = $element->innertext;
				}
				if ( $hrefclass ) {
					$element->class = $hrefclass;
				}
				if ( $nofollow ) {
					$element->rel = 'nofollow';
				}
				
				if ( $target_window ) {
					$element->target = $target_window;
				}
				
				$this->addStatistics( $marker );
			}
		}
		
		return $dom->save();
	}
	
	/**
	 * @param $marker
	 */
	private function addStatistics( $marker )
	{
		$module = App::module( 'spqr/linker' );
		
		if ( $module->config( 'statistics.collect_statistics' ) && $module->config( 'statistics.collect_views' ) ) {
			
			$statistic            = new Statistic();
			$statistic->marker_id = $marker->id;
			$statistic->type      = 'view';
			$statistic->date      = new \DateTime();
			
			if ( $module->config( 'statistics.collect_referrer' ) ) {
				$statistic->referrer = App::url()->current();
			}
			
			if ( $module->config( 'statistics.collect_ips' ) ) {
				$statistic->ip = App::request()->getClientIp();
			}
			
			$statistic->save();
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function subscribe()
	{
		return [
			'content.plugins' => [ 'onContentPlugins', 3 ],
		];
	}
}