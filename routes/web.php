<?php

use Illuminate\Support\Facades\Route;

// 公開ページは持たないので、トップは管理画面へ送る
Route::redirect('/', '/admin');
