<?php
namespace CarloNicora\Minimalism\Services\S3\Factories;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceFactory;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\S3\Configurations\S3Configurations;
use CarloNicora\Minimalism\Services\S3\S3;
use Exception;

class ServiceFactory extends AbstractServiceFactory
{
    /**
     * ServiceFactory constructor.
     * @param ServicesFactory $services
     * @throws Exception
     */
    public function __construct(ServicesFactory $services)
    {
        $this->configData = new S3Configurations($services);

        parent::__construct($services);
    }

    /**
     * @param ServicesFactory $services
     * @return S3
     */
    public function create(ServicesFactory $services) : S3
    {
        return new S3($this->configData, $services);
    }
}