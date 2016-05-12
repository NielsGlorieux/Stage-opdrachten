<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//themes
//hier wordt het ingestelde theme opgehaald en worden views waar theme: voorstaat in de controllers vervangen door
//views van het theme, als het theme deze views niet bevat worden de gewone views getoond.
use App\Settings;

$theme = Settings::where('name', 'theme')->first();
// $theme='';
View::addNamespace('theme', [
    base_path().'/public/themes/'. $theme->value .'/views',
    base_path().'/resources/views'
]);
if($theme->value != ''){
    View::addLocation(base_path().'/public/themes/'. $theme->value .'/views');
}else{
    View::addLocation(base_path().'/resources/views');
}
View::addLocation(app_path().'/admin/views');

//landpagina
Route::get('/', function () {
    return view('welcome');
});

Route::auth();

//home pagina
Route::get('/home', 'HomeController@index');

//POLLS
//Create poll (worden appart beschermd voor authenticatie omdat andere functies binnen PollController
// wel mogen bekeken worden door bezoekers)
Route::group(['middleware' => 'App\Http\Middleware\Authenticate'], function(){
    Route::get('/poll/create', 'PollController@create');
    Route::post('/poll/create', 'PollController@createPoll');
});
//show all polls
Route::get('/polls', 'PollController@showPolls');
//vote on poll
Route::group(['middleware' => 'App\Http\Middleware\MaxVotesMiddleware'], function(){
    Route::post('/poll/vote', 'PollController@postVote');
});
//show single poll
Route::get('/p/{id}','PollController@showPoll');
Route::post('/p/comment','PollController@postComment');

//routes die enkel admins kunnen gebruiken
Route::group(['middleware' => 'App\Http\Middleware\AdminMiddleware'], function(){
    //poll settings (kunnen worden gebruikt op de detail pagina van de poll)
    Route::post('/p/{id}','AdminController@disableComments');
    Route::post('/maxVotes','AdminController@setMaxVotes');
    Route::post('/maxLevel','AdminController@setMaxLevel');

    //BACKEND
    //backend homepage
    Route::get('/admin', 'AdminController@adminPage');
    
    //users dashboard
    Route::get('/admin/users','AdminController@users');
    Route::post('/admin/users/create','AdminController@addUser');
    Route::post('/admin/users/delete','AdminController@deleteUser');
    Route::post('/admin/users/edit','AdminController@editUser');
    //block user (wordt ook gebruikt op profielpagina)
    Route::post('/u/','AdminController@blockUser');
    //Search user 
    Route::post('/admin/users/','AdminController@searchUser');
   
    //page dashboard
    Route::get('admin/pages','AdminController@showPageDashboard');
    Route::post('admin/pages','AdminController@changeNavMenu');
    //create custom pages
    Route::post('/createPage', 'PageController@create');
    Route::get('/admin/pages/create','AdminController@createPage');
    //edit pages
    Route::get('admin/edit/{slug}', 'PageController@showEdit');
    Route::post('/edit', 'PageController@edit');
    Route::post('/changetype','PageController@changeType');
    //delete page
    Route::post('admin/pages/delete','PageController@deletePage');
  
    //admin forum
    //Categories
    Route::get('admin/forum/categories','ForumController@showForumCategories');
    Route::get('admin/forum/','ForumController@showForumCategories');
    Route::get('admin/forum/categories/create','ForumController@showCreateCategory');
    Route::post('/admin/create/category', 'ForumController@createCategory');
    Route::get('admin/forum/categories/{id}','ForumController@showCategory');
    Route::post('admin/forum/categories/delete','ForumController@deleteCat');
    //Topics
    Route::get('admin/forum/categories/{name}/create','ForumController@showCreateTopic');
    Route::post('admin/forum/categories/', 'ForumController@createTopic');
    Route::get('admin/forum/categories/{name}/{topic}','ForumController@showTopic');
    Route::post('admin/forum/categories/deleteTopic','ForumController@deleteTopic');
    //Replies
    Route::post('admin/forum/categories/post/', 'ForumController@createReply' );
  
    //User forum admin actions
    Route::get('/forum/categories/create','UserForumController@showCreateCategory');
    Route::post('/forum/create/category', 'UserForumController@createCategory');
    Route::post('/forum/categories', 'UserForumController@changeCatWatch');
    
    //themes dasboard
    Route::get('/admin/themes','AdminController@themes');
    Route::post('admin/themes','AdminController@chooseTheme');
    
   
    //Poll dashboard
    Route::get('/admin/polls','AdminController@adminPolls');
    Route::post('/admin/polls','AdminController@changePollLookAfterComplete');
    Route::post('/admin/polls/disable','AdminController@disablePercentage');
    Route::post('/admin/polls/createCategory', 'AdminController@postCategory');
    Route::post('/admin/polls/deleteCat','AdminController@deleteCategory');
    Route::post('/admin/polls/editCat','AdminController@editCategory');
    Route::post('/admin/polls/searchPoll','AdminController@searchPoll');
    //delete poll
    Route::post('/admin/polls/deletePoll', 'AdminController@deletePoll');
    //edit poll
    Route::post('/admin/polls/editPoll', 'AdminController@editPoll');
    Route::post('/admin/polls/deleteOption', 'PollController@deleteOption');
    //bulk
    Route::post('/admin/polls/bulkdelete', 'AdminController@bulkDeletePoll');
    
    //user forum admin actions
    Route::post('/forum/categories/deleteTopic', 'UserForumController@deleteTopic');
    
    //Chat
    Route::get('/admin/chat', 'ChatController@index');
    Route::post('/admin/sendChat', 'ChatController@sendMessage');

});            
//profiel pagina
Route::get('/u/{id}','HomeController@showProfile');
Route::post('/u/search','HomeController@searchUser');

// //user block
// Route::get('/redirect','HomeController@showBlockedPage');

//toon custom pages
Route::get('/page/{slug}', array('as' => 'page.show', 'uses' => 'PageController@show'));

//USER FORUM
Route::get('/forum', 'UserForumController@showForumCategories');
//Categories
Route::get('/forum/categories','UserForumController@showForumCategories');
Route::get('/forum/','UserForumController@showForumCategories');
Route::get('/forum/categories/{id}','UserForumController@showCategory');
Route::post('forum/categories/delete','UserForumController@deleteCat');
//Topics
Route::get('/forum/categories/{id}/create','UserForumController@showCreateTopic');
Route::post('/forum/categories/topic', 'UserForumController@createTopic');
Route::get('/forum/categories/{name}/{topic}','UserForumController@showTopic');
//Replies
Route::post('/forum/categories/post/', 'UserForumController@createReply' );


//inbox
Route::get('/inbox', 'PrivateMessageController@inbox');
Route::get('/outbox', 'PrivateMessageController@outbox');
Route::get('/sendbox', 'PrivateMessageController@sendbox');
Route::get('/message/{id}', 'PrivateMessageController@message');
Route::get('/sendMessage', 'PrivateMessageController@sendMessagepage');
Route::post('/sendMessage', 'PrivateMessageController@sendMessage');

//installer DEZE MOETEN BESCHERMD WORDEN
Route::get('/setup', 'InstallerController@setup');
Route::post('/setup', 'InstallerController@databaseSetup');
Route::get('/setup/migrate', 'InstallerController@migrate');
Route::get('/setup/admin', 'InstallerController@showCreateAdmin');
Route::post('/setup/admin', 'InstallerController@createAdmin');

//om sql requests te checken
// \Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
//     var_dump($query->sql);
//     var_dump($query->bindings);
//     var_dump($query->time);
// });
