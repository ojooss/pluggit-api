<?php
/**
 * Created by PhpStorm.
 * User: Oliver
 * Date: 03.10.2019
 * Time: 21:47
 */

namespace PluggitApi;

class Translation
{

    /**
     * @var Translation
     */
    private static $instance;

    /**
     * translation key => value
     *
     * @var array
     */
    private $dictionary;

    /**
     * Path to language file
     *
     * @var string
     */
    private $langguageFile;

    /**
     * Translation constructor.
     * @param string $lang
     * @throws \Exception
     */
    private function __construct($lang='en')
    {
        $this->langguageFile = __DIR__.DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.$lang.'.php';
        if (!file_exists($this->langguageFile)) {
            throw new \Exception('No languages file found for: ' . $lang);
        }
        $this->dictionary = require_once($this->langguageFile);
    }

    /**
     * @param $lang
     * @return Translation
     * @throws \Exception
     */
    public static function singleton($lang='')
    {
        // return existing instance
        if (null !== self::$instance) {
            return self::$instance;
        }

        // generat enew instance and return
        if (empty($lang)) {
            throw new \Exception('Call '.__METHOD__.' with language parameter at first time');
        }

        self::$instance = new self(strtolower($lang));
        return self::$instance;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function translate($key) {
        if (isset($this->dictionary[$key])) {
            return $this->dictionary[$key];
        }
        else {
            file_put_contents($this->langguageFile, "// missing translation:    '".$key."' => '".$key."',".PHP_EOL,FILE_APPEND);
            return $key;
        }
    }

}