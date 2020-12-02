<?php
namespace CarloNicora\Minimalism\Services\S3\Configurations;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceConfigurations;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\S3\Events\S3ErrorManager;
use Exception;

class S3Configurations extends AbstractServiceConfigurations
{
    /** @var array */
    protected const REQUIRED_CONFIGURATIONS = [
        'awsKey' => 'MINIMALISM_SERVICE_AWS_ACCESS_KEY',
        'awsAccessSecret' => 'MINIMALISM_SERVICE_AWS_ACCESS_SECRET',
        'region' => 'MINIMALISM_SERVICE_AWS_REGION',
        'bucket' => 'MINIMALISM_SERVICE_AWS_BUCKET',
        'signatureVersion' => 'MINIMALISM_SERVICE_AWS_SIGNATURE_VERSION',
        'uploadExpiration' => 'MINIMALISM_SERVICE_AWS_UPLOAD_EXPIRATION'
    ];

    /** @var string */
    protected string $awsKey;
    /** @var string */
    protected string $awsAccessSecret;
    /** @var string */
    protected string $region;
    /** @var string */
    protected string $bucket;
    /** @var string */
    protected string $signatureVersion;
    /** @var int */
    protected int $uploadExpiration;

    /**
     * ApiConfigurations constructor.
     * @param ServicesFactory $services
     * @throws Exception
     */
    public function __construct(ServicesFactory $services)
    {
        foreach (self::REQUIRED_CONFIGURATIONS as $variableName => $configName) {
            if (empty($configValue = getenv($configName))) {
                $services->logger()->error()->log(S3ErrorManager::CONFIGURATION_ERROR($configName))->throw();
            }
            $this->$variableName = $configValue;
        }
    }


    /**
     * @return string
     */
    public function getAwsKey(): string
    {
        return $this->awsKey;
    }

    /**
     * @return string
     */
    public function getAwsAccessSecret(): string
    {
        return $this->awsAccessSecret;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getBucket(): string
    {
        return $this->bucket;
    }

    /**
     * @return string
     */
    public function getSignatureVersion(): string
    {
        return $this->signatureVersion;
    }

    /**
     * @return int
     */
    public function getUploadExpiration(): int
    {
        return $this->uploadExpiration;
    }

}