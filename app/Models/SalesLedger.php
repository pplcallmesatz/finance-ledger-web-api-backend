<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesLedger extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'user_id',
        'total_product_price',
        'selling_product_price',
        'total_customer_price',
        'payment_status',
        'remarks',
        'sales_date',
        'company_address',
        'invoice_number',
        'payment_method',
        'customer_price'
    ];

    protected $searchableFields = ['*'];

    protected $table = 'sales_ledgers';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'ledger_product')
        ->withPivot('product_name', 'product_price', 'selling_price','quantity', 'customer_price','product_master_id');
    }

}
