<?php

use Domain\Information\ArticleManager;
use Domain\Information\CategoryManager;
use Domain\Information\Filters\FilterManager;
use Domain\Information\TagManager;
use Domain\User\UserManager;
use Support\Flash\Flash;
use Support\Uploads\Contract\Upload;

if (!function_exists('filters')) {
    function filters(): array
    {
        return app(FilterManager::class)
            ->getItems();
    }
}

if (!function_exists('flash')) {
    function flash(): Flash
    {
        return app(Flash::class);
    }
}

if (!function_exists('upload')) {
    function upload(): Upload
    {
        return app(Upload::class);
    }
}

if (!function_exists('article')) {
    function article(): ArticleManager
    {
        return app(ArticleManager::class);
    }
}

if (!function_exists('category')) {
    function category(): CategoryManager
    {
        return app(CategoryManager::class);
    }
}

if (!function_exists('tag')) {
    function tag(): TagManager
    {
        return app(TagManager::class);
    }
}

if (!function_exists('user')) {
    function user(): UserManager
    {
        return app(UserManager::class);
    }
}
