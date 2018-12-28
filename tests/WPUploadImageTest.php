<?php

use PHPUnit\Framework\TestCase;
use WaughJ\WPUploadImage\WPUploadImage;

require_once( 'MockWordPress.php' );

class WPUploadImageTest extends TestCase
{
	public function testBasic()
	{
		$image = new WPUploadImage( 1 );
		$this->assertEquals( $image->getHTML(), '<img src="https://www.example.com/wp-content/uploads/2018/12/demo.png?m=' . filemtime( getcwd() . '/2018/12/demo.png'  ) . '" alt="" />' );
	}

	public function testNonexistentImage()
	{
		$image = new WPUploadImage( 33 );
		$this->assertEquals( $image->getHTML(), '<img src="" alt="" />' );
	}

	public function testWithSpecialSize()
	{
		$image = new WPUploadImage( 2, 'medium' );
		$this->assertEquals( $image->getHTML(), '<img src="https://www.example.com/wp-content/uploads/2018/12/photo-300x300.jpg?m=' . filemtime( getcwd() . '/2018/12/photo-300x300.jpg'  ) . '" alt="" />' );
	}

	public function testNoCache()
	{
		$image = new WPUploadImage( 1, null, [ 'show-version' => false ] );
		$this->assertEquals( $image->getHTML(), '<img src="https://www.example.com/wp-content/uploads/2018/12/demo.png" alt="" />' );
	}

	public function testWithExtraAttributes()
	{
		$image = new WPUploadImage( 1, null, [ 'class' => 'center-img portrait', 'width' => 800, 'height' => 600, 'alt' => 'King' ] );
		$this->assertContains( ' src="https://www.example.com/wp-content/uploads/2018/12/demo.png?m=' . filemtime( getcwd() . '/2018/12/demo.png'  ) . '"', $image->getHTML() );
		$this->assertContains( ' width="800"', $image->getHTML() );
		$this->assertContains( ' height="600"', $image->getHTML() );
		$this->assertContains( ' class="center-img portrait"', $image->getHTML() );
		$this->assertContains( ' alt="King"', $image->getHTML() );
	}

	public function testAutoSrcset()
	{
		$image = new WPUploadImage( 2, 'responsive' );
		$this->assertContains( 'srcset="https://www.example.com/wp-content/uploads/2018/12/photo-150x150.jpg', $image->getHTML() );
	}
}
