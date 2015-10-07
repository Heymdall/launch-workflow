<?php

/**
 * Name:    Workflows
 * Description:  This PHP class object provides several useful functions for retrieving, parsing,
 *        and formatting data to be used with Alfred 2 Workflows.
 * Author:    David Ferguson (@jdfwarrior)
 * Revised:    2/9/2013
 * Version:    0.3
 */
class Workflows
{
  private $results;

  /**
   * Description:
   * Class constructor function. Intializes all class variables. Accepts one optional parameter
   * of the workflow bundle id in the case that you want to specify a different bundle id. This
   * would adjust the output directories for storing data.
   */
  function __construct() {
    $this->results = array();
  }


  /**
   * Description:
   * Returns an array of available result items
   *
   * @param none
   * @return array - list of result items
   */
  public function results() {
    return $this->results;
  }

  /**
   * Description:
   * Convert an associative array into XML format
   *
   * @param $a - An associative array to convert
   * @param string $format - format of data being passed (json or array), defaults to array
   * @return string - XML string representation of the array
   */
  public function toxml($a = null, $format = 'array') {

    if ($format == 'json'):
      $a = json_decode($a, TRUE);
    endif;

    if (is_null($a) && !empty($this->results)):
      $a = $this->results;
    elseif (is_null($a) && empty($this->results)):
      return false;
    endif;

    $items = new SimpleXMLElement("<items></items>");  // Create new XML element

    foreach ($a as $b):                // Lop through each object in the array
      $c = $items->addChild('item');        // Add a new 'item' element for each object
      $c_keys = array_keys($b);            // Grab all the keys for that item
      foreach ($c_keys as $key):            // For each of those keys
        if ($key == 'uid'):
          $c->addAttribute('uid', $b[$key]);
        elseif ($key == 'arg'):
          $c->addAttribute('arg', $b[$key]);
        elseif ($key == 'type'):
          $c->addAttribute('type', $b[$key]);
        elseif ($key == 'valid'):
          if ($b[$key] == 'yes' || $b[$key] == 'no'):
            $c->addAttribute('valid', $b[$key]);
          endif;
        elseif ($key == 'autocomplete'):
          $c->addAttribute('autocomplete', $b[$key]);
        elseif ($key == 'icon'):
          if (substr($b[$key], 0, 9) == 'fileicon:'):
            $val = substr($b[$key], 9);
            $c->$key = $val;
            $c->$key->addAttribute('type', 'fileicon');
          elseif (substr($b[$key], 0, 9) == 'filetype:'):
            $val = substr($b[$key], 9);
            $c->$key = $val;
            $c->$key->addAttribute('type', 'filetype');
          else:
            $c->$key = $b[$key];
          endif;
        else:
          $c->$key = $b[$key];
        endif;
      endforeach;
    endforeach;

    return $items->asXML();                // Return XML string representation of the array

  }

  /**
   * Description:
   * Helper function that just makes it easier to pass values into a function
   * and create an array result to be passed back to Alfred
   *
   * @param $uid - the uid of the result, should be unique
   * @param $arg - the argument that will be passed on
   * @param $title - The title of the result item
   * @param $sub - The subtitle text for the result item
   * @param $icon - the icon to use for the result item
   * @param $valid - sets whether the result item can be actioned
   * @param $auto - the autocomplete value for the result item
   * @return array - array item to be passed back to Alfred
   */
  public function result($uid, $arg, $title, $sub, $icon, $valid = 'yes', $auto = null, $type = null) {
    $temp = array(
      'uid' => $uid,
      'arg' => $arg,
      'title' => $title,
      'subtitle' => $sub,
      'icon' => $icon,
      'valid' => $valid,
      'autocomplete' => $auto,
      'type' => $type
    );

    if (is_null($type)):
      unset($temp['type']);
    endif;

    array_push($this->results, $temp);

    return $temp;
  }

}
