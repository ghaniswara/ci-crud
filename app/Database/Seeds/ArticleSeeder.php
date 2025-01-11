<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use App\Storage\MinioStorage;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create();
        $minioStorage = new MinioStorage();
        for ($i = 0; $i < 50; $i++) {
            $uuid = Uuid::uuid4()->toString();
            $data = [
                'title'          => $faker->sentence(rand(4, 8)),
                'content'        => $faker->paragraphs(rand(3, 7), true),
                'author'         => $faker->name(),
                'published_date' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
                'uuid'           => $uuid,
                'object_path'    => 'articles/' . $uuid . '.json',
            ];

            $json = json_encode($data, JSON_PRETTY_PRINT);
            $minioStorage->putJSON($json, $uuid, $minioStorage->ArticleBucket);

            $this->db->table('articles')->insert($data);
        }
    }
} 