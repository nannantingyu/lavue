<?php

Route::get('/', "IndexController@index");

//Index
Route::get('/blog_{id}', "IndexController@detail");
Route::get('/watch_{id}', "IndexController@detail");
Route::get('/incre_{id}', "AjaxController@incre");
Route::get('/hotkey', "IndexController@hotkey");
Route::get('/weibo', "IndexController@weibo");
Route::get('/keywords', "IndexController@keywords");
Route::get('/hotsearch', "IndexController@hotsearch");

//Ajax
Route::get('/baidu_tuisong', "AjaxController@baidu_tuisong");
Route::get('/body_src_repl', "AjaxController@body_src_repl");
Route::get('/image', "ApiController@img");
Route::get('/kx', "ApiController@getkx");
Route::get('/hotweibo', "ApiController@hotweibo");

//Login
Route::get('/login', "LoginController@index");
Route::get('/register', "LoginController@register");
Route::get('/logout', "LoginController@logout");
Route::get('/check/{check_str}', "LoginController@check");
Route::post('/login', "LoginController@login_post");
Route::post('/register', "LoginController@register_post");
Route::any('/captcha', function()
{
return captcha();
});

//List
Route::get('/list_{type}', "ListController@lists");
Route::get('/search_{keywords}_{page}', "ListController@search");
Route::get('/type_{keywords}_{page}', "ListController@type");
Route::get('/baidu_{id}', "ListController@baidusearch");
Route::get('/weibo_{id}', "ListController@weibosearch");
Route::get('/keys_{key}_{page}', "ListController@keys");
Route::get('/author_{author}', "ListController@author");
Route::get('/keys', "ListController@keywords");
Route::get('/news', "ListController@news");
Route::get('/hots', "ListController@hots");

//Caijing
$router->get('/getDates', 'EconomicController@getDates');
$router->get('/getPastorWillFd', 'EconomicController@getPastorWillFd');
$router->get('/getWeekData', 'EconomicController@getWeekData');
$router->get('/getjiedu', 'EconomicController@getjiedu');
$router->get('/getjiedudata', 'EconomicController@getjiedudata');
$router->get('/getcjdatas', 'EconomicController@getcjdatas');
$router->get('/getcjevent', 'EconomicController@getcjevent');
$router->get('/getcjholiday', 'EconomicController@getcjholiday');
$router->get('/fedata', 'EconomicController@fedata');
$router->get('/rili', 'EconomicController@rili');

//kuaixun
$router->get('/kuaixun', 'KuaixunController@kuaixun');
$router->get('/kuaixun_{id}', 'KuaixunController@kuaixun_detail');

//hq
$router->get('/hq_btc', 'HqController@index');

//other
$router->get('/sitemap.xml', 'ToolController@sitemap');
$router->get('/xmlsitemap/article{page}.xml', 'ToolController@site');
$router->post('/trans', 'ToolController@trans_downfile');

Route::get('/test', "Test2Controller@index");

//candy
$router->get('/residential', 'HouseController@residential');
$router->get('/house', 'HouseController@history');
$router->get('/areahouse', 'HouseController@getAreaResidential');
$router->get('/residentialinfo', 'HouseController@getResidentialInfo');
$router->post('/crawlinfo', 'HouseController@crawlinfo');
$router->post('/crawl/{name}', 'HouseController@crawl');

//admin
// 用户
$router->get('/cms', "Admin\IndexController@index");
Route::post('/adlogin', 'Admin\UserController@login');
Route::post('/adlogout', 'Admin\UserController@adlogout');
Route::post('/regist', 'Admin\RegisterController@regist');
Route::post('/addRole', 'Admin\UserController@addRole');
Route::post('/assignRoleForUser', 'Admin\UserController@assignRoleForUser');
Route::post('/retractRoleFromUser', 'Admin\UserController@retractRoleFromUser');
Route::get('/getRoles', 'Admin\UserController@getRoles');
Route::get('/getUsers', 'Admin\UserController@getUsers');
Route::get('/getRoleUsers', 'Admin\UserController@getRoleUsers');
Route::post('/setPassword', 'Admin\UserController@setPassword');
Route::post('/addOrUpdateUser', 'Admin\UserController@addOrUpdateUser');
Route::post('/setState', 'Admin\UserController@setState');

// 上传
Route::post('/uploads_image', 'Admin\UploadsController@image');

// 模块
Route::get('/getModule', 'Admin\ModuleController@getModule');
Route::post('/addOrUpdateModule', 'Admin\ModuleController@addOrUpdateModule');
Route::get('/getModuleTreeSelect', 'Admin\ModuleController@getModuleTreeSelect');
Route::get('/getModulePermission', 'Admin\ModuleController@getModulePermission');
Route::post('/addModulePermission', 'Admin\ModuleController@addModulePermission');
Route::get('/getRoleMoudlePermission', 'Admin\ModuleController@getRoleMoudlePermission');
Route::get('/getUserModulePermission', 'Admin\ModuleController@getUserModulePermission');

// 系统配置
Route::get('/configLists', 'Admin\ConfigController@lists');
Route::get('/configInfo', 'Admin\ConfigController@info');
Route::post('/setConfigState', 'Admin\ConfigController@setConfigState');
Route::post('/addConfig', 'Admin\ConfigController@addConfig');

// 文章分类
Route::get('/categoryLists', 'Admin\CategoryController@lists');
Route::get('/categoryInfo', 'Admin\CategoryController@info');
Route::post('/setCategoryState', 'Admin\CategoryController@setCategoryState');
Route::post('/addCategory', 'Admin\CategoryController@addCategory');
Route::get('/categoryTree', 'Admin\CategoryController@tree');

// 文章
Route::get('/articleLists', 'Admin\ArticleController@lists');
Route::get('/articleInfo', 'Admin\ArticleController@info');
Route::post('/setArticleState', 'Admin\ArticleController@setArticleState');
Route::post('/addArticle', 'Admin\ArticleController@addArticle');
Route::post('/deleteArticle', 'Admin\ArticleController@deleteArticle');
Route::post('/searchArticle', 'Admin\ArticleController@searchArticle');
Route::get('/articleListsByCategory', 'Admin\ArticleController@articleListsByCategory');
Route::get('/articleSource', 'Admin\ArticleController@source');
Route::get('/articleSourceSite', 'Admin\ArticleController@source_site');
Route::post('/multiOffline', 'Admin\ArticleController@multiOffline');
Route::post('/multiOnline', 'Admin\ArticleController@multiOnline');
Route::post('/multiDelete', 'Admin\ArticleController@multiDelete');
Route::post('/addOrUpdateSourceSite', 'Admin\ArticleController@addOrUpdateSourceSite');
Route::post('/removeSourceSite', 'Admin\ArticleController@removeSourceSite');
Route::get('/toArticlePage', 'Admin\ArticleController@toArticlePage');
Route::post('/redownloadImage', 'Admin\ArticleController@redownloadImage');

// 区块链
Route::get('/blockCoinLists', 'Admin\BlockCoinController@lists');
Route::get('/blockCoinInfo', 'Admin\BlockCoinController@info');
Route::post('/setBlockCoinState', 'Admin\BlockCoinController@setBlockCoinState');
Route::post('/addBlockCoin', 'Admin\BlockCoinController@addBlockCoin');

// 热门图片
Route::get('/hotBannerLists', 'Admin\HotBannerController@lists');
Route::get('/hotBannerInfo', 'Admin\HotBannerController@info');
Route::post('/setHotBannerState', 'Admin\HotBannerController@setHotBannerState');
Route::post('/addHotBanner', 'Admin\HotBannerController@addHotBanner');

// 词条
Route::get('/entryLists', 'Admin\EntryController@lists');
Route::get('/entryInfo', 'Admin\EntryController@info');
Route::post('/setEntryState', 'Admin\EntryController@setEntryState');
Route::post('/addEntry', 'Admin\EntryController@addEntry');
Route::post('/addEntryArticle', 'Admin\EntryController@addEntryArticle');
Route::post('/removeEntryArticle', 'Admin\EntryController@removeEntryArticle');

// 爬虫单独文章
Route::get('/crawlArticleLists', 'Admin\CrawlArticleController@lists');
Route::get('/crawlArticleInfo', 'Admin\CrawlArticleController@info');
Route::post('/addCrawlArticle', 'Admin\CrawlArticleController@addCrawlArticle');

// 爬虫分类转化
Route::get('/categoryMapLists', 'Admin\CategoryMapController@lists');
Route::get('/categoryMapInfo', 'Admin\CategoryMapController@info');
Route::post('/setCategoryMapState', 'Admin\CategoryMapController@setCategoryMapState');
Route::post('/addCategoryMap', 'Admin\CategoryMapController@addCategoryMap');
