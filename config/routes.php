<?php
/**
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Action\HomePageAction::class, 'home');
 * $app->post('/album', App\Action\AlbumCreateAction::class, 'album.create');
 * $app->put('/album/:id', App\Action\AlbumUpdateAction::class, 'album.put');
 * $app->patch('/album/:id', App\Action\AlbumUpdateAction::class, 'album.patch');
 * $app->delete('/album/:id', App\Action\AlbumDeleteAction::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Action\ContactAction::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Action\ContactAction::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Action\ContactAction::class,
 *     Zend\Expressive\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

/** @var \Zend\Expressive\Application $app */

//$app->get('/', App\Action\HomePageAction::class, 'home');
$app->get('/api/ping', App\Action\PingAction::class, 'api.ping');


/**
 * Templates Routes
 */
$app->get('/get-templates', App\Action\TemplatesAction::class, 'get-templates');


/**
 * Pages Routes
 */
$app->get('/', \App\Action\PagesAction::class, 'get-root-page');
$app->get('/{seo:[a-zA-Z0-9-_]+}-{pageId: \d+}', \App\Action\PagesAction::class, 'get-page');
$app->get('/{lang: espanol}/', \App\Action\PagesAction::class, 'get-root-spanish-page');
$app->get('/{lang: english}/', \App\Action\PagesAction::class, 'get-root-english-page');
$app->get('{lang: portugues}/', \App\Action\PagesAction::class, 'get-root-portugues-page');
$app->get('/{lang: espanol}/{seo:[a-zA-Z0-9-_]+}-{pageId: \d+}', \App\Action\PagesAction::class, 'get-spanish-page');
$app->get('/{lang: english}/{seo:[a-zA-Z0-9-_]+}-{pageId: \d+}', \App\Action\PagesAction::class, 'get-english-page');
$app->get('/{lang: portugues}/{seo:[a-zA-Z0-9-_]+}-{pageId: \d+}', \App\Action\PagesAction::class, 'get-portugues-page');

