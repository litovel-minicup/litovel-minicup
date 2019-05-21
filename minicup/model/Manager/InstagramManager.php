<?php


namespace Minicup\Model\Manager;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class InstagramManager
{
    /** @var Cache */
    private $cache;
    /** @var string */
    private $username;
    /** @var string */
    private $key;
    /** @var string */
    private $expiration = '5 minutes';

    /**
     * InstagramManager constructor.
     * @param string   $username
     * @param IStorage $storage
     */
    public function __construct(string $username, IStorage $storage)
    {
        $this->cache = new Cache($storage);
        $this->username = $username;
        $this->key = "ig-stories-$username";
    }


    /**
     * @return array
     * @throws \Throwable
     */
    public function load(): array
    {
        $data = $this->cache->load($this->key);
        if (!$data) {
            $data = $this->fetch();
            $this->cache->save($this->key, $data, [Cache::EXPIRE => $this->expiration]);
        }
        return $data;
    }

    /**
     * @return array
     * @throws JsonException
     */
    private function fetch(): array
    {
        $client = new Client();
        try {
            $response = $client->request('GET', "https://api.storiesig.com/stories/{$this->username}");
        } catch (GuzzleException $e) {
            return [];
        }

        $body = Json::decode($response->getBody(), Json::FORCE_ARRAY);
        return array_map(function ($item) {
            return [
                'url' => isset($item['video_versions']) ? $item['video_versions'][0]['url'] : $item['image_versions2']['candidates'][0]['url'],
                'type' => isset($item['video_versions']) ? 'video' : 'image',
                'taken_at' => $item['taken_at'],
            ];
        }, $body['items']);
    }
}