<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPageVersion extends Model
{
    protected $fillable = [
        'cms_page_id',
        'version_number',
        'content',
        'created_by',
    ];

    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function cmsPage()
    {
        return $this->belongsTo(CmsPage::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
