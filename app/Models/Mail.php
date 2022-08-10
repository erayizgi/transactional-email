<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Mail
 * @property integer $id
 * @property integer $recipient_id
 * @property string $subject
 * @property integer $content_id
 * @property Recipient $recipient
 * @property Content $content
 * @property string $sent_at
 * @method static findOrFail(int $id)
 */
class Mail extends Model
{
    use HasTimestamps;

    /**
     * Set table name
     * @var string
     */
    protected $table = 'mails';

    /**
     * Define the fillable columns
     * @var array<int, string>
     */
    protected $fillable = [
        'recipient_id',
        'subject',
        'content_id',
        'sent_at'
    ];

    /**
     * Each mail record represents one mail sent
     * Each mail record can only have one recipient
     * @return HasOne
     */
    public function recipient(): HasOne
    {
        return $this->hasOne(Recipient::class);
    }

    /**
     * Each mail record can only contain one content so mail and content have one-to-one relationship
     * @return HasOne
     */
    public function content(): HasOne
    {
        return $this->hasOne(Content::class);
    }
}