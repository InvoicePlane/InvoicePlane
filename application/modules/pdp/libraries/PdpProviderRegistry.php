<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'modules/pdp/libraries/providers/PdpProviderInterface.php';

class PdpProviderRegistry
{
    private $providersPath;

    public function __construct(string $providersPath = null)
    {
        $this->providersPath = $providersPath ?: APPPATH . 'modules/pdp/libraries/providers';
    }

    public function providers(): array
    {
        $providers = array();

        foreach ($this->providerFiles() as $file) {
            $before = get_declared_classes();
            require_once $file;
            $after = get_declared_classes();
            $classes = array_values(array_diff($after, $before));

            foreach ($classes as $class) {
                if (!$this->isProviderClass($class)) {
                    continue;
                }

                $code = $this->providerCode($class);
                $providers[$code] = array(
                    'code' => $code,
                    'name' => $this->providerName($class, $code),
                    'class' => $class,
                    'file' => $file,
                );
            }
        }

        ksort($providers);
        return $providers;
    }

    public function make(string $code): PdpProviderInterface
    {
        $providers = $this->providers();
        $code = strtolower(trim($code));

        if (!isset($providers[$code])) {
            if (isset($providers['superpdp'])) {
                $code = 'superpdp';
            } elseif (!empty($providers)) {
                $code = array_key_first($providers);
            } else {
                throw new RuntimeException('No PDP provider found in ' . $this->providersPath);
            }
        }

        $class = $providers[$code]['class'];
        return new $class();
    }

    private function providerFiles(): array
    {
        $files = glob(rtrim($this->providersPath, '/') . '/*Provider.php') ?: array();
        $files = array_filter($files, function ($file) {
            return basename($file) !== 'PdpProviderInterface.php' && basename($file) !== 'AbstractApiPdpProvider.php';
        });
        sort($files);
        return $files;
    }

    private function isProviderClass(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $ref = new ReflectionClass($class);
        return !$ref->isAbstract() && $ref->implementsInterface('PdpProviderInterface');
    }

    private function providerCode(string $class): string
    {
        if (method_exists($class, 'providerCode')) {
            return strtolower((string) $class::providerCode());
        }

        $short = (new ReflectionClass($class))->getShortName();
        $short = preg_replace('/Provider$/', '', $short);
        return strtolower($short);
    }

    private function providerName(string $class, string $code): string
    {
        if (method_exists($class, 'providerName')) {
            return (string) $class::providerName();
        }

        return $code === 'superpdp' ? 'SuperPDP' : ucfirst($code);
    }
}
