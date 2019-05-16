<?php

declare( strict_types = 1 );
namespace WaughJ\WPUploadImage;

use WaughJ\HTMLImage\HTMLImage;
use WaughJ\FileLoader\FileLoader;;
use WaughJ\FileLoader\MissingFileException;
use function WaughJ\WPGetImageSizes\WPGetImageSizes;

class WPUploadImage extends HTMLImage
{
	//
	//  PUBLIC
	//
	/////////////////////////////////////////////////////////

		public function __construct( int $id, string $size = null, array $attributes = [] )
		{
			$show_version = $attributes[ 'show-version' ] ?? true;

			if ( $size === null ) { $size = 'full'; };

			if ( $size === 'responsive' )
			{
				$image_sizes = WPGetImageSizes();
				$image = wp_get_attachment_image_src( $id, $image_sizes[ 0 ]->getSlug() );
				$src = $image[ 0 ];
				if ( $src )
				{
					try
					{
						$attributes = self::setSrcsetAndSizes( $id, $image_sizes, $attributes, $show_version );
					}
					catch ( MissingFileException $e )
					{
						throw new MissingFileException( $e->getFilename(), new HTMLImage( $src, null, $e->getFallbackContent() ) );
					}
				}
			}
			else
			{
				$image = wp_get_attachment_image_src( $id, $size );
				try
				{
					$src = ( $image ) ? self::getFormattedURL( $image, $show_version ) : null;
				}
				catch ( MissingFileException $e )
				{
					throw new MissingFileException( $e->getFilename(), new HTMLImage( $e->getFallbackContent(), null, $attributes ) );
				}
			}

			if ( $src )
			{
				parent::__construct( $src, null, $attributes );
			}
			else
			{
				parent::__construct( '' );
			}
		}

		public static function init() : void
		{
			$uploads = wp_upload_dir();
			self::$loader = new FileLoader([ 'directory-url' => $uploads[ 'url' ], 'directory-server' => $uploads[ 'path' ] ]);
		}

		public static function getFormattedURL( array &$wp_image_source_object, bool $show_version ) : string
		{
			$local_url = self::turnAbsolutePathIntoLocal( $wp_image_source_object[ 0 ] );
			return ( $show_version ) ? self::$loader->getSourceWithVersion( $local_url ) : self::$loader->getSource( $local_url );
		}

		public static function getFileLoader() : FileLoader
		{
			return self::$loader;
		}



	//
	//  PUBLIC
	//
	/////////////////////////////////////////////////////////

		private static function setSrcsetAndSizes( int $id, array $image_sizes, array $attributes, bool $show_version ) : array
		{
			$missing_urls = [];
			$srcset_strings = [];
			$size_strings = [];

			$prev_width = -1;
			$number_of_sizes = count( $image_sizes );
			for ( $i = 0; $i < $number_of_sizes; $i++ )
			{
				$wp_image_source_object = wp_get_attachment_image_src( $id, $image_sizes[ $i ]->getSlug() );

				try
				{
					$url = self::getFormattedURL( $wp_image_source_object, $show_version );
				}
				catch ( MissingFileException $e )
				{
					$missing_urls[] = $e->getFilename();
					$url = $e->getFallbackContent();
				}

				// Full-size image may be smaller than max size in uploads setting,
				// so we may reach the last image before going through all o' the sizes.
				// If we do, end loop now.
				$width = $wp_image_source_object[ 1 ];
				if ( $prev_width === $width )
				{
					break;
				}
				$prev_width = $width;

				$srcset_strings[] = "{$url} {$width}w";
				$is_last_size = ( $i === $number_of_sizes - 1 );
				$size_strings[] = ( $is_last_size ) ? "{$width}px" : "(max-width: {$width}px) {$width}px";
			}

			// Stringify srcs & sizes to be used as HTML attributes.
			$attributes[ 'srcset' ] = implode( ', ', $srcset_strings );
			$attributes[ 'sizes' ] = implode( ', ', $size_strings );

			if ( !empty( $missing_urls ) )
			{
				throw new MissingFileException( $missing_urls, $attributes );
			}

			return $attributes;
		}

		private static function turnAbsolutePathIntoLocal( string $absolute ) : string
		{
			return str_replace( self::$loader->getDirectoryURL()->getStringURL(), '', $absolute );
		}

		private static $loader;
}
WPUploadImage::init();
