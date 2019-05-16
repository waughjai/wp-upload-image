<?php

declare( strict_types = 1 );
namespace WaughJ\WPUploadImage;

class WPMissingMediaException extends \RuntimeException
{
    public function __construct( $ids )
    {
        $this->ids = ( is_array( $ids ) ) ? $ids : [ $ids ];
        $id_string = implode( ', ', $this->ids );
        parent::__construct( "Can’t create object based on media entry with IDs ({$id_string}). WordPress is missing media with these IDs." );
    }

    public function getMissingIDs() : array
    {
        return $this->ids;
    }

    private $ids;
}
