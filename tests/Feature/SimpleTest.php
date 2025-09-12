<?php

test('simple test to verify pest is working', function () {
    expect(true)->toBeTrue();
});

test('database connection works', function () {
    $this->assertDatabaseEmpty('subscribers');
});
