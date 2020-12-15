<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'sub_categories';

    protected $fillable = [
        'translation_lang', 'translation_of','parent_id','name', 'slug','photo','active','created_at','updated_at',
    ];

    public function scopeActive($query){
        return $query->where('active',1);
    }


    public function scopeSelection($query){
        return $query-> select('id','parent_id','translation_lang','name','slug','photo','active','translation_of');
    }


    public function getActive(){
        return  $this->active == 1 ? 'مفعل' : 'غير مفعل';
    }


    public function scopeDeafultCategory($query){
        return $query->where('translation_of',0);
    }


    public function getPhotoAttribute($val){
        return ($val !== null) ? asset('assets/'.$val) : "";

    }

    //get mainCategory of subcategory
    public function mainCategory(){
        return $this->belongsTo('App\Models\MainCategory','category_id','id');
    }

}
