<?php

namespace Lsr\Helpers\Files;

interface FileInterface
{

	public function getName() : string;

	public function getError() : int;

	public function getErrorMessage() : string;

	public function getExtension() : string;

	public function getBaseName() : string;

	public function getFileSize() : int;
}