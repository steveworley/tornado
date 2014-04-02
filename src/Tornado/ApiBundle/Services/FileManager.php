<?php
// src/Tornday/ApiBundle/Services/FileManager.php
namespace Tornado\ApiBundle\Services;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class FileManager
{
  /**
   * @var string
   */
  private $file = null;

  /**
   * @var string
   */
  private $uploadpath = null;

  /**
   * @var string
   */
  private $absolutePath = null;

  /**
   * @var FileSystem
   */
  private $fileSystem = null;

  /**
   * @var string
   */
  private $filePath = null;

  /**
   * Build a FileManager object.
   *
   * @param string $file
   */
  public function __construct($path, $fileSystem)
  {
    $this->setUploadPath($path);
    $this->setFileSystem($fileSystem);
  }

  /**
   * Set \$file
   *
   * @param UploadedFile $file
   * @return FileManager
   */
  public function setFile($file)
  {
    $this->file = $file;

    return $this;
  }

  /**
   * Get \file
   *
   * @return Uploadedfile
   */
  public function getFile()
  {
    return $this->file;
  }

  public function getFileName()
  {
    $path = $this->getFilePath();
    $parts = explode('/', $path);
    $part = end($parts);

    return str_replace('.php', '', $part);
  }

  /**
   * Set \UploadPath
   *
   * @param string $path
   * @return FileManager
   */
  public function setUploadPath($path)
  {
    $this->uploadPath = $path;

    return $this;
  }

  /**
   * Get \uploadPath
   *
   * @return string
   */
  public function getUploadPath()
  {
    return $this->uploadPath;
  }

  /**
   * Set \absolutePath
   *
   * @param string $path
   * @return FileManager
   */
  public function setAbsolutePath($path)
  {
    $this->absolutePath = $path;

    return $this;
  }

  /**
   * Get \absolutePath
   *
   * @return string
   */
  public function getAbsolutePath()
  {
    return $this->absolutePath;
  }

  /**
   * Set \fileSystem
   *
   * @param FileSystem
   * @return FileManager
   */
  public function setFileSystem($fileSystem)
  {
    $this->fileSystem = $fileSystem;

    return $this;
  }

  /**
   * Get \fileSystem
   *
   * @return FileSystem
   *
   */
  public function getFileSystem()
  {
    return $this->fileSystem;
  }

  /**
   * Set \filePath
   *
   * @param string
   * @return FileManager
   */
  public function setFilePath($filePath)
  {
    $this->filePath = $filePath;

    return $this;
  }

  public function getFilePath()
  {
    return $this->filePath;
  }

  private function createUnique()
  {
    $unique = md5(uniqid(rand(), 1));
    return substr($unique, -8);
  }

  /**
   * Create generate an unique upload directory for this file.
   *
   * @return FileManager
   */
  public function createUploadDirectory()
  {
    if (null === $this->getUploadPath()) {
      throw new \Exception("Cannot create upload path");
    }

    $uploadDirectory = $this->createUnique();

    if (!$this->getFileSystem()->exists($this->getuploadPath() . "/{$uploadDirectory}")) {
      $this->getFileSystem()->mkdir($this->getUploadPath() . "/{$uploadDirectory}");
    }

    $this->setFilePath("/{$uploadDirectory}");
    return $this;
  }

  public function createFile()
  {
    // Attempt to create the upload directory.
    $this->createUploadDirectory();

    if (null === $this->getFilePath()) {
      throw new \Exception("Cannot create file @" . $this->getUploadPath());
    }

    $path = $this->getUploadPath() . $this->getFilePath();
    $file = $this->createUnique() . ".php";

    if (!$this->getFileSystem()->exists("{$path}/{$file}")) {
      $this->getFileSystem()->touch("{$path}/{$file}");
    }

    if (is_string($this->getFile())) {
      // file_put_contents("{$path}/{$file}", $this->getFile());
      $this->getFilesystem()->dumpFile("{$path}/{$file}", $this->getFile());
    } else {
      $this->getFile()->move($path, $file);
    }

    $this->setFilePath($this->getFilePath() . "/{$file}");

    return "{$path}/{$file}";
  }
}
