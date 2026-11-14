<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// 公開ページは持たないので、トップは管理画面へ送る
Route::redirect('/', '/admin');
