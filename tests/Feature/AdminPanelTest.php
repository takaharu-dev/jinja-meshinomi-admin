<?php

declare(strict_types=1);

it('トップページは管理画面へリダイレクトする', function () {
    $this->get('/')->assertRedirect('/admin');
});

it('ログイン画面を表示できる', function () {
    $this->get('/admin/login')->assertOk();
});

it('未ログインで管理画面を開くとログイン画面へリダイレクトする', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});
