<?php

declare( strict_types = 1 );
namespace WaughJ\WPUploadImage
{
	use WaughJ\HTMLImage\HTMLImage;
	use WaughJ\FileLoader\FileLoader;
	use function WaughJ\WPGetImageSizes\WPGetImageSizes;
	use function WaughJ\TestHashItem\TestHashItemExists;

	class WPUploadImage extends HTMLImage
	{
		public function __construct( int $id, string $size = null, array $attributes = [] )
		{
			if ( $size === null ) { $size = 'full'; };

			if ( $size === 'responsive' )
			{
				$image_sizes = WPGetImageSizes();
				$image = wp_get_attachment_image_src( $id, $image_sizes[ 0 ]->getSlug() );
				$src = $image[ 0 ];
				if ( $src )
				{
					$attributes = self::getSrcsetAndSizes( $id, $image_sizes, $attributes );
				}
			}
			else
			{
				$image = wp_get_attachment_image_src( $id, $size );
				$src = $image[ 0 ];
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

		public static function getFileLoader() : FileLoader
		{
			$uploads = wp_upload_dir();
			$loader = new FileLoader([ 'directory-url' => $uploads[ 'url' ], 'directory-server' => $uploads[ 'path' ] ]);
			return $loader;
		}

		public static function filterUploadDir( string $url ) : string
		{
			return str_replace( wp_upload_dir()[ 'baseurl' ], '', $url );
		}

		private static function getSrcsetAndSizes( int $id, array $image_sizes, array $attributes ) : array
		{
			$src_strings = [];
			$size_strings = [];
			$number_of_sizes = count( $image_sizes );
			$prev_width = -1;
			for ( $i = 0; $i < $number_of_sizes; $i++ )
			{
				$size = wp_get_attachment_image_src( $id, $image_sizes[ $i ]->getSlug() );
				$url = $size[ 0 ];
				$width = $size[ 1 ];
				if ( $prev_width === $width )
				{
					break;
				}
				$src_strings[] = $url . " {$width}w";
				$size_strings[] = ( $i === $number_of_sizes - 1 )
					? "{$width}px"
					: "(max-width: {$width}px) {$width}px";
				$prev_width = $width;
			}
			$attributes[ 'srcset' ] = implode( ', ', $src_strings );
			$attributes[ 'sizes' ] = implode( ', ', $size_strings );
			return $attributes;
		}
	}
}
