<?php

namespace Lsr\Helpers\Files;

use RuntimeException;

class UploadedFile implements FileInterface
{

	private string $extension;
	private string $baseName;
	private int    $fileSize;

	public function __construct(
		public readonly string $name,
		public readonly string $tmpName,
		public readonly int    $error = UPLOAD_ERR_OK,
	) {
	}

	public static function parseUploaded(string $name) : ?UploadedFile {
		if (!isset($_FILES[$name])) {
			return null;
		}
		if (is_array($_FILES[$name]['name'])) {
			throw new RuntimeException('Cannot use UploadedFile::parseUploaded() for an array of files. Use UploadedFile::parseUploadedMultiple() instead.');
		}
		return new self($_FILES[$name]['name'], $_FILES[$name]['tmp_name'], $_FILES[$name]['error']);
	}

	/**
	 * @param string $name
	 *
	 * @return UploadedFile[]|UploadedFile[][]|UploadedFile[][][]
	 */
	public static function parseUploadedMultiple(string $name) : array {
		if (!isset($_FILES[$name])) {
			return [];
		}

		return self::parseFilesMultiple($_FILES[$name], $_FILES[$name]['name']);
	}

	/**
	 * @param array{
	 *     name:array<string|int,string|array<int|string,string>>,
	 *     tmp_name:array<string|int,string|array<int|string,string>>,
	 *     error:array<string|int,int|array<int|string,int>>
	 *   }                            $files
	 * @param array<string|int,mixed> $iterableFiles
	 * @param array<string|int>       $keys
	 *
	 * @return UploadedFile[]|UploadedFile[][]|UploadedFile[][][]
	 */
	private static function parseFilesMultiple(array $files, array $iterableFiles, array $keys = []) : array {
		$out = [];
		foreach ($iterableFiles as $key => $file) {
			if (is_array($file)) {
				$keys2 = $keys;
				$keys2[] = $key;
				$out[$key] = self::parseFilesMultiple($files, $file, $keys2);
				continue;
			}
			$names = $files['name'];
			$tmpNames = $files['tmp_name'];
			$errors = $files['error'];
			foreach ($keys as $key2) {
				$names = $names[$key2];
				$tmpNames = $tmpNames[$key2];
				$errors = $errors[$key2];
			}
			if (!is_string($names) || !is_string($tmpNames) || !is_int($errors)) {
				throw new RuntimeException('Cannot create new UploadedFile object for keys: '.implode('.', $keys));
			}
			$out[$key] = new self($names, $tmpNames, $errors);
		}
		return $out;
	}

	/**
	 * @return string
	 */
	public function getName() : string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getTmpName() : string {
		return $this->tmpName;
	}

	/**
	 * @return int
	 */
	public function getError() : int {
		return $this->error;
	}

	/**
	 * @return string File's name without path and extension
	 */
	public function getBaseName() : string {
		if (!isset($this->baseName)) {
			$this->baseName = basename($this->name, '.'.pathinfo($this->name, PATHINFO_EXTENSION));
		}
		return $this->baseName;
	}

	public function getFileSize() : int {
		if (!isset($this->fileSize)) {
			$this->fileSize = filesize($this->tmpName);
		}
		return $this->fileSize;
	}

	/**
	 * @return string Error message, or empty string if no error
	 */
	public function getErrorMessage() : string {
		return match ($this->error) {
			UPLOAD_ERR_OK => '',
			UPLOAD_ERR_INI_SIZE => lang('Uploaded file is too large', context: 'errors'),
			UPLOAD_ERR_FORM_SIZE => lang('Form size is to large', context: 'errors'),
			UPLOAD_ERR_PARTIAL => lang('The uploaded file was only partially uploaded.', context: 'errors'),
			UPLOAD_ERR_CANT_WRITE => lang('Failed to write file to disk.', context: 'errors'),
			default => lang('Error while uploading a file.', context: 'errors'),
		};
	}

	/**
	 * Check if the uploaded file has a valid extension
	 *
	 * @param string ...$valid List of allowed extensions
	 *
	 * @return bool
	 */
	public function validateExtension(string ...$valid) : bool {
		$extension = $this->getExtension();
		return in_array($extension, array_map('strtolower', $valid), true);
	}

	public function getExtension() : string {
		if (!isset($this->extension)) {
			$this->extension = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
		}
		return $this->extension;
	}

	public function save(string $path) : bool {
		return move_uploaded_file($this->tmpName, $path);
	}

}