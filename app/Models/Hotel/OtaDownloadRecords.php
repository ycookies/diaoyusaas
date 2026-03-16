<?php

namespace App\Models\Hotel;

use Dcat\Admin\Traits\HasDateTimeFormatter;

class OtaDownloadRecords extends HotelBaseModel
{
	use HasDateTimeFormatter;
    protected $table = 'ota_download_records';
    
    protected $guarded = [];
}
