<?php
/**
 * @project  phpstancs
 * @author   Adam Mátl <code@matla.cz>
 * @date     8.1.19
 * @encoding UTF-8
 * @file    StanCs.php
 */
declare(strict_types=1);

namespace matla\phpstancs;

use Nette\Neon\Neon;
use RuntimeException;

class StanCs
{
    /**
     * location of analysed project (directory containing vendor directory).
     *
     * @var string
     */
    private $projectRootDir;

    /**
     * @var string
     */
    private $phpstancsRootDir;

    /**
     * @var string[]
     */
    private $argv;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $fileToAnalise;


    /**
     * StanCs constructor.
     *
     * @param array $argv
     * @param string $projectRootDir
     */
    public function __construct(array $argv, string $projectRootDir)
    {
        $this->projectRootDir = $projectRootDir;
        $this->phpstancsRootDir = __DIR__ . '/../';
        $this->argv = $argv;
        $this->fileToAnalise = $this->argv[1];
        $this->config = $this->getConfig();
    }

    public function run(): string
    {
        if (!isset($this->argv[1])) {
            die('At least one parameter is mandatory  --version| -i |<src-file>');
        }

        if ($this->argv[1] === '--version' || in_array('-i', $this->argv, true)) {
            return $this->getCsOutput();
        }

        if (!$this->config->runCs) {
            return $this->getStanOutput();
        }

        $stanErrors = $this->getStanOutput();
        $csErrors = $this->getCsOutput();

        return substr($stanErrors, 0, -strlen(" </file> </phpcs>")) . substr($csErrors, 63);
    }

    /**
     * @return string
     */
    protected function getStanOutput(): string
    {
        $cwdBak = getcwd();
        if ($cwdBak === false) {
            throw new RuntimeException("getcwd() failed");
        }
        // PHP stan load autoloader primary from `cwd`/vendor/autoloader.php
        exec('cd ' . escapeshellarg($this->projectRootDir));

        $configLocation = $this->phpstancsRootDir;

        if (file_exists("{$this->projectRootDir}phpstancs.phpstan.neon")) {
            $configLocation = $this->projectRootDir . 'phpstancs.';
        } elseif (file_exists("{$this->projectRootDir}phpstan.neon")) {
            $configLocation = $this->projectRootDir;
        }

        ob_start();
        passthru(
            "{$this->getBinDir()}phpstan" .
            " analyse {$this->fileToAnalise}" .
            " -c {$configLocation}phpstan.neon" .
            ' --error-format cslike --no-progress'
        );

        $output = ob_get_clean();

        if ($output === false) {
            throw new RuntimeException('ob_get_clean failed');
        }
        exec('cd ' . escapeshellarg($cwdBak));
        return $output;
    }

    /**
     * @return string
     */
    protected function getCsOutput(): string
    {
        $args = $this->argv;
        array_shift($args);

        ob_start();
        passthru("{$this->getBinDir()}phpcs " . implode(' ', $args));

        $output = ob_get_clean();
        if ($output === false) {
            throw new RuntimeException('ob_get_clean failed');
        }

        return $output;
    }

    /**
     * @param string $filename
     *
     * @return mixed
     */
    private function decodeNeonFile(string $filename)
    {
        $neonString = file_get_contents($filename);
        if ($neonString === false) {
            throw new RuntimeException("Failed open {$filename}");
        }

        return Neon::decode($neonString);
    }

    private function getConfig(): Config
    {
        $configFile = $this->projectRootDir . 'phpstan.neon';

        $config = new Config();

        if (file_exists($configFile)) {
            $NeonConfig = $this->decodeNeonFile($configFile);
            if (isset($NeonConfig['parameters']['phpstancs']['runCS'])) {
                $config->runCs = (bool)$NeonConfig['parameters']['phpstancs']['runCS'];
            }
        }

        return $config;
    }

    protected function getBinDir(): string
    {
        return file_exists("{$this->phpstancsRootDir}vendor/bin/") ?
            "{$this->phpstancsRootDir}vendor/bin/" : "{$this->phpstancsRootDir}/../../bin/";
    }
}

// StanCs.php End
