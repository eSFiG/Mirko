<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'attachment';

    protected $fillable = [
        'original_name',
        'generated_name',
        'user_id',
    ];

    public function extension()
    {
        $data = explode('.', $this->original_name);
        return $data[1];
    }
}
