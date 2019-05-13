<?php

use PHPUnit\Framework\TestCase;
use WaughJ\WPUploadImage\WPUploadImage;

require_once( 'MockWordPress.php' );

class WPUploadImageTest extends TestCase
{
	public function testBasic()
	{
		$image = new WPUploadImage( 1 );
		$this->assertStringContainsString( ' src="https://www.example.com/wp-content/uploads/2018/12/demo.png?m=', $image->getHTML() );
		$this->assertStringContainsString( ' alt="" />', $image->getHTML() );
	}

	public function testNonexistentImage()
	{
		$image = new WPUploadImage( 33 );
		$this->assertEquals( $image->getHTML(), '<img src="" alt="" />' );
	}

	public function testWithSpecialSize()
	{
		$image = new WPUploadImage( 2, 'medium' );
		$this->assertStringContainsString( ' src="https://www.example.com/wp-content/uploads/2018/12/photo-300x300.jpg', $image->getHTML() );
	}

	public function testWithExtraAttributes()
	{
		$image = new WPUploadImage( 1, null, [ 'class' => 'center-img portrait', 'width' => 800, 'height' => 600, 'alt' => 'King', 'id' => 'king-img' ] );
		$this->assertStringContainsString( ' src="https://www.example.com/wp-content/uploads/2018/12/demo.png', $image->getHTML() );
		$this->assertStringContainsString( ' width="800"', $image->getHTML() );
		$this->assertStringContainsString( ' height="600"', $image->getHTML() );
		$this->assertStringContainsString( ' class="center-img portrait"', $image->getHTML() );
		$this->assertStringContainsString( ' alt="King"', $image->getHTML() );
		$this->assertStringContainsString( ' id="king-img"', $image->getHTML() );
	}

	public function testAutoSrcset()
	{
		$image = new WPUploadImage( 2, 'responsive' );
		$this->assertStringContainsString( 'srcset="https://www.example.com/wp-content/uploads/2018/12/photo-150x150.jpg', $image->getHTML() );
	}

	public function testBasicWithoutVersion()
	{
		$image = new WPUploadImage( 1, null, [ 'class' => 'center-img portrait', 'width' => 800, 'height' => 600, 'alt' => 'King', 'id' => 'king-img', 'show-version' => false ] );
		$this->assertStringNotContainsString( '?m=', $image->getHTML() );
		$this->assertStringNotContainsString( ' show-version="', $image->getHTML() );
	}
}
