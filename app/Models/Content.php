<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Content
 * @implements Model
 * @property integer $id
 * @property string $content
 * @property string $content_type
 * @property integer $recipient_id
 * @property string $provider
 * @method static findOrFail(int $id)
 */
class Content extends Model
{
    use HasTimestamps, HasFactory;

    const CONTENT_TYPES = [
        self::CONTENT_TYPE_TEXT,
        self::CONTENT_TYPE_HTML
    ];

    const CONTENT_TYPE_TEXT = "text/plain";
    const CONTENT_TYPE_HTML = "text/html";

    /**
     * Set table name
     * @var string
     */
    protected $table = 'contents';

    /**
     * Define the fillable columns
     * @var array<int, string>
     */
    protected $fillable = [
        'subject',
        'content',
        'content_type',
        'recipient_id',
        'provider'
    ];

    /**
     * Each content is special for recipient so content and recipient has one-to-one relationship
     * @return HasOne
     */
    public function recipient(): HasOne
    {
        return $this->hasOne(Recipient::class);
    }

}