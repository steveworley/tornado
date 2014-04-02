<?php
namespace Tornado\ApiBundle\Services;
use Tornado\ApiBundle\Services\BaseCommandService;

/**
 * Controls how to display the output from a command.
 * @see dependency_injection
 */
class Complexity extends BaseCommandService
{
  /**
   * Take the expected output from a phploc command and format it
   * into a way that we can use.
   *
   * @param string $output
   *   The STDERR output from a command.
   * @return array
   */
  public function formatOutput($output) {
    $output = str_replace('phploc 2.0.4 by Sebastian Bergmann', '', $output);
    $category = null;
    $formattedOutput = [];

    foreach (explode("\n", $output) as $item) {
      if (!preg_match("/^(\s)/", $item)) {
        $category = trim($item);
      } else {
        $item = trim($item);
        $item = preg_split("/\s\s([\s]+)?/", $item);

        if (count($item) == 2) {
          list($key, $value) = $item;
          $formattedOutput[$category][$key] = $value;
        }
      }
    }

    return $formattedOutput;
  }
}
