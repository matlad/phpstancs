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

use Nette\Neon\Neon;
use RuntimeException;

class StanCs
{
    /**
     * Tam kde je složka vendor.
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

    public function __construct(array $argv, string $projectRootDir)
    {
        $this->projectRootDir = $projectRootDir;
        $this->phpstancsRootDir = __DIR__ . '/../';
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
            "{$this->projectRootDir}vendor/bin/phpstan" .
            " analyse {$this->argv[1]}" .
            " -c {$this->projectRootDir}phpstan.neon" .
            ' --error-format cslike --no-progress'
        );

        $output = ob_get_clean();
        if($output === false)
        {
            throw new RuntimeException('ob_get_clean failed');
        }

        return $output;
    }

    /**
     * @return string
     */
    protected function getCsOutput(): string
    {
        ob_start();
        passthru("{$this->projectRootDir}vendor/bin/phpcs " . implode(' ', $this->argv));

        $output = ob_get_clean();
        if($output === false)
        {
            throw new RuntimeException('ob_get_clean failed');
        }

        return $output;
    }

    /**
     * @return string
     */
    protected function getVersion(): string
    {
        ob_start();
        passthru("{$this->projectRootDir}vendor/bin/phpcs --version");

        $output = ob_get_clean();
        if($output === false)
        {
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
            if (isset($NeonConfig['parameters']['runCS'])) {
                $config->runCs = (bool)$NeonConfig['parameters']['runCS'];
            }
        }

        return $config;
    }
}

// StanCs.php End
