<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\MainCategory;
use App\Models\Vendor;
use App\Notifications\VendorCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;


class VendorsController extends Controller
{

    public function index(){
       $vendors =  Vendor::selection()->paginate(PAGINATION_COUNT);
       return view('admin.vendors.index',compact('vendors'));
    }


    public function create(){
        $categories =  MainCategory::where('translation_of',0)->active()->get();
        return view('admin.vendors.create',compact('categories'));
    }


    public function store(VendorRequest $request){

        try {
           //  return $request;


            if (!$request->has('active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);


            $file_pass = "";
            if ($request->has('logo')) {
                $file_pass = uploadImage('vendors', $request->logo);
            }

            //insert in DB
           $vendor =  Vendor::create([
                'name'=>$request->name,
                'mobile'=>$request->mobile,
                'email'=>$request->email,
                'active' => $request->active,
                'address'=>$request->address,
                'logo'=>$file_pass,
                'category_id'=>$request->category_id,
               'password'=>$request ->password,
               'latitude'=>$request ->latitude,
               'longitude'=>$request ->longitude,

            ]);

            // after create send to mail
            Notification::send($vendor,new VendorCreated($vendor));

            return redirect()->route('admin.vendors')->with(['success' => 'تم الحفظ بنجاح']);

        }catch (\Exception $ex){
           // return $ex;
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }



    public function edit($id){
        try{
            $vendor = Vendor::selection()->find($id);
            if(!$vendor){
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوف']);
            }
            $categories =  MainCategory::where('translation_of',0)->active()->get();

            return view('admin.vendors.edit',compact('vendor','categories'));
        }catch (\Exception $exception){
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }



    public function update($id, VendorRequest $request)
    {

        try {

            $vendor = Vendor::Selection()->find($id);
            if (!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوف ']);


            DB::beginTransaction();
            //  save image    " if it exist in request "
            if ($request->has('logo') ) {
                $filePath = uploadImage('vendors', $request->logo);
                Vendor::where('id', $id)
                    ->update([
                        'logo' => $filePath,
                    ]);
            }


            if (!$request->has('active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);


            $data = $request->except('_token', 'id', 'logo', 'password');   //الاميل و البسورد  علشان انا بحدثهم بشرط منفصل


            //  save password    " if it exist in request "
            if ($request->has('password') && !is_null($request->  password)) {
                $data['password'] = $request->password;
            }


            Vendor::where('id', $id)
                ->update(
                    $data
                );

            DB::commit();
            return redirect()->route('admin.vendors')->with(['success' => 'تم التحديث بنجاح']);
        } catch (\Exception $exception) {
           // return $exception;
            DB::rollback();
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }



    public function destroy($id){

        try {
            $vendor = Vendor::find($id);
            if (!$vendor) {
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوف']);
            }

            //delete image from folder assets/images/vendo
            $image = Str::after($vendor->logo,'assets/');     // assets/   after assets in path
            $image = base_path('assets/'.$image);
            unlink($image);  //method for delete

            $vendor ->delete();
            return redirect()->route('admin.vendors')->with(['success' => 'تم حذف المتجر بنجاح']);


        }catch (\Exception $ex){
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }




    public function changeStatus($id)
    {

        try {

            $vendors = Vendor::find($id);
            if (!$vendors)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود']);


            $status = $vendors->active == 0 ? 1 : 0;
            $vendors->update(['active' => $status]);
            return redirect()->route('admin.vendors')->with(['success' => 'تم تغيير الحاله بنجاح']);


        } catch (\Exception $exception) {
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);

        }
    }




}
