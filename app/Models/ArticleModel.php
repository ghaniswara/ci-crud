<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Cache\RedisCache;
use App\Storage\MinioStorage;


class ArticleModel extends Model
{
    protected $table = 'articles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'content', 'author', 'published_date', 'slug', 'object_path', 'uuid'];
    
    private $redisCache;
    private $minioStorage;

    // redis keyss
    protected $frontpage_articles = 'frontpage_articles';
    protected $articlePrefix = 'article:';

    public function __construct()
    {
        parent::__construct();
        $this->redisCache = RedisCache::getInstance();
        $this->minioStorage = MinioStorage::getInstance();
    }

    public function searchArticles($search, $currentPage, $itemsPerPage)
    {
        if ($search == '' && $currentPage == 1 && $itemsPerPage == 10) {
            $cache = $this->redisCache->getClient()->get($this->frontpage_articles);
            if ($cache) {
                return unserialize($cache);
            }

            $results = $this->orderBy('published_date', 'DESC')->paginate($itemsPerPage, 'default', $currentPage);
            
            $this->redisCache->getClient()->set(
                $this->frontpage_articles, 
                serialize($results), 
                'EX', $this->redisCache->DurationHour);
            
            return $results;
        }

        return $this->like('title', $search)
            ->orLike('author', $search)
            ->orderBy('published_date', 'DESC')
            ->paginate($itemsPerPage, 'default', $currentPage);
    }

    public function countArticles($search)
    {
        return $this->like('title', $search)
            ->orLike('author', $search)
            ->countAllResults();
    }

    public function getArticle($id)
    {
        $cache = $this->redisCache->getClient()->get($this->articlePrefix . $id);

        if ($cache) {
            $article = unserialize($cache);
            $article['id'] = $id;
            return $article;
        }

        $article = $this->find($id);
        
        if ($article) {
            $article['id'] = $id;
            $this->redisCache->getClient()->set(
                $this->articlePrefix . $id, 
                serialize($article), 
                'EX', 
                $this->redisCache->DurationMonth
            );
        }

        return $article;
    }

    public function updateArticle($id, $data)
    {
        $this->update($id, $data);

        $article = $this->find($id);
        
        $this->redisCache->getClient()->set(
            $this->articlePrefix . $id, 
            serialize($article), 
            'EX', 
            $this->redisCache->DurationMonth
        );

        $this->redisCache->getClient()->del($this->frontpage_articles);

        $json = json_encode($article, JSON_PRETTY_PRINT);
        $this->minioStorage->putJSON($json, $data['uuid'], $this->minioStorage->ArticleBucket);

        return $this->getArticle($id);
    }

    public function createArticle($data)
    {
        $id = $this->insert($data, true);
        $data['id'] = $id;

        $this->redisCache->getClient()->set($this->articlePrefix . $id, serialize($data), 'EX', $this->redisCache->DurationMonth);
        $this->redisCache->getClient()->del($this->frontpage_articles);

        $json = json_encode($data, JSON_PRETTY_PRINT);
        $this->minioStorage->putJSON($json, $data['uuid'], $this->minioStorage->ArticleBucket);
    }

    public function deleteArticle($data)
    {
        $this->delete($data['id']);
        try {
            $this->redisCache->getClient()->del($this->articlePrefix . $data['id']);
            $this->redisCache->getClient()->del($this->frontpage_articles);
        } catch (\Exception $e) {
            error_log('Failed to delete cache: ' . $e->getMessage());
        }

        try {
            $this->minioStorage->deleteObject($data['object_path'], $this->minioStorage->ArticleBucket);
        } catch (\Exception $e) {
            error_log('Failed to delete object from MinIO: ' . $e->getMessage());
        }
    }
}

