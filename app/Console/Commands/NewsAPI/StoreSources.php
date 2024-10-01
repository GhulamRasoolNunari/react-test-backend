<?php

namespace App\Console\Commands\NewsAPI;

use App\Models\Source;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class StoreSources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news-api:store-sources';

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
        $api_key = config('app.news_api.api_key');
        if (is_null($api_key)){
            return $this->error('No API key provided! Please update the .env file and configure the cache accordingly');
        }
        $response = Http::retry(3, 100) // Retry 3 times with 100ms pause
        ->timeout(30)
        ->get('https://newsapi.org/v2/sources?apiKey='.$api_key);

        if (count($response->json()['sources'])){
            $data = [];
            foreach ($response->json()['sources'] as $source) {
                $tempData = [
                    'source_id' => $source['id'],
                    'name' => $source['name'],
                    'description' => $source['description'],
                    'url' => $source['url'],
                    'category' => $source['category'],
                    'language' => $source['language'],
                    'country' => $source['country'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $data[] = $tempData;
            }

            if (count($data)) {
                Source::truncate();
                if (count($data) > 1000) {
                    $data_chunks = array_chunk($data, 1000);
                    foreach ($data_chunks as $chunk) {
                        Source::insert($chunk);
                    }
                } else {
                    Source::insert($data);
                }

                $this->info('News Sources Stored Successfully');
            }
        }
    }
}
