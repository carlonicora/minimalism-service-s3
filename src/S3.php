<?php
namespace CarloNicora\Minimalism\Services\S3;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use Exception;
use RuntimeException;
use Throwable;

class S3 implements ServiceInterface
{
    /** @var string */
    protected string $bucket;

    /** @var int */
    protected int $uploadExpiration;

    /** @var string */
    protected string $amazonUrl;

    /** @var array */
    private const EXTENSIONS = [
        'bmp'   => 'image/bmp',
        'gif'   => 'image/gif',
        'ico'   => 'image/x-icon',
        'jpeg'  => 'image/jpeg',
        'jpg'   => 'image/jpeg',
        'pdf'   => 'application/pdf',
        'png'   => 'image/png',
        'svg'   => 'image/svg+xml',
        'webp'  => 'image/webp',
    ];

    /**
     * AWS constructor.
     * @param string $MINIMALISM_SERVICE_AWS_ACCESS_KEY
     * @param string $MINIMALISM_SERVICE_AWS_ACCESS_SECRET
     * @param string $MINIMALISM_SERVICE_AWS_REGION
     * @param string $MINIMALISM_SERVICE_AWS_BUCKET
     * @param string $MINIMALISM_SERVICE_AWS_SIGNATURE_VERSION
     * @param string $MINIMALISM_SERVICE_AWS_UPLOAD_EXPIRATION
     */
    public function __construct(
        private string $MINIMALISM_SERVICE_AWS_ACCESS_KEY,
        private string $MINIMALISM_SERVICE_AWS_ACCESS_SECRET,
        private string $MINIMALISM_SERVICE_AWS_REGION,
        private string $MINIMALISM_SERVICE_AWS_BUCKET,
        private string $MINIMALISM_SERVICE_AWS_SIGNATURE_VERSION,
        private string $MINIMALISM_SERVICE_AWS_UPLOAD_EXPIRATION,
    )
    {
        $this->amazonUrl = 'https://s3.' . $this->MINIMALISM_SERVICE_AWS_REGION . '.amazonaws.com/';
    }

    /**
     * @return string
     */
    public function getAmazonPublicUrl(): string
    {
        return $this->amazonUrl . $this->MINIMALISM_SERVICE_AWS_BUCKET . '/';
    }

    /**
     * @return S3Client
     */
    private function client(): S3Client
    {
        return new S3Client([
            'credentials' => [
                'key' => $this->MINIMALISM_SERVICE_AWS_ACCESS_KEY,
                'secret' => $this->MINIMALISM_SERVICE_AWS_ACCESS_SECRET,
            ],
            'region' => $this->MINIMALISM_SERVICE_AWS_REGION,
            'signature_version' => $this->MINIMALISM_SERVICE_AWS_SIGNATURE_VERSION,
            'version' => 'latest',
            'acl' => 'public-read'
        ]);
    }

    /**
     * @param string $localFile
     * @param string $remoteFile
     * @param string $extension
     * @return string|null
     * @throws Exception
     */
    public function upload(string $localFile, string $remoteFile, string $extension): ?string
    {
        try {
            $result = $this->client()->putObject([
                'Bucket' => $this->MINIMALISM_SERVICE_AWS_BUCKET,
                'Key' => $remoteFile,
                'Expires' => $this->MINIMALISM_SERVICE_AWS_UPLOAD_EXPIRATION,
                'SourceFile' => $localFile,
                'ACL' => 'public-read',
                'ContentType' => self::EXTENSIONS[$extension]
            ]);
            $awsUrl = $this->amazonUrl . $this->MINIMALISM_SERVICE_AWS_BUCKET . '/';
            return substr($result->get('ObjectURL'), strlen($awsUrl));
        } catch (S3Exception|Throwable $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }
    }

    /**
     *
     */
    public function initialise(): void {}

    /**
     *
     */
    public function destroy(): void {}
}