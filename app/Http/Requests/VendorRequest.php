<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'logo'=>'required_without:id|mimes:jpg,jpeg,png',    //require in create only but in update not require
            'name'=>'required|string|max:100',
            'mobile'=>'required|max:100|unique:vendors,mobile,'.$this->id,   //unique:table name,column name   , .$this->id  because  it unique its name ignore
            'email'=>'required|email|unique:vendors,email,'.$this->id,
            'category_id'=>'required|exists:main_categories,id',   // exist in table main_categories
            'address'=>'required|string|max:500',
            'password'=>'required_without:id',
        ];
    }



    public function messages()
    {
        return [
            'required'=>'هذا الحقل مطلوب',
            'max'=>'هذا الحقل طويل',
            'category_id.exists'=>'القسم غير موجود',
            'email.email'=>'صيغه البريد الالكتروني غير صحيحه',
            'address.string'=>'العنوان لابد ان يكون حروف او حروف وارقام',
            'name.string'=>'الاسم لابد ان يكون حروف او حروف وارقام',
            'logo.required_without'=>'الصوره مطلوبه',
            'mobile.required'=>'رقم الهاتف مستخدم من قبل',
            'email.required'=>'البريد الالكتروني مستخدم من قبل',
            'password.min'=>'كلمه المرور لابد ان تكون علي الاقل 6 احرف او احرف و ارقام',
            'password.string'=>'كلمه المرور لابد ان تكون حروف او حروف وارقام',
        ];
    }
}
