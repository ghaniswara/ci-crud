<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use App\Storage\MinioStorage;
use CodeIgniter\Controller;
use Cocur\Slugify\Slugify;
use Ramsey\Uuid\Uuid;
use App\Entity\BaseResponse;
use App\Cache\RedisCache;

class ArticleController extends Controller
{
    private $articleJSONPrefix = "article_json:";

    private $minioStorage;
    private $slugify;
    private $redisCache;
    public function __construct()
    {
        $this->minioStorage = MinioStorage::getInstance();
        $this->slugify = new Slugify();
        $this->redisCache = RedisCache::getInstance();
    }

    // View Endpoints
    public function edit($id)
    {
        $model = new ArticleModel();
       
        $article = $model->getArticle($id);

        return view('articles/edit', ['article' => $article]);
    }

    public function create()
    {
        return view('articles/create');
    }

    public function index()
    {
        $model = new ArticleModel();

        $search = $this->request->getVar('search') ?? '';
        $currentPage = $this->request->getVar('page') ?? 1;
        $itemsPerPage = 10;

        $articles = $model->searchArticles($search, $currentPage, $itemsPerPage);
        $totalArticles = $model->countArticles($search);
        $currentPage = $currentPage;
        $itemsPerPage = $itemsPerPage;

        return view('articles/index', [
            'articles' => $articles,
            'totalArticles' => $totalArticles,
            'itemsPerPage' => $itemsPerPage,
            'search' => $search,
            'currentPage' => $currentPage
        ]);

    }

    public function show($id)
    {     
        $model = new ArticleModel();
        $article = $model->getArticle($id);

        if (!$article) {
            return redirect()->to('/articles')->with('error', 'Article not found.');
        }

        return view('articles/show', ['article' => $article]);
    }


    // Business Logic Endpoints
    public function store()
    {

        $validation = \Config\Services::validation();

        $validation->setRules([
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required',
            'author' => 'required|min_length[3]',
            'published_date' => 'required|valid_date',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new ArticleModel();

        $uuid = Uuid::uuid4()->toString();
        $title = $this->request->getPost('title');
        $slug = $this->slugify->slugify($title);
        $objectPath = 'articles/' . $uuid . '.json';

        $data = [
            'title' => $title,
            'content' => $this->request->getPost('content'),
            'author' => $this->request->getPost('author'),
            'published_date' => $this->request->getPost('published_date'),
            'slug' => $slug,
            'object_path' => $objectPath,
            'uuid' => $uuid,
        ];

        $model->createArticle($data);

        return redirect()->to('/articles');
    }

    public function update($id)
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required',
            'author' => 'required|min_length[3]',
            'published_date' => 'required|valid_date',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new ArticleModel();
        $article = $model->getArticle($id);

        if (!$article) {
            return redirect()->to('/articles')->with('error', 'Article not found.');
        }

        $title = $this->request->getPost('title');
        $slug = $this->slugify->slugify($title);
        $objectPath = 'articles/' . $article['uuid'] . '.json';

        $data = [
            'title' => $title,
            'content' => $this->request->getPost('content'),
            'author' => $this->request->getPost('author'),
            'published_date' => $this->request->getPost('published_date'),
            'slug' => $slug,
            'object_path' => $objectPath,
            'uuid' => $article['uuid'],
        ];

        $updatedArticle = $model->updateArticle($id, $data);

        return redirect()->to('/articles');
    }

    public function delete($id)
    {
        $model = new ArticleModel();
        $article = $model->find($id);

        $objectPath = $article['object_path'];

        try {
            $model->delete($id);
        } catch (\Exception $e) {
            return redirect()->to('/articles')->with('error', 'Failed to delete article: ' . $e->getMessage());
        }

        try {
            $this->minioStorage->deleteObject($objectPath, $this->minioStorage->ArticleBucket);
        } catch (\Exception $e) {
            error_log('Failed to delete object from MinIO: ' . $e->getMessage());
        }

        try {
            if ($this->redisCache->getClient()->exists($article['uuid'])) {
                $this->redisCache->getClient()->del($article['uuid']);
            }
        } catch (\Exception $e) {
            error_log('Failed to delete cache: ' . $e->getMessage());
        }

        return redirect()->to('/articles');
    }

   public function api($id = null)
   {
    if ($id) {
        return $this->api_id($id);
    }

    $params_page = $this->request->getVar('page') ?? 1;
    $params_items_per_page = $this->request->getVar('items_per_page') ?? 10;
    $search = $this->request->getVar('search') ?? '';

        $model = new ArticleModel();
        $articles = $model->searchArticles($search, $params_page, $params_items_per_page);

        $response = new BaseResponse();
        $response->createResponse(200, 'success', $articles);

        return $this->response->setJSON($response);
   }

   public function api_id($id)
   {
        $model = new ArticleModel();
        $articles = $model->getArticle($id);

        $object_path = explode('/', $articles['object_path']);
        $object_path = end($object_path);

        $jsonCache = $this->redisCache->getClient()->get($this->articleJSONPrefix . $id);

        if ($jsonCache) {
            $articles = unserialize($jsonCache);
        } else {
            $url_to_json = $this->minioStorage->createPresignedUrl($object_path, $this->minioStorage->ArticleBucket);
            
            $this->redisCache->getClient()->set(
                $this->articleJSONPrefix . $id,
                serialize($articles),
                'EX',
                $this->redisCache->DurationMonth
            );
        }

        $response = new BaseResponse();
        $response->createResponse(200, 'success', $url_to_json);

        return $this->response->setJSON($response);
   }
}
