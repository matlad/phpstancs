<?php
/**
 * @project  phpstancs
 * @author   Adam Mátl <matla@matla.cz>
 * @date     8.1.19
 * @encoding UTF-8
 * @brief    StanCs.php
 */
declare(strict_types=1);

namespace matla\phpstancs;

use RuntimeException;
use stdClass;

class StanCs
{
    private $vendorDir;

    /**
     *
     * @var string
     */
    private $rootDir;

    /**
     * @var string[]
     */
    private $argv;

    private $config;

    public function __construct(array $argv, string $vendorDir)
    {
        $this->vendorDir = $vendorDir;
        $this->rootDir = __DIR__ . '/../';
        $this->argv = $argv;
        $this->config = $this->getConfig();
    }

    public function run(): string
    {
        if (!isset($this->argv[1])) {
            die('At least one parameter is mandatory  --version |<src-file>');
        }

        if ($this->argv[1] === '--version') {

            return $this->getVersion();
        }

        if (!$this->config->runCs) {
            return $this->getStanOutput();
        }

        $stanErrors = $this->getStanOutput();
        $csErrors = $this->getCsOutput();

        return substr($stanErrors, 0, -9) . substr($csErrors, 63);
    }

    /**
     * @return string
     */
    protected function getStanOutput(): string
    {
        ob_start();
        passthru(
            "{$this->vendorDir}bin/phpstan" .
            " analyse {$this->argv[1]}" .
            " -c {$this->rootDir}phpstan.neon" .
            " -l {$this->config->stanLvl}" .
            " --error-format cslike --no-progress"
        );

        return ob_get_clean();
    }

    /**
     * @return string
     */
    protected function getCsOutput(): string
    {
        ob_start();
        passthru("{$this->vendorDir}bin/phpcs " . implode(' ', $this->argv));

        return ob_get_clean();
    }

    /**
     * @return string
     */
    protected function getVersion(): string
    {
        ob_start();
        passthru("{$this->vendorDir}/bin/phpcs --version");

        return ob_get_clean();
    }

    /**
     * @param string $filename
     *
     * @return stdClass
     */
    private function decodeJsonFile(string $filename): stdClass
    {
        $json = file_get_contents($filename);
        if ($json === false) {
            throw new RuntimeException("Failed open {$filename}");
        }
        $result = json_decode($json);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Failed decode {$filename} : " . json_last_error_msg());
        }

        return $result;
    }

    private function getConfig(): stdClass
    {
        $userConfigFile = $this->vendorDir . '../stanCs.json';
        $configFile = $this->rootDir . 'config.json';

        $config = $this->decodeJsonFile($configFile);

        if (file_exists($userConfigFile)) {
            $userConfig = $this->decodeJsonFile($userConfigFile);
            $config = (object)array_merge((array)$config, (array)$userConfig);
        }

        return $config;
    }
}

// StanCs.php End
