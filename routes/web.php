<?php

use App\Services\ExportCsvDataHandler;

Route::get('/test', function () {
    return (new ExportCsvDataHandler)->handle();
});
