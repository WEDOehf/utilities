<?php declare(strict_types = 1);

namespace Wedo\Utilities\Tests;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Wedo\Utilities\FileSystemService;

/**
 * Tests as much as it can using a virtual file system. Glob is not tested.
 *
 * @see https://github.com/mikey179/vfsStream/wiki/Known-Issues
 */
class FileSystemServiceTest extends TestCase
{

	private vfsStreamDirectory $root;

	public function setUp(): void
	{
		$this->root = vfsStream::setup('root');
		$file = new vfsStreamFile('file');
		$file->setContent('file contents');
		$this->root->addChild($file);
		$this->root->addChild(new vfsStreamFile('protectedfile', 0222));
		$this->root->addChild(new vfsStreamFile('readablefile', 0444));
		$this->root->addChild(new vfsStreamFile('writablefile', 0666));
		$this->root->addChild(new vfsStreamDirectory('directory'));
	}

	public function testExists(): void
	{
		$fs = new FileSystemService();
		$this->assertFalse($fs->exists(vfsStream::url('root/bogusfile')));
		$this->assertTrue($fs->exists(vfsStream::url('root/file')));
	}

	public function testIsFile(): void
	{
		$fs = new FileSystemService();
		$this->assertFalse($fs->isFile(vfsStream::url('root/bogusfile')));
		$this->assertFalse($fs->isFile(vfsStream::url('root/directory')));
		$this->assertTrue($fs->isFile(vfsStream::url('root/file')));
	}

	public function testIsDirectory(): void
	{
		$fs = new FileSystemService();
		$this->assertFalse($fs->isDirectory(vfsStream::url('root/file')));
		$this->assertFalse($fs->isDirectory(vfsStream::url('root/bogusdirectory')));
		$this->assertTrue($fs->isDirectory(vfsStream::url('root/directory')));
	}

	public function testIsWritable(): void
	{
		$fs = new FileSystemService();
		$this->assertFalse($fs->isWritable(vfsStream::url('root/readablefile')));
		$this->assertTrue($fs->isWritable(vfsStream::url('root/writablefile')));
	}

	public function testIsReadable(): void
	{
		$fs = new FileSystemService();
		$this->assertFalse($fs->isReadable(vfsStream::url('root/protectedfile')));
		$this->assertTrue($fs->isReadable(vfsStream::url('root/readablefile')));
	}

	public function testRemove(): void
	{
		$fs = new FileSystemService();
		$this->root->addChild(new vfsStreamFile('tempremovefile'));
		$fs->remove(vfsStream::url('root/tempremovefile'));
		$this->assertFalse($this->root->hasChild('tempremovefile'));
	}

	public function testWrite(): void
	{
		$fs = new FileSystemService();
		$this->assertFalse($this->root->hasChild('tempwritefile'));
		$fs->write(vfsStream::url('root/tempwritefile'), '');
		$this->assertTrue($this->root->hasChild('tempwritefile'));
	}

	public function testRead(): void
	{
		$fs = new FileSystemService();
		$this->assertEquals('file contents', $fs->read(vfsStream::url('root/file')));
	}

	public function testMakeDirectory(): void
	{
		$fs = new FileSystemService();
		$fs->makeDirectory(vfsStream::url('root/tempmakedirectory/tempsubdirectory'));
		$this->assertTrue($this->root->hasChild('tempmakedirectory/tempsubdirectory'));
	}

	public function testRemoveDirectory(): void
	{
		$fs = new FileSystemService();
		$this->root->addChild(new vfsStreamDirectory('tempremovedirectory'));
		$fs->removeDirectory(vfsStream::url('root/tempremovedirectory'));
		$this->assertFalse($this->root->hasChild('tempremovedirectory'));
	}

	public function testGetFilesize(): void
	{
		$fs = new FileSystemService();
		$fs = $fs->getFilesize(__DIR__ . '/../LICENSE');

		$this->assertEquals(1066, $fs);
	}

}
