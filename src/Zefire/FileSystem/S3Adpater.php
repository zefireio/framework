<?php

namespace Zefire\FileSystem;

use Zefire\Contracts\Fillable;
use Zefire\Contracts\Connectable;
use Zefire\Encryption\Encryption;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\ObjectUploader;
use Zefire\Core\Serializable;

class S3Adapter implements Fillable, Connectable
{
    use Serializable;
    /**
     * Stores bucket name.
     *
     * @var string
     */
    protected $bucket;
    /**
     * Stores encrypted key.
     *
     * @var string
     */
    protected $key;
    /**
     * Stores encrypted secret.
     *
     * @var string
     */
    protected $secret;
    /**
     * Stores bucket region.
     *
     * @var string
     */
    protected $region;
    /**
     * Stores an S3 client instance.
     *
     * @var \Aws\S3\S3Client
     */
    protected $client;
    /**
     * Stores an encryption instance.
     *
     * @var \Zefire\Encryption\Encryption
     */
    protected $encryption;
    /**
     * Create a new S3 adapter instance.
     *
     * @param. \Zefire\Encryption\Encryption $encryption
     * @return void
     */
    public function __construct(Encryption $encryption)
    {
        $this->encryption = $encryption;
    }
    /**
     * Connects to AWS S3.
     *
     * @return void
     */
    public function connect()
    {
        if ($this->client == null) {
            $this->client = S3Client::factory([
                'credentials' => [
                    'key'    => $this->encryption->decrypt($this->key),
                    'secret' => $this->encryption->decrypt($this->secret),
                ],
                'version' => 'latest',
                'region' => $this->region,
            ]);
        }        
    }    
    /**
     * Mounts a S3 bucket disk.
     *
     * @param  array $config
     * @return void
     */
    public function mount(array $config)
    {
        if (isset($config['key']) && $config['key'] != '') {
            $this->key = $this->encryption->encrypt($config['key']);    
        } else {
            throw new \Exception('Please define the "key" for your AWS S3 connection.');
        }
        if (isset($config['secret']) && $config['secret'] != '') {
            $this->secret = $this->encryption->encrypt($config['secret']);    
        } else {
            throw new \Exception('Please define the "secret" for your AWS S3 connection.');
        }
        if (isset($config['region']) && $config['region'] != '') {
            $this->region = $config['region'];    
        } else {
            throw new \Exception('Please define the "region" for your AWS S3 connection.');
        }
        if (isset($config['bucket']) && $config['bucket'] != '') {
            $this->bucket = $config['bucket'];    
        } else {
            throw new \Exception('Please define the "bucket" for your AWS S3 connection.');
        }
        $this->connect();
    }
    /**
     * returns bucket name acting as a path.
     *
     * @return string
     */
    public function path()
    {
        return $this->bucket;
    }
    /**
     * Lists files from directory.
     *
     * @param  string $directory
     * @return array
     */
    public function list($directory = '')
    {
        $objects = $this->client->getIterator('ListObjects', array('Bucket' => $this->bucket));
        $list = [];
        foreach ($objects as $object) {
            $list[] = $object['Key'];
        }
        return $list;
    }
    /**
     * Checks if a file exists.
     *
     * @param  string $file
     * @return bool
     */
    public function exists($file)
    {
        return ($this->retrieve() != null) ? true : false;
    }
    /**
     * Retrieves a file content.
     *
     * @param  string $file
     * @return string
     */
    public function get($file)
    {
        $object = $this->retrieve($file);
        return $object['Body']->__toString();
    }
    /**
     * Creates a file if it does not exists
     * and puts the content in the file.
     *
     * @param  string $file
     * @param  string $content
     * @return string
     */
    public function put($file, $content)
    {
        $uploader = new ObjectUploader($this->client, $this->bucket, $file, $content, 'public-read');
        return $uploader->upload();
    }
    /**
     * Deletes a file.
     *
     * @param  string $file
     * @return bool
     */
    public function delete($file)
    {
        return $this->client->deleteObject(array(
            'Bucket' => $this->bucket,
            'Key'    => $file
        ));
    }
    /**
     * Gets a file's size.
     *
     * @param  string $file
     * @return string
     */
    public function size($file)
    {
        $object = $this->retrieve($file);
        return $object['ContentLength'];
    }
    /**
     * Gets a file's lat modified datetime.
     *
     * @param  string $file
     * @return string
     */
    public function lastModified($file)
    {
        $object = $this->retrieve($file);
        return $object['LastModified']->__toString();
    }
    /**
     * Gets a file's type.
     *
     * @param  string $file
     * @return string
     */
    public function type($file)
    {
        $object = $this->retrieve($file);
        return $object['ContentType'];
    }
    /**
     * Retrieves an s3 object.
     *
     * @param  string $file
     * @return mixed
     */
    protected function retrieve($file)
    {
        return $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key'    => $file
        ]);
    }
}