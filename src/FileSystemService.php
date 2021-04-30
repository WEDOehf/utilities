<?php declare(strict_types = 1);

namespace Wedo\Utilities;

class FileSystemService
{

	public function exists(string $path): bool
	{
		return file_exists($path);
	}

	public function isFile(string $path): bool
	{
		return is_file($path);
	}

	public function isDirectory(string $path): bool
	{
		return is_dir($path);
	}

	public function isWritable(string $path): bool
	{
		return is_writable($path);
	}

	public function isReadable(string $path): bool
	{
		return is_readable($path);
	}

	/**
	 * Performs `unlink` on the given path.
	 */
	public function remove(string $path): bool
	{
		return unlink($path);
	}

	public function write(string $path, mixed $content): bool
	{
		return (bool) file_put_contents($path, $content);
	}

	public function read(string $path): bool|string
	{
		return file_get_contents($path);
	}

	/**
	 * @return string[]
	 * @codeCoverageIgnore
	 */
	public function find(string $pattern): ?array
	{
		$result = glob($pattern);

		return $result === false ? null : $result;
	}

	public function makeDirectory(string $path, int $mode = 0777, bool $recursive = true): bool
	{
		return mkdir($path, $mode, $recursive);
	}

	public function removeDirectory(string $path): bool
	{
		return rmdir($path);
	}

	public function rename(string $oldName, string $newName): bool
	{
		return rename($oldName, $newName);
	}

	public function getFilesize(string $path): ?int
	{
		$result = filesize($path);

		return $result === false ? null : $result;
	}

	/**
	 * @return array<int, string>|null
	 * @codeCoverageIgnore
	 */
	public function scanDirectory(string $path): ?array
	{
		$result = scandir($path);

		return $result === false ? null : $result;
	}

}
