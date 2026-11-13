<?php

namespace Tests\Feature;

use App\Services\IndexNowService;
use Tests\TestCase;

class IndexNowKeyFileTest extends TestCase
{
    private const KEY_A = 'testkeyaaaa1111111111111111111111';

    private const KEY_B = 'testkeybbbb2222222222222222222222';

    protected function tearDown(): void
    {
        foreach ([self::KEY_A, self::KEY_B] as $k) {
            $file = public_path($k.'.txt');
            if (is_file($file)) {
                unlink($file);
            }
        }

        $state = storage_path('app/indexnow-published-key.txt');
        if (is_file($state)) {
            unlink($state);
        }

        parent::tearDown();
    }

    public function test_writes_key_file_when_key_is_configured(): void
    {
        config(['services.indexnow.key' => self::KEY_A]);

        app(IndexNowService::class)->publishKeyFile();

        $file = public_path(self::KEY_A.'.txt');
        $this->assertFileExists($file);
        $this->assertSame(self::KEY_A, file_get_contents($file));
    }

    public function test_removes_previous_key_file_on_rotation(): void
    {
        config(['services.indexnow.key' => self::KEY_A]);
        app(IndexNowService::class)->publishKeyFile();
        $this->assertFileExists(public_path(self::KEY_A.'.txt'));

        config(['services.indexnow.key' => self::KEY_B]);
        app(IndexNowService::class)->publishKeyFile();

        $this->assertFileDoesNotExist(public_path(self::KEY_A.'.txt'));
        $this->assertFileExists(public_path(self::KEY_B.'.txt'));
        $this->assertSame(self::KEY_B, file_get_contents(public_path(self::KEY_B.'.txt')));
    }

    public function test_removes_key_file_when_key_is_cleared(): void
    {
        config(['services.indexnow.key' => self::KEY_A]);
        app(IndexNowService::class)->publishKeyFile();
        $this->assertFileExists(public_path(self::KEY_A.'.txt'));

        config(['services.indexnow.key' => null]);
        app(IndexNowService::class)->publishKeyFile();

        $this->assertFileDoesNotExist(public_path(self::KEY_A.'.txt'));
    }

    public function test_does_nothing_when_key_never_configured(): void
    {
        config(['services.indexnow.key' => null]);

        app(IndexNowService::class)->publishKeyFile();

        $this->assertFileDoesNotExist(public_path(self::KEY_A.'.txt'));
        $this->assertFileDoesNotExist(public_path(self::KEY_B.'.txt'));
    }
}
