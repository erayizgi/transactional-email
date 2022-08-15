<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Recipient
 * @implements Model
 * @property int $id
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @method static findOrFail(int $id)
 * @method static firstOrCreate(array $array, array $toArray): Recipient
 */
class Recipient extends Model
{
    use HasTimestamps, HasFactory;

    /**
     * Set the table name
     * @var string
     */
    protected $table = 'recipients';

    /**
     * Define the fillable columns
     * @var array<int, string>
     */
    protected $fillable = [
        "email",
        "first_name",
        "last_name"
    ];

}