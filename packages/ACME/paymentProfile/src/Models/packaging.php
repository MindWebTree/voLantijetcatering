<?php

namespace ACME\paymentProfile\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\paymentProfile\Contracts\packaging as packagingContract;

class packaging extends Model implements packagingContract
{
    protected $table = 'packaging';
    protected $fillable = ['order_id','name','slip_sequence'];

    protected static function boot()
    {
        parent::boot();

        // Define a creating event listener
        static::creating(function ($packaging) {
            // Check if the slip_sequence column is null or empty
            if (empty($packaging->slip_sequence)) {
                // Fetch the maximum sequence value for the specific order_id
                $maxSequence = static::where('order_id', $packaging->order_id)->max('slip_sequence');
                // If no previous sequence exists, start from 1, otherwise increment the maximum sequence value by 1
                $newSequence = $maxSequence !== null ? $maxSequence + 1 : 1;
                // Assign the new sequence value to the packaging record being created
                $packaging->slip_sequence = $newSequence;
            }
        });
    }
}