<?php

declare(strict_types=1);
use Illuminate\Testing\TestResponse;

function requestFrom(string $ip): TestResponse
{
    return test()->withServerVariables(['REMOTE_ADDR' => $ip])->get('/admin/login');
}

describe('IP 制限が有効なとき', function () {
    beforeEach(function () {
        config([
            'admin.ip_restriction.enabled' => true,
            'admin.ip_restriction.allowed_ips' => ['192.0.2.10', '198.51.100.0/24', '2001:db8::/32'],
        ]);
    });

    it('許可した IP からはアクセスできる', function (string $ip) {
        requestFrom($ip)->assertOk();
    })->with([
        '完全一致' => '192.0.2.10',
        'CIDR の範囲内' => '198.51.100.200',
        'IPv6 の CIDR の範囲内' => '2001:db8::1',
    ]);

    it('許可していない IP には 403 を返す', function (string $ip) {
        requestFrom($ip)->assertForbidden();
    })->with([
        '別の IP' => '192.0.2.11',
        'CIDR の範囲外' => '198.51.101.1',
        '範囲外の IPv6' => '2001:db9::1',
    ]);

    it('ログイン画面以外のページも 403 にする', function () {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.1'])
            ->get('/')
            ->assertForbidden();
    });

    it('許可 IP が空なら全部拒否する', function () {
        config(['admin.ip_restriction.allowed_ips' => []]);

        requestFrom('192.0.2.10')->assertForbidden();
    });
});

it('IP 制限が無効なときはどの IP からもアクセスできる', function () {
    config(['admin.ip_restriction.enabled' => false]);

    requestFrom('203.0.113.1')->assertOk();
});
