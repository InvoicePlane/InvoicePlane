<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MerchantProviderRegistry
{
    private string $providersPath;

    public function __construct(?string $providersPath = null)
    {
        $this->providersPath = $providersPath ?: APPPATH . 'modules/einvoice/libraries/providers/';
    }

    public function discover(): array
    {
        $providers = [];

        foreach (glob($this->providersPath . '*Provider.php') ?: [] as $file) {
            require_once $file;
            $className = basename($file, '.php');

            if (!class_exists($className)) {
                continue;
            }

            $implements = class_implements($className);
            if (!$implements || !in_array(MerchantProviderInterface::class, $implements, true)) {
                continue;
            }

            $providers[$className::providerCode()] = [
                'code' => $className::providerCode(),
                'name' => $className::providerName(),
                'class' => $className,
            ];
        }

        return $providers;
    }

    public function getProvider(string $providerCode): MerchantProviderInterface
    {
        $providers = $this->discover();

        if (!isset($providers[$providerCode])) {
            throw new RuntimeException('Unknown merchant provider: ' . $providerCode);
        }

        $className = $providers[$providerCode]['class'];
        return new $className();
    }
}
