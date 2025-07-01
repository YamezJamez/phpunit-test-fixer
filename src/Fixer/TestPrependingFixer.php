<?php

declare(strict_types=1);

namespace Yamezjamez\PhpunitTestFixer\Fixer;

use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use SplFileInfo;

final class TestPrependingFixer extends AbstractPhpUnitFixer
{
    public function getName(): string
    {
        return 'Yamezjamez/prepend_test_method';
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_FUNCTION);
    }

    /**
     * As this is renaming methods, it is risky.
     */
    public function isRisky(): bool
    {
        return true;
    }

    public function getPriority(): int
    {
        return 0;
    }

    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        for ($index = 0; $index < $tokens->count(); $index++) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $methodNameIndex = $tokens->getNextMeaningfulToken($index);
            $methodNameToken = $tokens[$methodNameIndex];

            if (!$methodNameToken->isGivenKind(T_STRING)) {
                continue;
            }

            $methodName = $methodNameToken->getContent();

            $isPublic = false;
            for ($i = $index - 1; $i >= 0; $i--) {
                if ($tokens[$i]->isGivenKind(T_PUBLIC)) {
                    $isPublic = true;
                    break;
                }

                if ($tokens[$i]->isGivenKind(T_FUNCTION)) {
                    break;
                }
            }

            if (!$isPublic || str_starts_with($methodName, 'test')) {
                continue;
            }

            $tokens[$methodNameIndex] = new Token([T_STRING, 'test' . ucfirst($methodName)]);
        }
    }

    public function getDefinition(): FixerDefinition
    {
        return new FixerDefinition(
            'Prepends "test" to public method names in test files.',
            [],
            null,
            'May break test method name references'
        );
    }
}
