<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $data = Article::select('news.*', 'c.name as category_title')
        ->join('categories as c', 'c.id', '=', 'news.category_id')
        ->join(DB::raw('(SELECT MIN(id) as id FROM news GROUP BY title) as unique_news'), 'news.id', '=', 'unique_news.id')
        ->when(!empty($request->author), function($q) use($request) {
            $q->where('news.author', $request->author);
        })
        ->when(!empty($request->source), function($q) use($request) {
            $q->where('news.source', $request->source);
        })
        ->when(!empty($request->search), function($que) use($request) {
            $que->whereAny(
                [
                    'news.title',
                    'news.description',
                    'news.content',
                    'c.name'
                ],
                'LIKE',
                "%$request->search%"
            );
        })
        ->when(!empty($request->date), function($q) use($request) {
            $q->whereDate('news.published_at', $request->date);
        })
        ->when(!empty($request->category), function($q) use($request) {
            $q->where('news.category_id', $request->category);
        })
        ->take(20)
        ->get();
        
        return response()->json($data, 200);
    }

    /**
     * Get all distinct categories, sources and authors from news table
     *
     * @return JsonResponse
     */
    public function searchDependencies(): JsonResponse
    {
        $categories = DB::table('categories')->select('id', 'name')->get();
        $sources = DB::table('news')->select('source')->distinct()->get();
        $authors = DB::table('news')->select('author')->distinct()->get();
        return response()->json([
            'categories' => $categories,
            'sources' => $sources,
            'authors' => $authors,
        ], 200);
    }

}
