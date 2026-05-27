<?php
declare(strict_types=1);

try {
    $page = getRecord('pages','id',23);
    if (!$page) {
        http_response_code(404);
        echo json_encode(['error' => 'Page not found']);
        exit;
    }
    $bannerlist = getList('banners', 'page_id', $page->id);
    $seolink = getRecord('seo_links', 'entity_id', $page->id);
    if (!$seolink) {
        http_response_code(404);
        echo json_encode(['error' => 'Page seolinks not found']);
        exit;
    }   

    $seo     = $seolink ? getRecord('seo', 'id', $seolink->target_id) : null;
    $page->seo = $seo ?? null;
    if (!$bannerlist) {
        http_response_code(404);
        echo json_encode(['error' => 'Page banners not found']);
        exit;
    }
    $banners = [];
    while ($b = $bannerlist->fetch_assoc()) {
        $banners[] = $b;
    }   
    $page->banners = $banners ?? null;
    
    $featured = getRow('events',['slug','imagepath','title','cat_id','summary'],'active = 1 && featured =1') ?? [];
    if($featured) {
        $catid = $featured['cat_id'];
        $featured['cat_slug'] = getRow('categories',['slug'],"id = '$catid'")['slug'];
    }

    $page->featured = $featured;

    echo json_encode($page);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}