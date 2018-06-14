<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class PdfDriver extends Migration
{
    public function up()
    {
        Setting::saveByKey('pdfDriver', 'domPDF');
    }

    public function down()
    {
        //
    }
}
