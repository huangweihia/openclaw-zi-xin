<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'sort',
    ];

    protected $casts = [
        'sort' => 'integer',
    ];

    /**
     * 文章关联
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * 项目关联
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
