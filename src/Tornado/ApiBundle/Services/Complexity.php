<?php
namespace Tornado\ApiBundle\Services;
use Tornado\ApiBundle\Services\CommandInterface;

/**
 * Controls how to display the output from a command.
 * @see dependency_injection
 */
class Complexity extends CommandInterface
{
  /**
   * Construct a complexity command
   */
  public function __construct($fileSystem, $command)
  {
    parent::__construct($fileSystem, $command);
    $this->setType('complexity');
  }

  /**
   * Take the expected output from a phploc command and format it
   * into a way that we can use.
   *
   * @param string $output
   *   The command line output from a command.
   *
   * @return array
   */
  public function formatOutput($output) {
    $output = str_replace('phploc 2.0.4 by Sebastian Bergmann', '', $output);
    $category = null;
    $formattedOutput = [];

    foreach (explode("\n", $output) as $item) {
      $trimItem = trim($item);

      if (!preg_match("/^(\s)/", $item)) {

        // The phploc command indents all the properties - if we find a line
        // without any leading spaces we can assume that this is going to be
        // a category. We assign $category to this item so we can use it
        // for subsequent lines.
        $category = $trimItem;
        continue;
      }

      // Each line is separated by whitespace - if we split on 2 (or more)
      // we should find the actual information from the output of phploc.
      $item = preg_split("/\s\s([\s]+)?/", $trimItem);

      if (count($item) == 2) {

        // Only information from the command output that can be successfully
        // split into 2 parts is useful informaiton. We can assign lines
        // that match to $formattedOutput under $category ready for return.
        list($key, $value) = $item;
        $formattedOutput[$category][$key] = $value;
      }
    }

    return $formattedOutput;
  }
}
