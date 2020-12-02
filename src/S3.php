<?php
namespace CarloNicora\Minimalism\Services\S3;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractService;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\S3\Configurations\S3Configurations;
use CarloNicora\Minimalism\Services\S3\Events\S3ErrorManager;
use Exception;

class S3 extends AbstractService
{
    /** @var S3Configurations  */
    private S3Configurations $configData;

    /** @var string */
    protected string $bucket;

    /** @var int */
    protected int $uploadExpiration;

    /** @var string */
    protected string $amazonUrl;

    /**
     * AWS constructor.
     * @param S3Configurations $configData
     * @param ServicesFactory $services
     */
    public function __construct(S3Configurations $configData, ServicesFactory $services)
    {
        parent::__construct($configData, $services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->configData = $configData;

        $this->bucket = $configData->getBucket();
        $this->uploadExpiration = $configData->getUploadExpiration();

        $this->amazonUrl = 'https://s3.' . $configData->getRegion() . '.amazonaws.com/';
    }

    /**
     * @return string
     */
    public function getAmazonPublicUrl(): string
    {
        return $this->amazonUrl . $this->configData->getBucket() . '/';
    }

    /**
     * @return S3Client
     */
    private function client(): S3Client
    {
        return new S3Client([
            'credentials' => [
                'key' => $this->configData->getAwsKey(),
                'secret' => $this->configData->getAwsAccessSecret(),
            ],
            'region' => $this->configData->getRegion(),
            'signature_version' => $this->configData->getSignatureVersion(),
            'version' => 'latest',
            'acl' => 'public-read'
        ]);
    }

    /**
     * @param string $localFile
     * @param string $remoteFile
     * @return string|null
     * @throws Exception
     */
    public function upload(string $localFile, string $remoteFile): ?string
    {
        try {
            $result = $this->client()->putObject([
                'Bucket' => $this->bucket,
                'Key' => $remoteFile,
                'Expires' => $this->uploadExpiration,
                'SourceFile' => $localFile,
                'ACL' => 'public-read'
            ]);
            $awsUrl = $this->amazonUrl . $this->bucket . '/';
            return substr($result->get('ObjectURL'), strlen($awsUrl));
        } catch (S3Exception $e) {
            $this->services->logger()->error()->log(S3ErrorManager::S3_EXCEPTION($e))
                ->throw();
        }

        return null;
    }
}