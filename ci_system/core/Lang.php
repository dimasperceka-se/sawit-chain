<?php

if (!defined('BASEPATH')) {
   exit('No direct script access allowed');
}

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package      CodeIgniter
 * @author      ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license      http://codeigniter.com/user_guide/license.html
 * @link      http://codeigniter.com
 * @since      Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Language Class
 *
 * @package      CodeIgniter
 * @subpackage   Libraries
 * @category   Language
 * @author      ExpressionEngine Dev Team
 * @link      http://codeigniter.com/user_guide/libraries/language.html
 */
class CI_Lang
{

   /**
    * List of translations
    *
    * @var array
    */
   public $language = array();
   public $CI;

   /**
    * List of loaded language files
    *
    * @var array
    */
   public $is_loaded = array();

   /**
    * Constructor
    *
    * @access   public
    */
   public function __construct()
   {
      log_message('debug', "Language Class Initialized");
   }

   // --------------------------------------------------------------------

   /**
    * Load a language file
    *
    * @access   public
    * @param   mixed   the name of the language file to be loaded. Can be an array
    * @param   string   the language (english, etc.)
    * @param   bool   return loaded array of translations
    * @param    bool   add suffix to $langfile
    * @param    string   alternative path to look for language file
    * @return   mixed
    */

    public function load($langfile = '', $idiom = '', $return = false, $add_suffix = true, $alt_path = '')
    {
        // Calling early before CI reformats them
        $this->set = 'general';

        if (is_array($langfile)) {
            foreach ($langfile as $_lang)
                $this->load($_lang);
            return $this->language;
        }

        if ($add_suffix == TRUE) {
            $langfile = str_replace('_lang.', '', $langfile) . '_lang';
        }

        $langfile .= '_old.php';

        $config = & get_config();

        $deft_lang = (!isset($config['language'])) ? 'english' : $config['language'];
        $idiom = ($lang == '') ? $deft_lang : $lang;
        $this->idiom = $idiom;

        if (in_array($langfile . '_lang' . EXT, $this->is_loaded, TRUE))
            return $this->language;

        // Determine where the language file is and load it
        if ($alt_path != '' && file_exists($alt_path . 'language/' . $idiom . '/' . $langfile)) {
            include($alt_path . 'language/' . $idiom . '/' . $langfile);
        } else {
            $found = FALSE;
            foreach (get_instance()->load->get_package_paths(TRUE) as $package_path) {
                if (file_exists($package_path . 'language/' . $idiom . '/' . $langfile)) {
                    include($package_path . 'language/' . $idiom . '/' . $langfile);
                    $found = TRUE;
                    break;
                }
            }

            if ($found !== TRUE) {
                $database_lang = $this->_get_from_db();
                if (!empty($database_lang)) {
                    $this->language = array_merge($this->language, $database_lang);
                    $this->is_loaded[] = $langfile . '_lang' . EXT;
                    // $js_array = json_encode($this->language);
                    // echo "<script>var lang_arr = " . $js_array . ";</script>\n";
                    unset($database_lang);
                }
                // show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
            }
        }

        log_message('debug', 'Language file loaded: language/' . $idiom . '/' . $langfile);
        return TRUE;
    }

/*
public function load($langfile = '', $idiom = '', $return = false, $add_suffix = true, $alt_path = '')
{
// Calling early before CI reformats them
$this->set = $langfile;

$langfile = str_replace('.php', '', $langfile);

if ($add_suffix == true) {
$langfile = str_replace('_lang.', '', $langfile) . '_lang';
}

$langfile .= '.php';

if (in_array($langfile, $this->is_loaded, true)) {
return;
}

$config = &get_config();

if ($idiom == '') {
$deft_lang = (!isset($config['language'])) ? 'english' : $config['language'];
$idiom     = ($deft_lang == '') ? 'english' : $deft_lang;
}
$this->idiom = $idiom;

// Determine where the language file is and load it
if ($alt_path != '' && file_exists($alt_path . 'language/' . $idiom . '/' . $langfile)) {
include $alt_path . 'language/' . $idiom . '/' . $langfile;
} else {
$found = false;

foreach (get_instance()->load->get_package_paths(true) as $package_path) {
if (file_exists($package_path . 'language/' . $idiom . '/' . $langfile)) {
include $package_path . 'language/' . $idiom . '/' . $langfile;
$found = true;
break;
}
}

if ($found !== true) {
$database_lang = $this->_get_from_db();
if (!empty($database_lang)) {
$this->language    = array_merge($this->language, $database_lang);
$this->is_loaded[] = $langfile . '_lang' . EXT;
unset($database_lang);
}
// show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
}
}

if (!isset($lang)) {
log_message('error', 'Language file contains no data: language/' . $idiom . '/' . $langfile);
return;
}

if ($return == true) {
return $lang;
}

$this->is_loaded[] = $langfile;
$this->language    = array_merge($this->language, $lang);

$js_array = json_encode($this->language);
echo "<script>var lang_arr = " . $js_array . ";</script>\n";
unset($lang);

log_message('debug', 'Language file loaded: language/' . $idiom . '/' . $langfile);
return true;
}
 */

   /*
   public function load($langfile, $lang = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '') {
   // Calling early before CI reformats them
   $this->set = 'general';

   if (is_array($langfile)) {
   foreach ($langfile as $_lang)
   $this->load($_lang);
   return $this->language;
   }

   if ($add_suffix == TRUE) {
   $langfile = str_replace('_lang.', '', $langfile) . '_lang';
   }

   $langfile .= '_old.php';

   $config = & get_config();

   $deft_lang = (!isset($config['language'])) ? 'english' : $config['language'];
   $idiom = ($lang == '') ? $deft_lang : $lang;
   $this->idiom = $idiom;

   if (in_array($langfile . '_lang' . EXT, $this->is_loaded, TRUE))
   return $this->language;

   // Determine where the language file is and load it
   if ($alt_path != '' && file_exists($alt_path . 'language/' . $idiom . '/' . $langfile)) {
   include($alt_path . 'language/' . $idiom . '/' . $langfile);
   } else {
   $found = FALSE;
   foreach (get_instance()->load->get_package_paths(TRUE) as $package_path) {
   if (file_exists($package_path . 'language/' . $idiom . '/' . $langfile)) {
   include($package_path . 'language/' . $idiom . '/' . $langfile);
   $found = TRUE;
   break;
   }
   }


   if ($found !== TRUE) {
   $database_lang = $this->_get_from_db();
   if (!empty($database_lang)) {
   $this->language = array_merge($this->language, $database_lang);
   $this->is_loaded[] = $langfile . '_lang' . EXT;
   // $js_array = json_encode($this->language);
   // echo "<script>var lang_arr = " . $js_array . ";</script>\n";
   unset($database_lang);
   }
   // show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
   }
   }

   #log_message('debug', 'Language file loaded: language/' . $idiom . '/' . $langfile);
   return TRUE;
   }
    */

   // --------------------------------------------------------------------

   /**
    * Fetch a single line of text from the language array
    *
    * @access   public
    * @param   string   $line   the language line
    * @return   string
    */
   public function line($line = '')
   {
      $value = ($line == '' or !isset($this->language[$line])) ? false : $this->language[$line];

      // Because killer robots like unicorns!
      if ($value === false) {
         log_message('error', 'Could not find the language line "' . $line . '"');
      }

      return $value;
   }

   public function all_lines()
   {
      return $this->language;
   }

   /**
     * Load a language from database
     *
     * @access    private
     * @return    array
     */
    private function _get_from_db()
    {
        $CI = &get_instance();

        // $CI->db may be an empty string while the DB object is still being
        // initialised (Loader.php sets it to '' before calling DB()). Guard
        // here so a connection failure doesn't produce a misleading fatal error
        // on top of the real DB error.
        if ( ! isset($CI->db) || ! is_object($CI->db))
        {
            return array();
        }

        $CI->db->select('*');
        $CI->db->from('sys_translation');
        $CI->db->where('language', $this->idiom);
        $CI->db->where('set', $this->set);

        $query  = $CI->db->get()->result();
        $return = array();
        foreach ($query as $row) {
            $return[$row->key] = addslashes($row->text);
        }

        unset($CI, $query);
        return $return;
    }

}

// END Language Class

/* End of file Lang.php */
/* Location: ./system/core/Lang.php */
