<?php

namespace App\Services\Articles;

use App\Models\Articles\WebsiteArticle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ArticleService
{
    public function getArticles(bool $paginate = false, int $perPage = 8): array|Collection|LengthAwarePaginator
    {
        $query = WebsiteArticle::with([
            'user:id,username',
            'user.avatar:player_id,figure_code',
        ])->orderByDesc('id');

        return $paginate ? $query->paginate($perPage) : $query->get();
    }

    public function fetchArticle(string $slug): WebsiteArticle
    {
        return WebsiteArticle::where('slug', '=', $slug)->firstOrFail();
    }
}
