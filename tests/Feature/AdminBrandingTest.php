<?php

declare(strict_types=1);

it('ログイン画面に本体と揃えたタイトル・ロゴ・favicon・フォントが出る', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSeeInOrder(['<title>', '神社めし呑み 社務所', '</title>'], escape: false)
        ->assertSee('#e9524e', escape: false)
        ->assertSee('favicon.svg')
        ->assertSee('fonts.googleapis.com/css2?family=Noto+Sans+JP', escape: false);
});
