<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryMaster extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'description', 'symbol', 'self_life'];

    protected $searchableFields = ['*'];

    protected $table = 'category_masters';

    public function productMasters()
    {
        return $this->hasMany(ProductMaster::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
