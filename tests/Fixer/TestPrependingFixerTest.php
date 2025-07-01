<?php

declare(strict_types=1);

namespace Yamezjamez\Tests\Fixer;

use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Yamezjamez\PhpunitTestFixer\Fixer\TestPrependingFixer;

final class TestPrependingFixerTest extends TestCase
{
    private TestPrependingFixer $fixer;

    protected function setUp(): void
    {
        $this->fixer = new TestPrependingFixer();
    }

    #[Test]
    public function fixesTests(): void
    {
        $input = '<?php namespace Yamezjamez\Tests\Somewhere; final class FooTest extends \\PHPUnit_Framework_TestCase { public function doSomething(): void {}}';
        $expected = '<?php namespace Yamezjamez\Tests\Somewhere; final class FooTest extends \\PHPUnit_Framework_TestCase { public function testDoSomething(): void {}}';
        
        $tokens = Tokens::fromCode($input);
        $this->fixer->fix(new \SplFileInfo(__FILE__), $tokens);

        $this->assertSame($expected, $tokens->generateCode());
    }

    #[Test]
    public function ignoresExistingTestMethods(): void
    {
        $input = '<?php namespace Yamezjamez\Tests\Somewhere; final class FooTest extends \\PHPUnit_Framework_TestCase { public function testAlreadyTested(): void {}}';
        
        $tokens = Tokens::fromCode($input);
        $this->fixer->fix(new \SplFileInfo(__FILE__), $tokens);

        $this->assertSame($input, $tokens->generateCode());
    }

    #[Test]
    public function ignoresNonTestClasses(): void
    {
        $input = '<?php namespace Yamezjamez\Fixers\Somewhere; final class NotATest { public function doSomething(): void {}}';
        
        $tokens = Tokens::fromCode($input);
        $this->fixer->fix(new \SplFileInfo(__FILE__), $tokens);

        $this->assertSame($input, $tokens->generateCode());
    }

    #[Test]
    #[DataProvider('nonTestMethodsProvider')]
    public function ignoreNonTestMethods(string $testMethod): void
    {
        $input = '<?php namespace Yamezjamez\Tests\Somewhere; final class FooTest extends \\PHPUnit_Framework_TestCase {'.$testMethod.'}';
        
        $tokens = Tokens::fromCode($input);
        $this->fixer->fix(new \SplFileInfo(__FILE__), $tokens);

        $this->assertSame($input, $tokens->generateCode());
    }

    public static function nonTestMethodsProvider(): \Generator
    {
        yield 'Protected function' => ['protected function notATestMethod(): void {}'];
        yield 'Private function' => ['private function anotherMethod(): void {}'];
    }
}
