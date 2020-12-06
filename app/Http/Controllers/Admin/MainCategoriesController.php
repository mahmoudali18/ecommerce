<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainCategoryRequest;
use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MainCategoriesController extends Controller
{

    public function index(){
        $default_lang = get_default_lang();
       $categories =  MainCategory::where('translation_lang',$default_lang)->selection()->get();
        return view('admin.maincategories.index',compact('categories'));
    }



    public function create(){
        return view('admin.maincategories.create');

    }



    public function store(MainCategoryRequest $request)
    {
        try {
            // return $request;

            //get default lang
            $main_categories = collect($request->category);
            $filter = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] == get_default_lang();
            });

            $default_category = array_values($filter->all())[0];

            $file_pass = "";
            if ($request->has('photo')) {
                $file_pass = uploadImage('maincategories', $request->photo);
            }

            DB::beginTransaction();

            $default_category_id = MainCategory::insertGetId([
                'translation_lang' => $default_category['abbr'],
                'translation_of' => 0,  //default  lang
                'name' => $default_category['name'],
                'slug' => $default_category['name'],
                'photo' => $file_pass,
            ]);


            //get other lang
            $categories = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] != get_default_lang();
            });

            if (isset($categories) && $categories->count()) {

                $categories_arr = [];
                foreach ($categories as $category) {
                    $categories_arr[] = [
                        'translation_lang' => $category['abbr'],
                        'translation_of' => $default_category_id,
                        'name' => $category['name'],
                        'slug' => $category['name'],
                        'photo' => $file_pass,
                    ];
                }
                MainCategory::insert($categories_arr);
            }


            DB::commit();

            return redirect()->route('admin.maincategories')->with(['success' => 'تم الحفظ بنجاح']);


        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);

        }

    }




    public function edit($mainCat_id){
        // get specific categories and its translation
       $mainCategory =  MainCategory::with('categories')
           ->selection()
           ->find($mainCat_id);
        if(!$mainCategory)
            return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);

        return view('admin.maincategories.edit',compact('mainCategory'));
    }




    public function update($mainCat_id ,MainCategoryRequest $request){
        try {
            //  return $request;
            $main_category = MainCategory::find($mainCat_id);
            if (!$main_category)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);

            $category = array_values($request->category)[0];

            if (!$request->has('category.0.active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);



            MainCategory::where('id', $mainCat_id)
                ->update([
                    'name' => $category['name'],
                    'active' => $request->active
                ]);

            //  save image    " if it exist in request "
            if ($request->has('photo')) {
                $file_pass = uploadImage('maincategories', $request->photo);

                MainCategory::where('id', $mainCat_id)
                    ->update([
                        'photo' => $file_pass,
                    ]);
            }


            return redirect()->route('admin.maincategories')->with(['success' => 'تم التحديث بنجاح']);

        }catch (\Exception $ex){
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }



    public function destroy($id){
        try {
            $mainCategory = MainCategory::find($id);
            if (!$mainCategory)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);

            //check if exist vendors in this mainCategory
            $vendors = $mainCategory ->vendors(); //vendors this is relation
            if(isset($vendors) && $vendors->count() > 0){
                return redirect()->route('admin.maincategories')->with(['error' => 'لا يمكن حذف هذا القسم']);
            }

            //delete image from folder assets/images/maincategories
            $image = Str::after($mainCategory->photo,'assets/');     // assets/   after assets in path
            $image = base_path('assets/'.$image);
            unlink($image);  //method for delete

            //delete translation of mainCategory before delete default mainCategory
            $mainCategory->categories()->delete();     //categories    this is relation

            $mainCategory ->delete();
            return redirect()->route('admin.maincategories')->with(['success' => 'تم حذف القسم بنجاح']);

        }catch (\Exception $ex){
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }




    public function changeStatus($id){

        try {

            $mainCategory = MainCategory::find($id);
            if (!$mainCategory)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);


           $status = $mainCategory->active == 0 ? 1 : 0 ;
           $mainCategory->update(['active' =>$status]);
            return redirect()->route('admin.maincategories')->with(['success' => 'تم تغيير الحاله بنجاح']);


        }catch (\Exception $exception){
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);

        }
    }



}
