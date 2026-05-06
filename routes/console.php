<?php

use App\Services\FifoStockService;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    app(FifoStockService::class)->markExpiredBatches();
})
    ->daily()
    ->name('mark-expired-batches')
    ->withoutOverlapping();
