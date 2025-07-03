<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseLedger extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'name',
        'description',
        'invoice_number',
        'purchase_price',
        'seller',
        'purchase_date',
        'payment_method',
        'expense_type',
        'deduct'
    ];

    protected $searchableFields = ['*'];

    protected $table = 'expense_ledgers';

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
