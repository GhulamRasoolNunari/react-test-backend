<?php

namespace App\Console\Commands\NewsAPI;

use App\Models\Article;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class StoreArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news-api:store-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDateTime = date('Y-m-d H:i:s');

        $getCategories = Category::where('source', 'news-api')->get();


        foreach ($getCategories as $key => $category) {
            $result = $this->getArticles($currentDateTime, $category);

            if ($result['status'] === "ok") {
                $articleData = [];
                foreach ($result['articles'] as $key => $article) {
                    if (empty($article['author']) ||  $article['title'] == '[Removed]' || empty($article['source']['name']))
                    {
                        continue;
                    }
                    $articleData[] = [
                        'category_id' => $category->id,
                        'source' => $article['source']['name'],
                        'author' => $article['author'],
                        'title' => $article['title'],
                        'description' => $article['description'],
                        'source_url' => $article['url'],
                        'image_url' => $article['urlToImage'],
                        'content' => $article['content'],
                        'published_at' => Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s'),
                        'created_at' => $currentDateTime,
                        'updated_at' => $currentDateTime,
                    ];
                }
                if(count($articleData)) {
                    if (count($articleData) > 1000) {
                        $chunk_data = array_chunk($articleData, 1000);
                        foreach ($chunk_data as $chunk) {
                            Article::insert($chunk);
                        }
                    } else {
                        Article::insert($articleData);
                    }
                    $this->info('Data fetched and inserted successfully!');
                }
            }
        }
    }

    private function getArticles($currentDateTime, $category, $page = 1){
        $config_app = config('app');

        $from = Carbon::now()->subDays(2)->format('Y-m-d H:i:s');

        $baseUrl = 'https://newsapi.org/v2/top-headlines';
        $queryParams = [
            'q' => 'a',
            'category' => $category->name,
            'from' => $from,
            'to' => $currentDateTime,
            'pageSize' => 100,
            'page' => $page,
            'apiKey' => $config_app['news_api']['api_key']
        ];

        $response = Http::retry(3, 100) // Retry 3 times with 100ms pause
            ->timeout(30)
            ->get($baseUrl, $queryParams); // Pass query parameters array directly
        
        return $response->json();
    }
}
