<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PluggitApi\Translation;

final class TranslationTest extends TestCase
{

    private static string $testfile = __DIR__.DIRECTORY_SEPARATOR.
                               '..'.DIRECTORY_SEPARATOR.
                               'src'.DIRECTORY_SEPARATOR.
                               'languages'.DIRECTORY_SEPARATOR.
                               'test.php';

    public static function setUpBeforeClass(): void
    {
        if (file_exists(self::$testfile)) {
            unlink(self::$testfile);
        }
        $content = "<?php ".PHP_EOL.
                   "return [".PHP_EOL.
                   "   'testcase' => 'Testcase result',".PHP_EOL.
                   "];".PHP_EOL;
        file_put_contents(self::$testfile, $content);
    }

    /**
     * @runInSeparateProcess
     * @throws Exception
     */
    public function testSingleton():void
    {
        $this->assertEquals(Translation::singleton('test'), Translation::singleton('TEST'));
    }

    /**
     * @runInSeparateProcess
     * @throws Exception
     */
    public function testTranslateText():void
    {
        $this->assertEquals('Testcase result', Translation::singleton('test')->translate('testcase'));
    }

    /**
     * @runInSeparateProcess
     * @throws Exception
     */
    public function testMissingTranslation():void
    {
        $this->assertEquals(__METHOD__, Translation::singleton('test')->translate(__METHOD__));
        $this->assertStringContainsString(__METHOD__, file_get_contents(self::$testfile));
    }

    /**
     * @runInSeparateProcess
     * @throws Exception
     */
    public function testInitialization():void
    {
        try {
            Translation::singleton();
            $this->fail('Exception is missing');
        }
        catch (Exception $e) {
            $this->assertStringContainsString('language parameter at first time', $e->getMessage());
        }

        $object = Translation::singleton('test');
        $this->assertInstanceOf(Translation::class, $object);
    }

}
