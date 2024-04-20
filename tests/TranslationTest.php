<?php
declare(strict_types=1);

namespace PluggitApi\Tests;

use Exception;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
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
     * @throws Exception
     */
    #[RunInSeparateProcess]
    public function testSingleton():void
    {
        self::assertEquals(Translation::singleton('test'), Translation::singleton('TEST'));
    }

    /**
     * @throws Exception
     */
    #[RunInSeparateProcess]
    public function testTranslateText():void
    {
        self::assertEquals('Testcase result', Translation::singleton('test')->translate('testcase'));
    }

    /**
     * @throws Exception
     */
    #[RunInSeparateProcess]
    public function testMissingTranslation():void
    {
        self::assertEquals(__METHOD__, Translation::singleton('test')->translate(__METHOD__));
        self::assertStringContainsString(__METHOD__, file_get_contents(self::$testfile));
    }

    /**
     * @throws Exception
     */
    #[RunInSeparateProcess]
    public function testInitialization():void
    {
        try {
            Translation::singleton();
            $this->fail('Exception is missing');
        } catch (Exception $e) {
            self::assertStringContainsString('language parameter at first time', $e->getMessage());
        }

        $object = Translation::singleton('test');
        self::assertInstanceOf(Translation::class, $object);
    }
}
