<?php
/**
 * @project  phpstancs
 * @author   Adam Mátl <code@matla.cz>
 * @date     6.1.19
 * @encoding UTF-8
 */
declare(strict_types=1);

namespace matla\phpstancs;

use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use PHPStan\File\RelativePathHelper;
use PHPStan\Command\Output;

/**
 * Class CsLikeFormatter
 *
 * @package matla\phpstancs
 */
class CsLikeFormatter implements ErrorFormatter
{
    /** @var RelativePathHelper */
    private $relativePathHelper;

    public function __construct(RelativePathHelper $relativePathHelper)
    {
        $this->relativePathHelper = $relativePathHelper;
    }

    /**
     * Formats the errors and outputs them to the console.
     *
     * @param AnalysisResult $analysisResult
     * @param Output $style
     *
     * @return int Error code.
     */
    public function formatErrors(
        AnalysisResult $analysisResult,
        Output $output
    ): int {

        $output->writeLineFormatted('<?xml version="1.0" encoding="UTF-8"?>');
        $output->writeLineFormatted('<phpcs version="3.4.0">');


        foreach ($this->groupByFile($analysisResult) as $relativeFilePath => $errors) {
            $output->writeLineFormatted(
                sprintf(
                    '<file name="%s" errors="' . count($errors) . '" warnings="0" fixable="1">',
                    $this->escape($relativeFilePath)
                )
            );

            /**
             * @var \PHPStan\Analyser\Error[] $errors
             */
            foreach ($errors as $error) {
                $output->writeLineFormatted(
                    sprintf(
                        '    <error ' .
                        'line="%d" ' .
                        'column="1" ' .
                        'severity="5"  ' .
                        'fixable="1">phpstan: %s \</error>',
                        $error->getLine(),
                        $this->escape($error->getMessage())
                    )
                );
            }
            $output->writeLineFormatted('</file>');
        }

        $output->writeLineFormatted('</phpcs>');

        return $analysisResult->hasErrors() ? 1 : 0;
    }

    /**
     * Escapes values for using in XML
     *
     * @param string $string
     *
     * @return string
     */
    protected function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    /**
     * Group errors by file
     *
     * @param AnalysisResult $analysisResult
     *
     * @return array<string, array> Array that have as key the relative path of file
     *                              and as value an array with occured errors.
     */
    private function groupByFile(AnalysisResult $analysisResult): array
    {
        $files = [];

        /** @var \PHPStan\Analyser\Error $fileSpecificError */
        foreach ($analysisResult->getFileSpecificErrors() as $fileSpecificError) {
            $relativeFilePath = $this->relativePathHelper->getRelativePath(
                $fileSpecificError->getFile()
            );

            $files[$relativeFilePath][] = $fileSpecificError;
        }

        return $files;
    }
}

// CsLikeFormatter.php End
