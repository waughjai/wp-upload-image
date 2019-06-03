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
			if ( $size === null ) { $size = 'full'; };

			// Configure fallback source.
			$image_sizes = WPGetImageSizes();
			$image = ( $size === 'responsive' ) ? wp_get_attachment_image_src( $id, $image_sizes[ 0 ]->getSlug() ) : wp_get_attachment_image_src( $id, $size );
			if ( $image === false )
			{
				throw new WPMissingMediaException( $id );
			}

			// If responsive, configure srcset.
			if ( $size === 'responsive' )
			{
				$attributes = self::setSrcsetAndSizes( $id, $image_sizes, $attributes );
			}

			$src = self::turnAbsolutePathIntoLocal( $image );
			parent::__construct( $src, self::$loader, $attributes );
		}

		public static function init() : void
		{
			$uploads = wp_upload_dir();
			self::$loader = new FileLoader([ 'directory-url' => $uploads[ 'baseurl' ], 'directory-server' => $uploads[ 'basedir' ] ]);
		}

		public static function getFileLoader() : FileLoader
		{
			return self::$loader;
		}

		public static function turnAbsolutePathIntoLocal( array $wp_image_source_object ) : string
		{
			return str_replace( self::$loader->getDirectoryURL()->getStringURL(), '', $wp_image_source_object[ 0 ] );
		}



	//
	//  PUBLIC
	//
	/////////////////////////////////////////////////////////

		private static function setSrcsetAndSizes( int $id, array $image_sizes, array $attributes ) : array
		{
			$srcset_strings = [];

			$prev_width = -1;
			$number_of_sizes = count( $image_sizes );
			for ( $i = 0; $i < $number_of_sizes; $i++ )
			{
				$wp_image_source_object = wp_get_attachment_image_src( $id, $image_sizes[ $i ]->getSlug() );
				$url = self::turnAbsolutePathIntoLocal( $wp_image_source_object );

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
			}

			// Stringify srcs to be used as HTML attributes.
			$attributes[ 'srcset' ] = implode( ', ', $srcset_strings );
			return $attributes;
		}

		private static $loader;
}
WPUploadImage::init();
