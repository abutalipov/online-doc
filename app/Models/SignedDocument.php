<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignedDocument extends Model
{
    protected $fillable = [
        'user_id',
        'signature_key_id',
        'document_template_id',
        'path',
        'fields',
        'secret_key_id',
    ];
    public function getFieldsAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setFieldsAttribute($value)
    {
        $this->attributes['fields'] = json_encode(array_values($value));
    }
}
