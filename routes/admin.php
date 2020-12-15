<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

define('PAGINATION_COUNT',10);

Route::group(['namespace'=>'Admin','middleware' => 'auth:admin'],function (){
    Route::get('/','DashboardController@index')->name('admin.dashboard');

    ############################ Begin languages Routes ###############################
    Route::group(['prefix'=>'languages'],function (){
        Route::get('/','LanguagesController@index')->name('admin.languages');
        Route::get('create','LanguagesController@create')->name('admin.languages.create');
        Route::post('store','LanguagesController@store')->name('admin.languages.store');

        Route::get('edit/{id}','LanguagesController@edit')->name('admin.languages.edit');
        Route::post('update/{id}','LanguagesController@update')->name('admin.languages.update');

        Route::get('delete/{id}','LanguagesController@destroy')->name('admin.languages.delete');


    });
    ############################ End languages Routes ###############################


    ############################ Begin main categories Routes ###############################
    Route::group(['prefix'=>'main_categories'],function (){
        Route::get('/','MainCategoriesController@index')->name('admin.maincategories');
        Route::get('create','MainCategoriesController@create')->name('admin.maincategories.create');
        Route::post('store','MainCategoriesController@store')->name('admin.maincategories.store');     //[17  ,19]    [22,19]

        Route::get('edit/{id}','MainCategoriesController@edit')->name('admin.maincategories.edit');     //[20 ,23]
        Route::post('update/{id}','MainCategoriesController@update')->name('admin.maincategories.update');  //[20 ,23]   [21,24]

        Route::get('delete/{id}','MainCategoriesController@destroy')->name('admin.maincategories.delete');  //[36 41]  [41  46]



        Route::get('changeStatus/{id}','MainCategoriesController@changeStatus')->name('admin.maincategories.status');  //[38 43]

    });
    ############################ End main categories Routes ###############################


    ############################ Begin vendors(التاجر) Routes ###############################
    Route::group(['prefix'=>'vendors'],function (){
        Route::get('/','VendorsController@index')->name('admin.vendors');
        Route::get('create','VendorsController@create')->name('admin.vendors.create');      // [26  31]
        Route::post('store','VendorsController@store')->name('admin.vendors.store');       //[30 35 ]

        Route::get('edit/{id}','VendorsController@edit')->name('admin.vendors.edit');     //[34 39]
        Route::post('update/{id}','VendorsController@update')->name('admin.vendors.update');  //[35 40]

        Route::get('delete/{id}','VendorsController@destroy')->name('admin.vendors.delete');    //[42 47]

        Route::get('changeStatus/{id}','VendorsController@changeStatus')->name('admin.vendors.status');  //[48 43]



    });
    ############################ End vendors Routes ###############################


    ############################ Begin sub categories Routes ###############################
    Route::group(['prefix'=>'sub_categories'],function (){
        Route::get('/','SubCategoriesController@index')->name('admin.subcategories');
        Route::get('create','SubCategoriesController@create')->name('admin.subcategories.create');
        Route::post('store','SubCategoriesController@store')->name('admin.subcategories.store');     //

        Route::get('edit/{id}','SubCategoriesController@edit')->name('admin.subcategories.edit');     //
        Route::post('update/{id}','SubCategoriesController@update')->name('admin.subcategories.update');  //

        Route::get('delete/{id}','SubCategoriesController@destroy')->name('admin.subcategories.delete');  //



        Route::get('changeStatus/{id}','SubCategoriesController@changeStatus')->name('admin.subcategories.status');  //

    });
    ############################ End sub categories Routes ###############################


});


Route::group(['namespace'=>'Admin','middleware' => 'guest:admin'],function (){
    Route::get('login','LoginController@getLogin')->name('get.admin.login');
    Route::post('login','LoginController@login')->name('admin.login');

});




#############################  Begin  test part  Routes   #####################################     [54   59]
Route::get('subcategory',function(){
    $maincategpry = \App\Models\MainCategory::find(13);
    return $maincategpry->subCategories;
});


Route::get('maincategory',function(){
    $subcategpry = \App\Models\SubCategory::find(1);
    return $subcategpry->mainCategory;
});
#############################  end  test part  Routes   #####################################


