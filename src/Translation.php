<?php
/**
 * Created by PhpStorm.
 * User: Oliver
 * Date: 03.10.2019
 * Time: 21:47
 */

namespace PluggitApi;

use Exception;

class Translation
{

    /**
     * @var Translation|null
     */
    private static ?Translation $instance = null;

    /**
     * translation key => value
     *
     * @var array
     */
    private array $dictionary;

    /**
     * Path to language file
     *
     * @var string
     */
    private string $languageFile;

    /**
     * Translation constructor.
     * @throws Exception
     */
    private function __construct(string $lang = 'en')
    {
        $this->languageFile = __DIR__.DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.$lang.'.php';
        if (!file_exists($this->languageFile)) {
            throw new Exception('No languages file found for: ' . $lang);
        }
        $this->dictionary = require_once($this->languageFile);
    }

    /**
     * @return Translation
     * @throws Exception
     */
    public static function singleton(string $lang = ''): Translation
    {
        // return existing instance
        if (null !== self::$instance) {
            return self::$instance;
        }

        // generate new instance and return
        if (empty($lang)) {
            throw new Exception('Call '.__METHOD__.' with language parameter at first time');
        }

        self::$instance = new self(strtolower($lang));
        return self::$instance;
    }

    /**
     * @param $key
     * @return string
     */
    public function translate($key): string
    {
        if (isset($this->dictionary[$key])) {
            return $this->dictionary[$key];
        } else {
            file_put_contents(
                $this->languageFile,
                "// missing translation:    '".$key."' => '".$key."',".PHP_EOL,
                FILE_APPEND
            );
            return $key;
        }
    }
}
