<?php
declare(strict_types=1);

namespace MageStack\FirePush\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Serialize\SerializerInterface;

class ConfigUploader extends Value
{
    private const REQUIRED_KEYS = [
        'type',
        'project_id',
        'private_key_id',
        'private_key',
        'client_email',
        'client_id',
        'auth_uri',
        'token_uri',
        'auth_provider_x509_cert_url',
        'client_x509_cert_url',
        'universe_domain'
    ];

    public function __construct(
        private readonly FileDriver $fileDriver,
        private readonly EncryptorInterface $encryptor,
        private readonly SerializerInterface $serializer,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function beforeSave()
    {
        $fileData = $this->getValue();

        if ($this->shouldDeleteFile($fileData)) {
            $this->setValue(null);
            return parent::beforeSave();
        }

        if (!$this->isValidUpload($fileData)) {
            return parent::beforeSave();
        }

        $content = $this->readFileContent($fileData['tmp_name']);
        $this->validateJsonStructure($content);

        $this->setValue($this->encryptor->encrypt($content));

        return parent::beforeSave();
    }

    private function shouldDeleteFile(mixed $fileData): bool
    {
        return is_array($fileData) && array_key_exists('delete', $fileData);
    }

    private function isValidUpload(mixed $fileData): bool
    {
        return is_array($fileData)
            && !empty($fileData['tmp_name'])
            && is_uploaded_file($fileData['tmp_name']);
    }

    private function readFileContent(string $tmpFile): string
    {
        try {
            return $this->fileDriver->fileGetContents($tmpFile);
        } catch (\Exception $e) {
            throw new LocalizedException(__('Could not read uploaded file: %1', $e->getMessage()));
        }
    }

    private function validateJsonStructure(string $content): void
    {
        try {
            $decoded = $this->serializer->unserialize($content);
        } catch (\InvalidArgumentException $e) {
            throw new LocalizedException(__('Uploaded file is not a valid JSON.'));
        }

        $missing = array_diff(self::REQUIRED_KEYS, array_keys($decoded));
        if (!empty($missing)) {
            throw new LocalizedException(__('Missing keys in JSON: %1', implode(', ', $missing)));
        }
    }
}